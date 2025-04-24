<?php
global $post, $current_password, $current_password_province, $current_province, $view;

$best = get_post_meta($post->ID, '_best', true);
$phone_number = get_post_meta($post->ID, '_phone_number', true);
$external_url = get_post_meta($post->ID, '_external_url', true);

$view_id = $view?$view->ID:0;

$class = get_the_terms( $post, 'contractor_class' );

$index = 0;

if($class) {
    $index = absint(get_term_meta($class[0]->term_id, 'order', true));
}

if( !is_user_logged_in() && $current_password) {
    $default_province = (int) get_option( 'default_term_province', 0 );
    ?>
    <div class="contractor col-md-6 col-lg-3">
        <div class="inner d-flex flex-column">
            <div class="contractor-thumbnail position-relative">
                <div class="entry-thumbnail d-block <?php echo (!has_post_thumbnail( $post ))?'no-thumbnail bg-secondary-subtle':''; ?>">
                    <span class="d-block"><?php the_post_thumbnail('large', ['alt'=>esc_attr(get_the_title())]); ?></span>
                </div>

                <div class="position-absolute top-0 start-50 mt-1 translate-middle-x d-flex featured">
                    <?php if($best=='true') { ?>
                    <span class="d-block px-2 py-1 mx-1 bg-danger text-white rounded-0 fw-bold text-uppercase text-nowrap">Nhà thầu Uy tín</span>
                    <?php } ?>
                    <?php if($class) { ?>
                    <span class="d-block px-2 py-1 mx-1 <?=esc_attr($class[0]->description)?> rounded-0 fw-bold text-uppercase text-nowrap"><?php echo esc_html($class[0]->name); ?></span>
                    <?php } ?>
                </div>

                <?php if($phone_number!='' || $external_url!='') { ?>
                <div class="entry-contacts position-absolute start-0 bottom-0 mb-1 d-flex justify-content-center z-3 w-100">
                    <?php if($phone_number!='') { ?>
                        <div class="phone-number bg-danger text-white px-2 py-1 text-black m-1"><?=esc_html($phone_number)?></div>
                        <a class="btn btn-sm btn-danger rounded-0 m-1" href="<?=esc_url('tel:'.$phone_number)?>">Gọi</a>
                        <a class="btn btn-sm btn-primary rounded-0 m-1" href="<?=esc_url('https://zalo.me/'.$phone_number)?>" target="_blank">Zalo</a>
                    <?php } ?>
                    <?php if($external_url!='') { ?>
                        <a class="btn btn-sm btn-success rounded-0 m-1" href="<?=esc_url($external_url)?>" target="_blank">Xem</a>
                    <?php } ?>
                </div>
                <?php } ?>
                 
            </div>
            <?php
            if($index) {
                echo '<div class="order-number"><div class="d-flex justify-content-center pt-3"><span class="d-block fw-bold fs-3 text-yellow bg-black rounded-circle">'.$index.'</span></div></div>';
            }
            ?>
            <div class="contractor-summary">
                <h3 class="entry-title text-center text-uppercase py-3 px-1">
                    <?php the_title(); ?>
                </h3>
            </div>
            <?php
            // debug($current_password_province);
            // debug($default_province);
            if($current_password_province->term_id == $default_province) { ?>
            <div class="provinces px-2 pb-1 d-flex flex-wrap justify-content-center">
                <?php
                $provinces = get_the_terms( $post, 'province' );
                if($provinces) {
                    foreach ($provinces as $key => $value) {
                        ?>
                        <div class="mb-2 mx-1 px-1 border border-secondary text-secondary"><?=esc_html($value->name)?></div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php
} elseif (has_role('administrator')) {
    $has_term = false;
    
    $_is_down = get_post_meta($post->ID, '_is_down', true);

    $provinces = get_the_terms( $post, 'province' );
    //debug($provinces);
    ?>
    <div class="contractor col-md-6 col-lg-3">
        <div class="inner d-flex flex-column justify-content-between">
            <div class="contractor-thumbnail position-relative">
                 <div class="entry-thumbnail d-block <?php echo (!has_post_thumbnail( $post ))?'no-thumbnail bg-secondary-subtle':''; ?>">
                    <span class="d-block"><?php the_post_thumbnail('large', ['alt'=>esc_attr(get_the_title())]); ?></span>
                </div>
                <?php edit_post_link( '<span class="dashicons dashicons-edit"></span>','','',0,'post-edit-link bg-dark btn btn-sm btn-secondary position-absolute start-0 top-0 ms-1 mt-1 rounded-0' ); ?>
                <?php
                //debug($best);
                $nonce = wp_create_nonce( 'toggle-best-'.$post->ID );
                $html_id = uniqid('toggle-best-'.$post->ID.'-');
                ?>
                <input type="checkbox" class="btn-check toggle-best end-0 top-0" autocomplete="off" id="<?=esc_attr($html_id)?>" data-id="<?=$post->ID?>" data-nonce="<?=esc_attr($nonce)?>" <?php checked( $best, 'true', true ); ?> value="true">
                <label class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 rounded-0 mt-1 me-1 fw-bold z-3" for="<?=esc_attr($html_id)?>">TỐT</label>

                <div class="entry-contacts position-absolute end-0 bottom-0 mb-1 w-100 d-flex justify-content-center z-3">
                    <?php if($phone_number!='') { ?>
                        <div class="phone-number bg-danger text-white px-2 py-1 text-black m-1"><?=esc_html($phone_number)?></div>
                        <a class="btn btn-sm btn-danger rounded-0 m-1" href="<?=esc_url('tel:'.$phone_number)?>">Gọi</a>
                        <a class="btn btn-sm btn-primary rounded-0 m-1" href="<?=esc_url('https://zalo.me/'.$phone_number)?>" target="_blank">Zalo</a>
                    <?php } ?>
                    <?php if($external_url!='') { ?>
                        <a class="btn btn-sm btn-success rounded-0 m-1" href="<?=esc_url($external_url)?>" target="_blank">Xem</a>
                    <?php } ?>
                </div>

                <?php
                if($_is_down) {
                    ?>
                    <span class="btn btn-sm btn-warning position-absolute top-0 start-50 translate-middle-x rounded-0 mt-1 z-3" ><span class="dashicons dashicons-download"></span></span>
                    <?php
                }
                ?>
            </div>
            <div class="contractor-summary flex-grow-1">
                <h3 class="entry-title text-center text-uppercase">
                    <?php the_title(); ?>
                </h3>
                <?php
                if(''!=$post->post_excerpt) {
                ?>
                <div class="entry-excerpt">
                    <?php echo wp_format_content($post->post_excerpt); ?>
                </div>
                <?php
                }
                ?>
            </div>
            <div class="action-buttons d-flex justify-content-center mb-3 align-items-center">
                 <?php
                    $nonce = wp_create_nonce( 'action-'.$post->ID );
                    ?>
                <button type="button" class="contractor-arrange btn btn-sm btn-success rounded-0 m-1" data-arrange="up" data-id="<?=$post->ID?>" data-nonce="<?=esc_attr($nonce)?>"><span class="dashicons dashicons-arrow-up-alt"></span></button>
                <button type="button" class="contractor-arrange btn btn-sm btn-warning rounded-0 m-1" data-arrange="down" data-id="<?=$post->ID?>" data-nonce="<?=esc_attr($nonce)?>"><span class="dashicons dashicons-arrow-down-alt"></span></button>
            </div>
            <?php
            $nonce = wp_create_nonce( 'change-province-'.$post->ID );
            ?>
            <div class="change-province-wrap p-2 w-100 border border-dark">
                <select class="change-province select2-hidden-accessible" multiple="multiple" data-id="<?=absint($post->ID)?>" data-nonce="<?=esc_attr($nonce)?>">
                <?php
                if($provinces) {
                    foreach ($provinces as $key => $value) {
                        ?>
                        <option value="<?=absint($value->term_id)?>" selected="selected"><?=esc_html($value->name)?></option>
                        <?php
                    }
                }
                ?>
                </select>
            </div>
        </div>
    </div>
    <?php
}