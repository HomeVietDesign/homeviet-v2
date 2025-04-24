<?php
/**
 * Facebook Pixel Plugin FacebookEClick class.
 *
 * This file contains the main logic for FacebookEClick.
 *
 * @package FacebookPixelPlugin
 */

/**
 * Define FacebookEClick class.
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
 * FacebookEClick class.
 */
class FacebookEClick extends FacebookWordpressIntegrationBase {
    const TRACKING_NAME = 'eclick';

    public static function inject_pixel_code() {
		add_action( 'wp_footer', array( __CLASS__, 'injectClickSentListener' ),  );

		add_action( 'wp_ajax_track_eclick', [ __CLASS__, 'ajax_track_eclick'] );
		add_action( 'wp_ajax_nopriv_track_eclick', [ __CLASS__, 'ajax_track_eclick'] );

		add_filter( 'track_eclick', array( __CLASS__, 'trackEClick' ) );
    }

	public static function ajax_track_eclick() {
		$ename = $_POST['ename'];
		$response = ['ename' => $ename, 'fb_pxl_code' => ''];
		$response = apply_filters( 'track_eclick', $response );
		wp_send_json( $response );
	}

    public static function injectClickSentListener() {
        ob_start();
    ?>
    <!-- Meta Pixel Event Code -->
    <script type='text/javascript'>
    window.addEventListener('DOMContentLoaded', function(){
		jQuery(function($){
			$('a[href^="https://zalo.me/"]').on('click', function(event){
				$.ajax({
					url:theme.ajax_url+'?action=track_eclick',
					method:'POST',
					async:false,
					dataType: 'json',
					data: {ename: 'Nhắn zalo'},
					beforeSend:function(){
					},
					success:function(response){
						eval(response.fb_pxl_code);
					}
				});
			});

			$('a[href^="tel:"]').on('click', function(event){
				$.ajax({
					url:theme.ajax_url+'?action=track_eclick',
					method:'POST',
					async:false,
					dataType: 'json',
					data: {ename: 'Gọi điện'},
					beforeSend:function(){
					},
					success:function(response){
						eval(response.fb_pxl_code);
					}
				});
			});
		});
	});
    </script>
    <!-- End Meta Pixel Event Code -->
        <?php
        $listener_code = ob_get_clean();
        echo $listener_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function trackEClick($response) {
    	
        $is_internal_user = FacebookPluginUtils::is_internal_user();
        
        //$is_internal_user = false;
        
        if ( $is_internal_user ) {
            return $response;
        }

        $server_event = ServerEventFactory::safe_create_event(
            $response['ename'],
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
FacebookEClick::inject_pixel_code();