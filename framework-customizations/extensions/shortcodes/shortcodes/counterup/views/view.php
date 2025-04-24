<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['items'])) {
	?>
	<div class="fw-shortcode-counterup">
		<div class="counterup d-flex flex-wrap justify-content-center align-items-center">
			<?php
			foreach ($atts['items'] as $key => $value) {
				?>
				<div class="item px-3 px-lg-4 py-2">
					<div class="inner d-flex justify-content-center align-items-center">
						<div class="number"><span class="counter"><?=number_format($value['numbers'],0,',','.')?></span>+</div><div class="title"><?=esc_html($value['title'])?></div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}

