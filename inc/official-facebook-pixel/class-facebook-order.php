<?php
/**
 * Facebook Pixel Plugin FacebookOrder class.
 *
 * This file contains the main logic for FacebookOrder.
 *
 * @package FacebookPixelPlugin
 */

/**
 * Define FacebookOrder class.
 *
 * @return void
 */

/*
* Copyright (C) 2017-present, Meta, Inc.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

namespace FacebookPixelPlugin\Integration;

defined( 'ABSPATH' ) || die( 'Direct access not allowed' );

use FacebookPixelPlugin\Core\FacebookPluginUtils;
use FacebookPixelPlugin\Core\FacebookServerSideEvent;
use FacebookPixelPlugin\Core\FacebookWordPressOptions;
use FacebookPixelPlugin\Core\ServerEventFactory;
use FacebookPixelPlugin\Core\PixelRenderer;
use FacebookPixelPlugin\Core\EventIdGenerator;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;
use FacebookAds\Object\ServerSide\CustomData;

// use FacebookAds\Api;
// use FacebookAds\Object\ServerSide\EventRequest;
// use FacebookAds\Exception\Exception;

/**
 * FacebookOrder class.
 */
class FacebookOrder extends FacebookWordpressIntegrationBase {
    const TRACKING_NAME = 'product-order';

    /**
     * Add hooks to inject the Contact Form 7 tracking code.
     *
     * Adds the following hooks:
     *  - order_submit: Triggers a server-side event when the form is submitted.
     *  - wp_footer: Injects the mail sent listener.
     */
    public static function inject_pixel_code() {

        add_filter( 'order_submit', array( __CLASS__, 'trackServerEvent' ) );

        add_action( 'purchase', [__CLASS__, 'trackPurchaseEvent'] );
        
        add_action(
            'wp_footer',
            array( __CLASS__, 'injectOrderProductListener' ),
            10
        );

        //add_filter('before_conversions_api_event_sent', [__CLASS__, 'before_conversions_api_event_sent']);
    }

    public static function before_conversions_api_event_sent($events) {
        debug_log($events);

        return $events;
    }

    public static function trackPurchaseEvent($data) {
        //debug_log($data);
        
        $event_data = get_post_meta($data['id'], '_event_data', true);
        $order_data = get_post_meta($data['id'], '_data', true);

        $user_data = ( new UserData() )
                    ->setClientIpAddress( $order_data['ip_address'] )
                    ->setClientUserAgent( $order_data['user_agent'] )
                    ->setPhones( $event_data['ph'] )
                    ->setEmails( $event_data['em'] )
                    ->setFbp( $event_data['fbp'] )
                    ->setFbc( $event_data['fbc'] );

        $custom_data = (new CustomData())
                    ->setValue(0.00)
                    ->setCurrency('VND');

        $event = ( new Event() )
                ->setEventName( 'Purchase' )
                ->setEventTime( $event_data['event_time'] )
                //->setEventTime( time() )
                ->setEventId( EventIdGenerator::guidv4() )
            ->setEventSourceUrl(
                $event_data['event_source_url']
            )
                ->setActionSource( 'website' )
                ->setUserData( $user_data )
                ->setCustomData( $custom_data );
        try {
            FacebookServerSideEvent::send([$event]);
            wp_update_post([
                'ID' => $data['id'],
                'post_status' => 'publish'
            ]);
            update_post_meta( $data['id'], '_purchase', 1 );

        } catch( \Exception $e ) {
            throw $e;
        }

    }

    /**
     * Injects a JavaScript listener for the 'orderProduct' event,
     * which is triggered when a form is submitted.
     *
     * The listener executes the Pixel code sent in the response
     * via the 'fb_pxl_code' key.
     *
     * @return void
     */
    public static function injectOrderProductListener() {
        ob_start();
    ?>
    <!-- Meta Pixel Event Code -->
    <script type='text/javascript'>
        document.addEventListener( 'orderProduct', function( event ) {
	        if( "fb_pxl_code" in event.detail){
                if(event.detail.fb_pxl_code!='') {
	               eval(event.detail.fb_pxl_code);
                }
	        }
        }, false );
    </script>
    <!-- End Meta Pixel Event Code -->
        <?php
        $listener_code = ob_get_clean();
        echo $listener_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function trackServerEvent( $response ) {
        $is_internal_user = FacebookPluginUtils::is_internal_user();
        
        //$is_internal_user = false;
        
        $submit_failed    = (1 !== $response['code']);
        if ( $is_internal_user || $submit_failed ) {
            return $response;
        }

        $server_event = ServerEventFactory::safe_create_event(
            //'Gửi số',
            'AddToCart',
            array( __CLASS__, 'readFormData' ),
            array( $response ),
            self::TRACKING_NAME,
            true
        );
        FacebookServerSideEvent::get_instance()->track( $server_event );

        $events = FacebookServerSideEvent::get_instance()->get_tracked_events();
        if ( count( $events ) === 0 ) {
            return $response;
        }

        $order_data = [
            'name' => $response['data']['name'],
            'url' => $server_event->getEventSourceUrl(),
            'referrer' => base64_decode($response['data']['ref']),
            'user_agent' => $server_event->getUserData()->getClientUserAgent(),
            'ip_address'=>$server_event->getUserData()->getClientIpAddress(),
            'image' => $response['data']['image'],
            'type' => $response['data']['type'],
            'id' => $response['data']['id'],
        ];

        $event_data = [
            'event_name'=>$server_event->getEventName(),
            'event_time'=>$server_event->getEventTime(),
            'event_source_url'=>$server_event->getEventSourceUrl(),
            'event_id'=>$server_event->getEventId(),
            'fbc'=>$server_event->getUserData()->getFbc(),
            'fbp'=>$server_event->getUserData()->getFbp(),
            'em'=>$server_event->getUserData()->getEmails(),
            'ph'=>$server_event->getUserData()->getPhones()
        ];

        if(function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('add_product_order', [['order_data'=>$order_data, 'event_data'=>$event_data]], 'order');
        }

        $event_id  = $events[0]->getEventId();
        $fbq_calls = PixelRenderer::render(
            $events,
            self::TRACKING_NAME,
            false
        );
        $code      = sprintf(
            "
    if( typeof window.pixelLastGeneratedOrderEvent === 'undefined'
    || window.pixelLastGeneratedOrderEvent != '%s' ){
    window.pixelLastGeneratedOrderEvent = '%s';
    %s
    }
        ",
            $event_id,
            $event_id,
            $fbq_calls
        );

        $response['fb_pxl_code'] = $code;

        return $response;
    }

    public static function readFormData( $response ) {
        if ( empty( $response['data'] ) ) {
            return array();
        }

        return array(
            // 'email'      => '',
            // 'first_name' => '',
            // 'last_name'  => '',
            'phone'      => $response['data']['phone'],
        );
    }

}
FacebookOrder::inject_pixel_code();