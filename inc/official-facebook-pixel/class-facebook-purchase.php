<?php
/**
 * Facebook Pixel Plugin FacebookPurchase class.
 *
 * This file contains the main logic for FacebookPurchase.
 *
 * @package FacebookPixelPlugin
 */

/**
 * Define FacebookPurchase class.
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
 * FacebookPurchase class.
 */
class FacebookPurchase extends FacebookWordpressIntegrationBase {
    const TRACKING_NAME = 'purchase';

    /**
     * Add hooks to inject the Contact Form 7 tracking code.
     *
     * Adds the following hooks:
     *  - order_submit: Triggers a server-side event when the form is submitted.
     *  - wp_footer: Injects the mail sent listener.
     */
    public static function inject_pixel_code() {

        add_action( 'purchase', [__CLASS__, 'trackPurchaseEvent'] );
        
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
                //->setEventTime( $event_data['event_time'] )
                ->setEventTime( time() )
                ->setEventId( EventIdGenerator::guidv4() )
            ->setEventSourceUrl(
                $event_data['event_source_url']
            )
                ->setActionSource( 'website' )
                ->setUserData( $user_data )
                ->setCustomData( $custom_data );
        try {
            FacebookServerSideEvent::send([$event]);
            $datetime = get_the_date( 'Y-m-d H:i:s', $data['id'] );

           // debug_log($datetime);

            wp_update_post([
                'ID' => $data['id'],
                'post_status' => 'publish',
                'edit_date' => $datetime
            ]);

            update_post_meta( $data['id'], '_purchase', 1 );

        } catch( \Exception $e ) {
            throw $e;
        }

    }

}
FacebookPurchase::inject_pixel_code();