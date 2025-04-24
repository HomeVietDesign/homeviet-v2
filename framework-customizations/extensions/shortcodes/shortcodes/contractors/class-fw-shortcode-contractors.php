<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Contractors extends FW_Shortcode
{
	
	public function _init()
	{

        add_action('wp_ajax_contractors_paginate', [$this, 'ajax_contractors_paginate']);
        add_action('wp_ajax_nopriv_contractors_paginate', [$this, 'ajax_contractors_paginate']);

        add_action('wp_footer', [$this, 'html_modals']);

        add_action('wp_ajax_toggle_best', [$this, 'ajax_toggle_best']);
        add_action('wp_ajax_contractor_arrange', [$this, 'ajax_contractor_arrange']);

        add_action('wp_ajax_edit_external_url_update', [$this, 'ajax_edit_external_url_update']);
        add_action('wp_ajax_change_provinces', [$this, 'ajax_change_provinces']);
	}

    public function ajax_change_provinces() {
         global $view, $current_province;

        $response = [
            'code' => false,
            'terms' => ''
        ];

        $id = isset($_POST['id'])?absint($_POST['id']):0;
        $provinces = isset($_POST['provinces'])?$_POST['provinces']:[];
        if(!empty($provinces)) $provinces = array_map('absint', $provinces);

         $response['terms'] = $provinces;
         
        if($id && check_ajax_referer( 'change-province-'.$id, 'nonce', false )) {

            $terms = wp_set_object_terms( $id, $provinces, 'province', false );
            $response['code'] = (is_array($terms) && !empty($terms))?true:false;
            $response['terms'] = $terms;

            wp_cache_delete( $id, 'posts' );
            
            // if($current_province) {
            //     clean_term_cache( $current_province->term_id, $current_province->taxonomy );
            // }
            
            $url = home_url( $_POST['uri']?$_POST['uri']:'' );
            $view_url = get_permalink( $view );
            wp_remote_request($url, ['method'=>'PURGE']);
            if($url != $view_url) {
                wp_remote_request($view_url, ['method'=>'PURGE']);
            }
        }

        wp_send_json($response);
    }

    public function ajax_edit_external_url_update() {
        global $view;

        $response = [
            'code' => false,
            'data' => ''
        ];
        $id = isset($_POST['id'])?absint($_POST['id']):0;
        $external_url = isset($_POST['external_url'])?sanitize_url($_POST['external_url']):'';
        $response['data'] = $external_url;
        
        if($id && check_ajax_referer( 'edit-external-url-'.$id, 'nonce', false )) {
            
            update_post_meta( $id, '_external_url', $external_url );
            wp_cache_delete( $id, 'posts' );
            $response['code'] = true;
        }

        wp_send_json( $response );
    }

    public function ajax_contractor_arrange() {
        global $wpdb, $view;
        $response = [
            'code' => false,
            'arrange' => '',
        ];
        $id = isset($_POST['id'])?absint($_POST['id']):0;
        $arrange = isset($_POST['arrange'])?$_POST['arrange']:'';
        $response['arrange'] = $arrange;
   
        if($id && check_ajax_referer( 'action-'.$id, 'nonce', false )) {
            $args = [
                'post_type' => 'contractor',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                //'fields' => 'ids',
                'orderby' => 'date',
            ];

            switch ($arrange) {
                case 'up':
                    $args['order'] = 'DESC';
                    $contractors = get_posts($args);
                    $max = ($contractors)?$contractors[0]->post_date:current_time('mysql');
                    $max_inc = date('Y-m-d H:i:s', strtotime($max.' +1 minute'));
                    $wpdb->update( $wpdb->posts, ['post_date' => $max_inc], ['ID' => $id] );
                    update_post_meta( $id, '_is_down', '' );
                    wp_cache_delete( $id, 'posts' );

                    $response['code'] = true;
                    break;
                
                case 'down':
                    $args['order'] = 'ASC';
                    $contractors = get_posts($args);
                    $min = ($contractors)?$contractors[0]->post_date:current_time('mysql');
                    $min_dec = date('Y-m-d H:i:s', strtotime($min.' -1 minute'));
                    $wpdb->update( $wpdb->posts, ['post_date' => $min_dec], ['ID' => $id] );
                    update_post_meta( $id, '_is_down', '1' );
                    wp_cache_delete( $id, 'posts' );

                    $response['code'] = true;
                    break;
            }

            // litespeed purge cache with url
            // need RewriteCond %{REQUEST_METHOD} ^HEAD|GET|PURGE$ in .htaccess
            $url = home_url( $_POST['uri']?$_POST['uri']:'' );
            $view_url = get_permalink( $view );
            wp_remote_request($url, ['method'=>'PURGE']);
            if($url != $view_url) {
                wp_remote_request($view_url, ['method'=>'PURGE']);
            }
            
        }

        wp_send_json( $response );
    }

    public function ajax_toggle_best() {
        global $view;

        $response = [
            'code' => false,
            'best' => ''
        ];
        $id = isset($_POST['id'])?absint($_POST['id']):0;
        $best = isset($_POST['best'])?$_POST['best']:'false';
        $response['best'] = $best;
        //debug_log($best);
        if($id && check_ajax_referer( 'toggle-best-'.$id, 'nonce', false )) {
            //debug_log($best);
            update_post_meta( $id, '_best', $best );
            wp_cache_delete( $id, 'posts' );
            $response['code'] = true;

            $url = home_url( $_POST['uri']?$_POST['uri']:'' );
            $view_url = get_permalink( $view );
            wp_remote_request($url, ['method'=>'PURGE']);
            if($url != $view_url) {
                wp_remote_request($view_url, ['method'=>'PURGE']);
            }
        }

        wp_send_json( $response );
    }

    public function ajax_contractors_paginate() {
        global $current_password, $current_province, $view;

        $response = [
            'items' => '',
            'paginate_links' => ''
        ];

        $paged = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 1;
        $args = isset($_REQUEST['query']) ? $_REQUEST['query'] : [];
        debug_log($args);
        $allow_query = false;

        if( !is_user_logged_in() ) {
            if($current_password) {
                $allow_query = true;
            }
        } elseif(has_role('administrator')) {
            $allow_query = true;
        }

        if($allow_query) {

            $args['paged'] = $paged;

    		$query = new \WP_Query($args);

            //debug_log($query);

            if($query->have_posts()) {
                
                ob_start();
                    self::loop_contractors($query);
                $response['items'] = ob_get_clean();

               $response['paginate_links'] = self::pagination($query, 3, 2);

            }
        }
        wp_send_json($response);
        
        die;
    }

    public static function contractors($query) {

        //debug($query);

        if($query->have_posts()) {
            
            ?>
            <div class="list-contractors row justify-content-center">
                <?php self::loop_contractors($query); ?>
            </div>
            <?php
            
        }
    }

    public static function pagination($wp_query, $end_size=3, $mid_size=2) {
    
        // Get max pages and current page out of the current query, if available.
        $total   = (int)$wp_query->max_num_pages;
        $current = ($wp_query->query_vars['paged']==0)?1:(int)$wp_query->query_vars['paged'];

        // Who knows what else people pass in $args.
        if ( $total < 2 ) {
            return;
        }
        
        if ( $end_size < 1 ) {
            $end_size = 1;
        }
        
        if ( $mid_size < 0 ) {
            $mid_size = 2;
        }

        $r          = '';
        $page_links = array();
        $dots       = false;

        if ( $current && 1 < $current ): 
            $page_links[] = '<button class="prev page-numbers" data-paged="'.($current - 1).'" type="button"><span class="dashicons dashicons-arrow-left"></span></button>';
        endif;

        for ( $n = 1; $n <= $total; $n++ ) :
            if ( $n == $current ) :
                $page_links[] = '<span class="page-numbers current">'.$n.'</span>';

                $dots = true;
            else :
                if ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) :
                    $page_links[] = '<button class="page-numbers" data-paged="'.$n.'" type="button">'.$n.'</button>';

                    $dots = true;
                elseif ( $dots ) :
                    //$page_links[] = '<span class="page-numbers dots">&hellip;</span>';
                    if($total>2*$end_size+1) {
                        $page_links[] = '<span class="page-numbers dots">&hellip;</span>';
                    } else {
                        $page_links[] = '<button class="page-numbers" data-paged="'.$n.'" type="button">'.$n.'</button>';
                    }
                    $dots = false;
                endif;
            endif;
        endfor;

        if ( $current && $current < $total ) :
            $page_links[] = '<button class="next page-numbers" data-paged="'.($current + 1).'" type="button"><span class="dashicons dashicons-arrow-right"></span></button>';
        endif;

        
        $r = implode( "\n", $page_links );

        return $r;
    }

    public static function loop_contractors($query) {
        while ($query->have_posts()) {
            $query->the_post();
            
            get_template_part( 'contractor', 'loop' );
        }
        wp_reset_postdata();
    }

    public function html_modals() {
       
        if(has_role('administrator')) {
            global $current_province;
            ?>
            <div class="modal fade" id="edit-external-url-modal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fs-5">Sửa liên kết nhà thầu</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form class="edit-external-url-form" method="post">
                                <input type="hidden" name="id" value="0">
                                <input type="hidden" name="nonce" value="">
                                <input type="text" name="external_url" class="form-control" value="">
                                <div class="pt-3 d-flex justify-content-end align-items-center">
                                    <div class="response-msg me-3"></div>
                                    <button type="button" class="copy-text edit-external-url-copy btn btn-sm btn-warning me-2" data-text="" data-bs-toggle="tooltip" data-bs-title="Sao chép" disabled>Chép link</button>
                                    <a href="" class="edit-external-url-open btn btn-sm btn-danger me-2 disabled" target="_blank">Mở link</a>
                                    <button type="submit" class="edit-external-url-update btn btn-sm btn-primary">Cập nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}
