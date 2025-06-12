<?php
/**
 * Facebook Pixel Plugin FacebookTimer class.
 *
 * This file contains the main logic for FacebookTimer.
 *
 * @package FacebookPixelPlugin
 */

/**
 * Define FacebookTimer class.
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
 * FacebookTimer class.
 */
class FacebookTimer extends FacebookWordpressIntegrationBase {
    const TRACKING_NAME = 'timer';

    public static function inject_pixel_code() {
		add_action( 'wp_footer', array( __CLASS__, 'injectClickSentListener' ),  );

		add_filter( 'track_timer', array( __CLASS__, 'trackTimer' ) );
    }


    public static function injectClickSentListener() {
	    ?>
	    <!-- Meta Pixel Event Code -->
	    <script type='text/javascript'>
		    if (!sessionStorage.getItem('sent3MinEvent')) {
				setTimeout(function() {
					fetch('/wp-json/theme-api/timer_event', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json'
						},
						body: JSON.stringify({ event: '3min_view' })
					}).then(response => {
						if (!response.ok) {
							throw new Error('Server response was not OK');
						}
						return response.json(); // chuyển về JSON
					}).then(function(data){
						//console.log(data);
						eval(data.fb_pxl_code);
					});
					sessionStorage.setItem('sent3MinEvent', 'true');
				//}, 5000);
				}, 180000);
			}
	    </script>
	    <!-- End Meta Pixel Event Code -->
        <?php
    }

    public static function trackTimer($response) {
    	
        $is_internal_user = FacebookPluginUtils::is_internal_user();
        
        //$is_internal_user = false;
        
        if ( $is_internal_user ) {
            return $response;
        }

        $server_event = ServerEventFactory::safe_create_event(
            $response['event'],
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
        $event_id  = $events[0]->getEventId();
        $fbq_calls = PixelRenderer::render(
            $events,
            self::TRACKING_NAME,
            false
        );
        $code      = sprintf(
            "
    if( typeof window.pixelLastGeneratedEClickEvent === 'undefined'
    || window.pixelLastGeneratedEClickEvent != '%s' ){
    window.pixelLastGeneratedEClickEvent = '%s';
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

    public static function readFormData($response) {

    	return [];
    }

}
FacebookTimer::inject_pixel_code();