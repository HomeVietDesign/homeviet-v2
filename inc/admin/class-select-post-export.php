<?php
namespace HomeViet;

class Select_Post_Export {

	const VERSION = '1.2';

	private static $instance = null;

	private function __construct() {
        if(is_admin()) {
            add_action( 'admin_init', [$this, 'admin_init'] );
            //add_action( 'wp_ajax_post_image_import', [$this, 'ajax_post_image_import'] );
            add_filter( 'export_skip_postmeta', [$this, 'export_skip_postmeta'], 10, 2 );

            add_action( 'admin_notices', [$this, 'imported_images_notice'] );
        }
    }

    public function imported_images_notice() {
        if(isset($_GET['pii'])):
            if($_GET['pii']==1) {
                ?>
                <div class="notice notice-success is-dismissible"><p>Hình ảnh vừa được nhập.</p></div>
                <?php
            } else {
                ?>
                <div class="notice notice-error is-dismissible"><p>Xảy ra lỗi trong quá trình nhập ảnh.</p></div>
                <?php
            }
        
        endif;
    }

    public function export_skip_postmeta($skip, $meta_key) {
        /*
        if(preg_match('/^(rank_math|_yoast_|_oembed_|ao_post|rank_math_|amazonS3_|brizy)/', $meta_key)) {
            $skip = true;
        }
        */
        $meta_keys = [
            '_thumbnail_id',
            '_allow_save',
            '_allow_order',
            '_functions',
            '_location',
            '_breadth',
            '_length',
            '_area',
            '_total_amount',
            '_design_cost',
            '_show_general_design_cost',
            '_footer_content',
            '_featured',
            //'_get_premium',
            '_parts',
            '_images',
            '_sale_off',
            '_show_general_sale_off',
            // attachment
            '_wp_attached_file',
            '_wp_attachment_metadata',
            'fb_filesize',
        ];

        if($meta_key=='fw:opt:ext:pb:page-builder:json' || $meta_key=='fw_options' || in_array($meta_key, $meta_keys)) {
            $skip = false;
        } else {
            $skip = true;
        }

        return $skip;
    }

	public function admin_init() {
		add_filter( 'bulk_actions-edit-content_builder', [$this, 'bulk_post_actions'], 10, 1 );
        add_filter( 'bulk_actions-edit-seo_post', [$this, 'bulk_post_actions'], 10, 1 );
        add_filter( 'bulk_actions-edit-post', [$this, 'bulk_post_actions'], 10, 1 );
        add_filter( 'bulk_actions-edit-page', [$this, 'bulk_post_actions'], 10, 1 );
		add_filter( 'handle_bulk_actions-edit-content_builder', [$this, 'handle_bulk_post_action'], 10, 3);
        add_filter( 'handle_bulk_actions-edit-seo_post', [$this, 'handle_bulk_post_action'], 10, 3);
        add_filter( 'handle_bulk_actions-edit-post', [$this, 'handle_bulk_post_action'], 10, 3);
        add_filter( 'handle_bulk_actions-edit-page', [$this, 'handle_bulk_post_action'], 10, 3);

        add_filter( 'content_builder_row_actions', [$this, 'post_row_actions'], 10, 2 );
        add_filter( 'seo_post_row_actions', [$this, 'post_row_actions'], 10, 2 );
        add_filter( 'post_row_actions', [$this, 'post_row_actions'], 10, 2 );
        add_filter( 'page_row_actions', [$this, 'post_row_actions'], 10, 2 );
    }

