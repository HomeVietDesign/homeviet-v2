<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

if(!empty($atts['label']) || !empty($atts['categories'])) {
?>
<p class="fw-categories d-flex flex-wrap">
    <?php
    if(!empty($atts['label'])) {
        ?>
        <span class="fw-categories-label me-2"><?=esc_html($atts['label'])?></span>
        <?php
    }
    if(!empty($atts['categories'])) {
        $i = 0;
        foreach ($atts['categories'] as $key => $value) {
            $cat = get_term_by('term_id', $value, 'category');
            if($cat) {
                if($i>0) {
                    ?>
                    <span class="fw-categories-sep mx-2" style="color:#0f0;"><?=esc_html($atts['sep'])?></span>
                    <?php
                }
                ?>
                <a class="d-flex align-items-center justify-content-center" href="<?php echo esc_url(get_category_link( $value )); ?>"><?php echo esc_html( get_term_field( 'name', $value, 'category' ) ); ?></a>
                <?php
                $i++;
            }
        }
        
    }
    ?>
</p>
<?php
}
