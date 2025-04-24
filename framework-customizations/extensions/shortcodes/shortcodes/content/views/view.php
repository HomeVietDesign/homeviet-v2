<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(isset($atts['content_id'])) {
    $id = absint( $atts['content_id'][0] );
    $xpost = get_post( $id );
    if($xpost && $xpost->post_status=='publish') {
        $content = $xpost->post_content;
        if ( function_exists('fw_ext_page_builder_get_post_content') ) {
            $content = fw_ext_page_builder_get_post_content($xpost);
        }

        echo '<div class="content-builder">';
        echo wp_get_the_content( $content );
        echo '</div>';

    }
}