    public function ajax_post_image_import() {
        $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
        $redirect = isset($_GET['re']) ? base64url_decode($_GET['re']) : '';
        if(check_ajax_referer( 'image_import_'.$post_id, '_wpnonce', false )) {

            $post_import = get_post($post_id);

            if($post_import) {
                
                $builder = get_post_meta($post_import->ID, 'fw:opt:ext:pb:page-builder:json', true);
                $builder = json_decode($builder, true);
                if(!empty($builder)) {
                    
                    foreach ($builder as $index => $element) {
                        if($element['type']=='section' && !empty($element['_items'])) {
                            foreach ($element['_items'] as $sub_index => $sub_element) {
                                if($sub_element['type']=='column' && !empty($sub_element['_items'])) {
                                    foreach ($sub_element['_items'] as $sub_sub_index => $sub_sub_element) {
                                        if ($sub_sub_element['type']=='simple') {
                                            $builder[$index]['_items'][$sub_index]['_items'][$sub_sub_index] = $this->post_images_import_process_element($sub_sub_element);
                                        }
                                    }
                                } elseif ($sub_element['type']=='simple') {
                                    $builder[$index]['_items'][$sub_index] = $this->post_images_import_process_element($sub_element);
                                }
                            }
                        } elseif ($element['type']=='column' && !empty($element['_items'])) {
                            foreach ($element['_items'] as $sub_index => $sub_element) {
                                if ($sub_element['type']=='simple') {
                                    $builder[$index]['_items'][$sub_index] = $this->post_images_import_process_element($sub_element);
                                }   
                            }
                        } elseif ($element['type']=='simple') {
                            $builder[$index] = $this->post_images_import_process_element($element);
                        }
                    }
                    //debug_log($builder);
                    update_post_meta( $post_import->ID, 'fw:opt:ext:pb:page-builder:json', esc_sql(json_encode($builder)) );

                }
                
                // $content = $this->post_images_import_process_content($post_import->post_content);
                // wp_update_post( ['ID' => $post_import->ID, 'post_content' => $content] );

                delete_post_meta( $post_import->ID, '_handle_import' );
            }

            $redirect = add_query_arg('pii', 1, $redirect);
        } else {
            $redirect = add_query_arg('pii', 0, $redirect);
        }

        wp_redirect($redirect);

        exit();
    }

