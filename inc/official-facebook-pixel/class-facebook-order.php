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

    public static function track( $order_data ) {
        
        $return = [
            'event_data' => [],
            'fb_pxl_code' => '',
        ];

        // $is_internal_user = FacebookPluginUtils::is_internal_user();
        
        // if ( $is_internal_user ) {
        //     return $return;
        // }

        $server_event = ServerEventFactory::safe_create_event(
            'AddToCart',
            array( __CLASS__, 'read_form_data' ),
            array( $order_data ),
            self::TRACKING_NAME,
            true
        );

       // debug_log($server_event);

        FacebookServerSideEvent::get_instance()->track( $server_event );

        $events = FacebookServerSideEvent::get_instance()->get_tracked_events();
        if ( count( $events ) === 0 ) {
            return $return;
        }

        //debug_log($events);

        $event_data = [
            'event_name'=>$server_event->getEventName(),
            'event_time'=>$server_event->getEventTime(),
            'event_source_url'=>$server_event->getEventSourceUrl(),
            'event_id'=>$server_event->getEventId(),
            'fbc'=>$server_event->getUserData()->getFbc(),
            'fbp'=>$server_event->getUserData()->getFbp(),
            'em'=>$server_event->getUserData()->getEmails(),
            'ph'=>$server_event->getUserData()->getPhones(),
        ];

        $return['event_data'] = $event_data;

        $event_id  = $events[0]->getEventId();
        $fbq_calls = PixelRenderer::render(
            $events,
            self::TRACKING_NAME,
            false
        );
        $code      = sprintf(
            "if( typeof window.pixelLastGeneratedOrderEvent === 'undefined'
                || window.pixelLastGeneratedOrderEvent != '%s' ){
                window.pixelLastGeneratedOrderEvent = '%s';
                %s
            }
            ",
            $event_id,
            $event_id,
            $fbq_calls
        );

        $return['fb_pxl_code'] = $code;

        return $return;
    }

    public static function read_form_data( $order_data ) {
        return array(
            'phone'      => $order_data['phone']
        );
    }

}
