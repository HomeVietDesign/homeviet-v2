<?php
/**
 * Facebook Pixel Plugin FacebookWPCF7 class.
 *
 * This file contains the main logic for FacebookWPCF7.
 *
 * @package FacebookPixelPlugin
 */

/**
 * Define FacebookWPCF7 class.
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
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;

/**
 * FacebookWPCF7 class.
 */
class FacebookWPCF7 extends FacebookWordpressIntegrationBase {
    const PLUGIN_FILE   = 'contact-form-7/wp-contact-form-7.php';
    const TRACKING_NAME = 'contact-form-7';

    public static function track( $order_data ) {
        
        $return = [
            'event_data' => [],
            'fb_pxl_code' => '',
        ];

        $server_event = ServerEventFactory::safe_create_event(
            'AddToCart',
            array( __CLASS__, 'readFormData' ),
            array( $order_data ),
            self::TRACKING_NAME,
            true
        );

        //debug_log($server_event);

        FacebookServerSideEvent::get_instance()->track( $server_event );

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

        $return['event_data'] = $event_data;

        add_action( 'wpcf7_feedback_response', [__CLASS__, 'inject_lead_event'], 20, 2 );

        return $return;
    }

    /**
     * Injects the Pixel code into the Contact Form 7 response.
     *
     * Hooks into the `wpcf7_feedback_response` action and checks if the form
     * is submitted successfully and if the user is not an internal user.
     * If conditions are met, it renders the Pixel code using the `PixelRenderer` class
     * and appends the code to the form response.
     *
     * @param array $response The Contact Form 7 response.
     * @param array $result   The form data.
     *
     * @return array The modified Contact Form 7 response.
     */
    public static function inject_lead_event( $response, $result ) {
        
        $events = FacebookServerSideEvent::get_instance()->get_tracked_events();
        if ( count( $events ) === 0 ) {
            return $response;
        }

        $event_id  = $events[0]->getEventId();
        $fbq_calls = PixelRenderer::render(
            $events,
            self::TRACKING_NAME,
            false
        );
        $code      = sprintf(
            "
            if( typeof window.pixelLastGeneratedLeadEvent === 'undefined'
            || window.pixelLastGeneratedLeadEvent != '%s' ){
             window.pixelLastGeneratedLeadEvent = '%s';
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

    /**
     * Reads the form data from the Contact Form 7 submission.
     *
     * @param object $form The Contact Form 7 form object.
     *
     * @return array The form data in the format expected
     * by the `FacebookServerSideEvent` class.
     */
    public static function readFormData( $order_data ) {
        return array(
            'phone'      => isset( $order_data['phone'] )?$order_data['phone']:'',
        );
    }

}