    private function upload_attachment($url='', $post_id=0) {

        $url = esc_url($url);
        $attachmentId = 0;

        $url = unparse_url(parse_url($url));

        if( !empty( $url )  ) {

            if ( !function_exists('media_handle_sideload') ) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            }

            $file = array();
            $file['name'] = wp_basename(urldecode($url));
            $file['tmp_name'] = download_url($url);
            // debug_log($url);
            // debug_log($file);
            $attachment_title = sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) );
            $attachmentId = post_exists($attachment_title, '', '', 'attachment');
            
            if(!$attachmentId) {
                if (!is_wp_error($file['tmp_name'])) {
                    $attachmentId = media_handle_sideload($file, $post_id);

                    if ( is_wp_error($attachmentId) ) {
                        @unlink($file['tmp_name']);
                        return 0;
                    }
                }
            }
            
            
        }

        return absint($attachmentId);
    }

    public function post_images_import_process_content($content) {
        if($content!='') {
            $content_dom = str_get_html($content);
            foreach ($content_dom->find('img') as $content_img) {
                if(strpos($content_img->src, $_SERVER['HTTP_HOST'])===false && strpos($content_img->src, 'emoji')===false) {
                    //debug_log($content_img->class);
                    $size = (preg_match('/(?:.*)\ssize\-([^\s]+)(?:.*)/', $content_img->class, $matchs))?$matchs[1]:'full';
                    
                    $attachment_id = $this->upload_attachment($content_img->src);
                    if($attachment_id) {
                        $content_img->outertext = wp_get_attachment_image($attachment_id, $size, false, ['class'=>'alignnone size-'.$size.' wp-image-'.$attachment_id]);
                    }
                    
                }
            }
            $content = $content_dom.'';
        }

        return $content;
    }

    public function post_images_import_process_element($element) {
        
        switch($element['shortcode']) {
            case 'text_block':
                if($element['atts']['text']!='') {
                    $html = str_get_html($element['atts']['text']);

                    foreach ($html->find('img') as $content_img) {
                        if(strpos($content_img->src, $_SERVER['HTTP_HOST'])===false && strpos($content_img->src, 'emoji')===false) {
                            $size = (preg_match('/(?:.*)\ssize\-([^\s]+)(?:.*)/', $content_img->class, $matchs))?$matchs[1]:'full';
                            $attachment_id = $this->upload_attachment($content_img->src);
                            if($attachment_id) {
                                $content_img->outertext = wp_get_attachment_image($attachment_id, $size, false, ['class'=>'alignnone size-'.$size.' wp-image-'.$attachment_id]);
                            }
                        }
                    }

                    $element['atts']['text'] = $html.'';
                }
                break;
        }

        return $element;
    }

    public function post_row_actions($actions, $post) {
        // Check for your post type.
        if ( $post->post_type == "post" || $post->post_type == "page" || $post->post_type == "seo_post" || $post->post_type == "content_builder" ) {
            $is_handle_import = get_post_meta($post->ID, '_handle_import', true);

            // You can check if the current user has some custom rights.
            if ( current_user_can( 'edit_post', $post->ID ) && $is_handle_import==1 ) {

                // $trash = $actions['trash'];
                // unset($actions['trash']);

                $redirect = base64url_encode(fw_current_url());

                //debug_log($redirect);

                // Include a nonce in this link
                $import_link = wp_nonce_url( admin_url( 'admin-ajax.php?action=post_image_import&post=' . $post->ID.'&re='.$redirect ), 'image_import_'.$post->ID );

                // Add the new Copy quick link.
                $actions = array_merge( $actions, array(
                    'image_import' => sprintf( '<a href="%1$s">%2$s</a>',
                    esc_url( $import_link ), 
                    'Nhập hình ảnh'
                    ) 
                ) );

                // Re-insert thrash link preserved from the default $actions.
                // $actions['trash'] = $trash;
            }
        }

        return $actions;
    }

	protected function export_post_taxonomy($post) {


        $taxonomies = get_object_taxonomies($post->post_type);
        if (empty($taxonomies)) {
            return;
        }
        $terms = wp_get_object_terms($post->ID, $taxonomies);

        foreach ((array) $terms as $term) {
            echo "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->export_cdata($term->name) . "</category>\n";
        }
    }

    protected function export_site_url() {
        if (is_multisite()) {
            // Multisite: the base URL.
            return network_home_url();
        } else {
            // WordPress (single site): the blog URL.
            return get_bloginfo_rss('url');
        }
    }

    protected function do_authors(array $post_ids = []) {
        global $wpdb;

        if (!empty($post_ids)) {
            $post_ids = array_map('absint', $post_ids);
            $and      = 'AND ID IN ( ' . implode(', ', $post_ids) . ')';
        } else {
            $and = '';
        }

        $authors = array();
        $results = $wpdb->get_results("SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft' $and");
        foreach ((array) $results as $result) {
            $authors[] = get_userdata($result->post_author);
        }

        $authors = array_filter($authors);

        foreach ($authors as $author) {
            echo "\t<wp:author>";
            echo '<wp:author_id>' . (int) $author->ID . '</wp:author_id>';
            echo '<wp:author_login>' . $this->export_cdata($author->user_login) . '</wp:author_login>';
            echo '<wp:author_email>' . $this->export_cdata($author->user_email) . '</wp:author_email>';
            echo '<wp:author_display_name>' . $this->export_cdata($author->display_name) . '</wp:author_display_name>';
            echo '<wp:author_first_name>' . $this->export_cdata($author->first_name) . '</wp:author_first_name>';
            echo '<wp:author_last_name>' . $this->export_cdata($author->last_name) . '</wp:author_last_name>';
            echo "</wp:author>\n";
        }
    }

    protected function export_cdata($str) {
        if (!seems_utf8($str)) {
            $str = utf8_encode($str);
        }
        // $str = ent2ncr(esc_html($str));
        $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';

        return $str;
    }

    protected function make_file_name() {

        $date = gmdate('YmdHis');
        $wp_filename = $_SERVER['HTTP_HOST'] . '-export-' . $date . '.xml';

        $file_name = apply_filters('export_wp_filename', $wp_filename, $date);


        return ($file_name);
    }

    public function export_ids_process_element($element) {
        $export_ids = [];

        switch($element['shortcode']) {
            case 'grid_images':
            case 'carousel':
            case 'owlcarousel':
                if( !empty($element['atts']['images']) ) {
                    foreach ($element['atts']['images'] as $key => $value) {
                        $export_ids[] = $value['attachment_id'];
                    }
                } 
                break;

            case 'media_image':
                //debug_log($element);
                if( !empty($element['atts']['image']) ) {
                    $export_ids[] = $element['atts']['image']['attachment_id'];
                } 
                break;

            case 'media_video':
                //debug_log($element);
                if( !empty($element['atts']['video']) ) {
                    $export_ids[] = $element['atts']['video']['attachment_id'];
                }

                if( !empty($element['atts']['thumbnail']) ) {
                    $export_ids[] = $element['atts']['thumbnail']['attachment_id'];
                }

                break;

            case 'content':
                //debug_log($element);
                if( !empty($element['atts']['content_id']) ) {
                    $content_id = absint($element['atts']['content_id'][0]);
                    $export_ids[] = $content_id;
                    $export_ids = array_merge($export_ids, $this->related_export_ids($content_id));
                } 
                break;
        }

        return $export_ids;
    }

	public function do_export($post_ids) {
		global $wpdb;
        $filename = $this->make_file_name();
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
        echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
        ?>
        <rss version="2.0" xmlns:excerpt="http://wordpress.org/export/<?php echo self::VERSION; ?>/excerpt/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:wp="http://wordpress.org/export/<?php echo self::VERSION; ?>/">
            <channel>

                <title><?php bloginfo_rss('name'); ?></title>
                <link><?php bloginfo_rss('url'); ?></link>
                <description><?php bloginfo_rss('description'); ?></description>
                <pubDate><?php echo gmdate('D, d M Y H:i:s +0000'); ?></pubDate>
                <language><?php bloginfo_rss('language'); ?></language>
                <wp:wxr_version><?php echo self::VERSION; ?></wp:wxr_version>
                <wp:base_site_url><?php echo $this->export_site_url(); ?></wp:base_site_url>
                <wp:base_blog_url><?php bloginfo_rss('url'); ?></wp:base_blog_url>
                <?php
                $this->do_authors($post_ids);

                foreach ($post_ids as $p) {
                    $is_sticky = is_sticky($p) ? 1 : 0;
                    $post = get_post($p);
                    $title = $this->export_cdata(apply_filters('the_title_export', $post->post_title));
                    $content = $this->export_cdata(apply_filters('the_content_export', $post->post_content));
                    $excerpt = $this->export_cdata(apply_filters('the_excerpt_export', $post->post_excerpt));


                ?>
                    <item>
                        <title><?php echo $title; ?></title>
                        <link><?php the_permalink_rss(); ?></link>
                        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                        <dc:creator><?php echo $this->export_cdata(get_the_author_meta('login')); ?></dc:creator>
                        <guid isPermaLink="false"><?php the_guid(); ?></guid>
                        <description></description>
                        <content:encoded><?php echo $content; ?></content:encoded>
                        <excerpt:encoded><?php echo $excerpt; ?></excerpt:encoded>
                        <wp:post_id><?php echo (int) $post->ID; ?></wp:post_id>
                        <wp:post_date><?php echo $this->export_cdata($post->post_date); ?></wp:post_date>
                        <wp:post_date_gmt><?php echo $this->export_cdata($post->post_date_gmt); ?></wp:post_date_gmt>
                        <wp:post_modified><?php echo $this->export_cdata($post->post_modified); ?></wp:post_modified>
                        <wp:post_modified_gmt><?php echo $this->export_cdata($post->post_modified_gmt); ?></wp:post_modified_gmt>
                        <wp:comment_status><?php echo $this->export_cdata($post->comment_status); ?></wp:comment_status>
                        <wp:ping_status><?php echo $this->export_cdata($post->ping_status); ?></wp:ping_status>
                        <wp:post_name><?php echo $this->export_cdata($post->post_name); ?></wp:post_name>
                        <wp:status><?php echo $this->export_cdata($post->post_status); ?></wp:status>
                        <wp:post_parent><?php echo (int) $post->post_parent; ?></wp:post_parent>
                        <wp:menu_order><?php echo (int) $post->menu_order; ?></wp:menu_order>
                        <wp:post_type><?php echo $this->export_cdata($post->post_type); ?></wp:post_type>
                        <wp:post_password><?php echo $this->export_cdata($post->post_password); ?></wp:post_password>
                        <wp:is_sticky><?php echo (int) $is_sticky; ?></wp:is_sticky>
                        <?php if ('attachment' === $post->post_type) : ?>
                            <wp:attachment_url><?php echo $this->export_cdata(wp_get_attachment_url($post->ID)); ?></wp:attachment_url>
                        <?php endif; ?>
                        <?php //$this->export_post_taxonomy($post); ?>
                        <?php
                        $postmeta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID));
                        foreach ($postmeta as $meta) :
                            /**
                             * Filters whether to selectively skip post meta used for WXR exports.
                             *
                             * Returning a truthy value from the filter will skip the current meta
                             * object from being exported.
                             *
                             * @since 3.3.0
                             *
                             * @param bool   $skip     Whether to skip the current post meta. Default false.
                             * @param string $meta_key Current meta key.
                             * @param object $meta     Current meta object.
                             */
                            if (apply_filters('export_skip_postmeta', false, $meta->meta_key, $meta)) {
                                continue;
                            }
                        ?>
                            <wp:postmeta>
                                <wp:meta_key><?php echo $this->export_cdata($meta->meta_key); ?></wp:meta_key>
                                <wp:meta_value><?php echo $this->export_cdata($meta->meta_value); ?></wp:meta_value>
                            </wp:postmeta>
                        <?php
                        endforeach;

                        $_comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID));
                        $comments  = array_map('get_comment', $_comments);
                        foreach ($comments as $c) :
                        ?>
                            <wp:comment>
                                <wp:comment_id><?php echo (int) $c->comment_ID; ?></wp:comment_id>
                                <wp:comment_author><?php echo $this->export_cdata($c->comment_author); ?></wp:comment_author>
                                <wp:comment_author_email><?php echo $this->export_cdata($c->comment_author_email); ?></wp:comment_author_email>
                                <wp:comment_author_url><?php echo esc_url_raw($c->comment_author_url); ?></wp:comment_author_url>
                                <wp:comment_author_IP><?php echo $this->export_cdata($c->comment_author_IP); ?></wp:comment_author_IP>
                                <wp:comment_date><?php echo $this->export_cdata($c->comment_date); ?></wp:comment_date>
                                <wp:comment_date_gmt><?php echo $this->export_cdata($c->comment_date_gmt); ?></wp:comment_date_gmt>
                                <wp:comment_content><?php echo $this->export_cdata($c->comment_content); ?></wp:comment_content>
                                <wp:comment_approved><?php echo $this->export_cdata($c->comment_approved); ?></wp:comment_approved>
                                <wp:comment_type><?php echo $this->export_cdata($c->comment_type); ?></wp:comment_type>
                                <wp:comment_parent><?php echo (int) $c->comment_parent; ?></wp:comment_parent>
                                <wp:comment_user_id><?php echo (int) $c->user_id; ?></wp:comment_user_id>
                                <?php
                                $c_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID));
                                foreach ($c_meta as $meta) :
                                    /**
                                     * Filters whether to selectively skip comment meta used for WXR exports.
                                     *
                                     * Returning a truthy value from the filter will skip the current meta
                                     * object from being exported.
                                     *
                                     * @since 4.0.0
                                     *
                                     * @param bool   $skip     Whether to skip the current comment meta. Default false.
                                     * @param string $meta_key Current meta key.
                                     * @param object $meta     Current meta object.
                                     */
                                    if (apply_filters('export_skip_commentmeta', false, $meta->meta_key, $meta)) {
                                        continue;
                                    }
                                ?>
                                    <wp:commentmeta>
                                        <wp:meta_key><?php echo $this->export_cdata($meta->meta_key); ?></wp:meta_key>
                                        <wp:meta_value><?php echo $this->export_cdata($meta->meta_value); ?></wp:meta_value>
                                    </wp:commentmeta>
                                <?php endforeach; ?>
                            </wp:comment>
                        <?php endforeach; ?>
                    </item>
                <?php
                }
                ?>
            </channel>
        </rss>
        <?php
	}

    public function related_export_ids($post_id) {
        $post_import = get_post($post_id);
        $related_ids = [];

        if($post_import) {
            
            $builder = get_post_meta($post_import->ID, 'fw:opt:ext:pb:page-builder:json', true);
            $builder = json_decode($builder, true);
            if(!empty($builder)) {
                
                foreach ($builder as $index => $element) {
                    if($element['type']=='section' && !empty($element['_items'])) {
                        foreach ($element['_items'] as $sub_index => $sub_element) {
                            if($sub_element['type']=='column' && !empty($sub_element['_items'])) {
                                foreach ($sub_element['_items'] as $sub_sub_index => $sub_sub_element) {
                                    if ($sub_sub_element['type']=='simple') {
                                        $related_ids = array_merge($related_ids, $this->export_ids_process_element($sub_sub_element));
                                    }
                                }
                            } elseif ($sub_element['type']=='simple') {
                                $related_ids = array_merge($related_ids, $this->export_ids_process_element($sub_element));
                            }
                        }
                    } elseif ($element['type']=='column' && !empty($element['_items'])) {
                        foreach ($element['_items'] as $sub_index => $sub_element) {
                            if ($sub_element['type']=='simple') {
                                $related_ids = array_merge($related_ids, $this->export_ids_process_element($sub_element));
                            }   
                        }
                    } elseif ($element['type']=='simple') {
                        $related_ids = array_merge($related_ids, $this->export_ids_process_element($element));
                    }
                }

            }

            // meta _images
            $_images = get_post_meta( $post_import->ID, '_images', true );

            if($_images) {
                foreach ($_images as $key => $value) {
                   $related_ids[] = $value['attachment_id'];
                }
            }
        }

        return $related_ids;
    }

	public function handle_bulk_post_action($redirect_url, $action, $post_ids) {
        if($action=='post_export') {
        	$the_count = count($post_ids);

        	if ($the_count > 0) {
                global $wpdb;

                // Array to hold all additional IDs (attachments and thumbnails).
                $additional_ids = array();

                // Create a copy of the post IDs array to avoid modifying the original array.
                $processing_ids = $post_ids;

                while ( $next_posts = array_splice( $processing_ids, 0, 20 ) ) {
                    $posts_in     = array_map( 'absint', $next_posts );
                    $placeholders = array_fill( 0, count( $posts_in ), '%d' );

                    // Create a string for the placeholders.
                    $in_placeholder = implode( ',', $placeholders );

                    // Prepare the SQL statement for attachment ids.
                    $attachment_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "
                        SELECT ID
                        FROM $wpdb->posts
                        WHERE post_parent IN ($in_placeholder) AND post_type = 'attachment'
                            ",
                            $posts_in
                        )
                    );

                    $thumbnails_ids = $wpdb->get_col(
                        $wpdb->prepare(
                            "
                        SELECT meta_value
                        FROM $wpdb->postmeta
                        WHERE $wpdb->postmeta.post_id IN ($in_placeholder)
                        AND $wpdb->postmeta.meta_key = '_thumbnail_id'
                            ",
                            $posts_in
                        )
                    );

                    $additional_ids = array_merge( $additional_ids, $attachment_ids, $thumbnails_ids );
                }

                // Merge the additional IDs back with the original post IDs after processing all posts
                $_post_ids = array_unique( array_merge( $post_ids, $additional_ids ) );
                $export_ids = [];

                foreach ($post_ids as $post_id) {
                    $export_ids = array_merge($export_ids, $this->related_export_ids($post_id));
                }

                $_post_ids = array_unique( array_merge( $_post_ids, $export_ids ) );

                $this->do_export($_post_ids);

        		exit();
        	}
        }

		return $redirect_url;
	}

	public function bulk_post_actions($bulk_actions) {
		$bulk_actions['post_export'] = 'Xuất lựa chọn';
		return $bulk_actions;
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Select_Post_Export::instance();