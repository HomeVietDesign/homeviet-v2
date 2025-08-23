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

    public static function track($events) {
        //debug_log($data);

        if(isset($events['event_data']['fb']) && !empty($events['event_data']['fb'])) {
            
            $user_data = ( new UserData() )
                        ->setClientIpAddress( $events['order_data']['ip_address'] )
                        ->setClientUserAgent($events['order_data']['user_agent'] )
                        ->setPhones( $events['event_data']['fb']['ph'] )
                        ->setEmails( $events['event_data']['fb']['em'] )
                        ->setFbp( $events['event_data']['fb']['fbp'] )
                        ->setFbc( $events['event_data']['fb']['fbc'] );

            $custom_data = (new CustomData())
                        ->setValue(0.00)
                        ->setCurrency('VND');

            $event = ( new Event() )
                    ->setEventName( 'Purchase' )
                    ->setEventTime( time() )
                    ->setEventId( EventIdGenerator::guidv4() )
                    ->setEventSourceUrl(
                        $events['event_data']['fb']['event_source_url']
                    )
                    ->setActionSource( 'website' )
                    ->setUserData( $user_data )
                    ->setCustomData( $custom_data );

            //debug_log($event);
            
            FacebookServerSideEvent::send([$event]);

        }
    }

}
