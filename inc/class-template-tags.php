<?php
namespace HomeViet;

final class Template_Tags {

	public static function product_cost() {
		global $post;
		
		$_design_price = absint(get_post_meta($post->ID, '_design_price', true)); //giá thiết kế riêng
		$general_design_price = absint(get_option('product_design_price')); //giá thiết kế chung
		$_use_general_design_price = get_post_meta($post->ID, '_use_general_design_price', true); //dùng giá thiết kế chung?
		$design_price = ($_use_general_design_price=='yes') ? $general_design_price : $_design_price; //giá thiết kế cuối cùng
		
		$_price = absint(get_post_meta($post->ID, '_price', true)); //giá đầu tư
		$general_price = absint(get_option('product_price'));
		$_use_general_price = get_post_meta($post->ID, '_use_general_price', true);
		$price = ($_use_general_price=='yes') ? $general_price : $_price;

		$_area_1 = floatval(get_post_meta($post->ID, '_area_1', true));
		$_floors = floatval(get_post_meta($post->ID, '_floors', true));
		$area = $_area_1*$_floors;

		$_total_factor = floatval(get_post_meta($post->ID, '_total_factor', true));
		if($_total_factor==0) $_total_factor=1;

		$_total_amount = $price * $area * $_total_factor / 1000000; // tỷ

		if($_total_amount>0 || '' != $design_price):

		?>
		<div class="costs-info position-absolute end-0 bottom-0 py-1 px-2">
			<?php if($_total_amount>0): ?>
			<div class="total_amount text-end"><strong><?php echo esc_html(number_format($_total_amount,2,'.',',')); ?></strong> tỷ</div>
			<?php endif; ?>

			<?php if($design_price>0 && is_single() && $_show_general_design_price=='yes'): ?>
			<div class="product-design-fee d-flex text-yellow align-items-end">
				<span class="d-block me-1">Phí thiết kế:</span>
				<span class="d-block fs-5 fw-bold lh-sm"><?=$design_price?></span>
				<span class="d-block">k/m2</span>
			</div>
			<?php endif; ?>
		</div>
		<?php endif;
	}
	
	public static function pagination($query) {
		/*
		max_num_pages : number pages
		query_vars[paged] : current page
		*/
		//debug($query);

		$output = '';
		$paged = ($query->query_vars['paged']==0)?1:$query->query_vars['paged'];

		if($query->max_num_pages>1) {

			// hiển thị về đầu và về trang trước
			if($paged == 1) 
				$output = $output . '<span class="disabled mx-1 d-block p-1"><span class="dashicons dashicons-controls-skipback"></span></span><span class="disabled mx-1 d-block p-1"><span class="dashicons dashicons-controls-back"></span></span>';
			else	
				$output = $output . '<a class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="' . 1 . '" ><span class="dashicons dashicons-controls-skipback"></span></a><a class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="' . ($paged-1) . '" ><span class="dashicons dashicons-controls-back"></span></a>';
			
			// hiển thị link trang 1 khi trang > 3
			if(($paged-2)>0) {
				$output = $output . '<a class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="1" >1</a>';
			}

			// hiển thị ... khi trang > 4
			if(($paged-2)>1) {
					$output = $output . '...';
			}
			
			// hiển thị 2 trang phía trước và tiếp theo tại trang hiện tại
			for($i=($paged-1); $i<=($paged+1); $i++)	{
				if($i<1) continue;
				if($i>$query->max_num_pages) break;
				if($paged == $i)
					$output = $output . '<span class="current mx-1 d-block p-1">'.$i.'</span>';
				else				
					$output = $output . '<a class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="' . $i . '" >'.$i.'</a>';
			}
			
			// hiển thị ... khi tổng số trang còn lớn hơn số trang hiện tại + thêm 2 trang tiếp theo
			if(($query->max_num_pages-($paged+1))>1) {
				$output = $output . '...';
			}

			// hiển thị trang cuối
			if(($query->max_num_pages-($paged+1))>0) {
				if($paged == $query->max_num_pages)
					$output = $output . '<span class="current mx-1 d-block p-1">' . $query->max_num_pages .'</span>';
				else				
					$output = $output . '<a class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="' . $query->max_num_pages .'" >' . $query->max_num_pages .'</a>';
			}
			
			// hiển thị tiếp theo và cuối cùng
			if($paged < $query->max_num_pages)
				$output = $output . '<a  class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="' . ($paged+1) . '" ><span class="dashicons dashicons-controls-forward"></span></a><a  class="link mx-1 d-block p-1" href="javascript:void(0);" data-paged="' . $query->max_num_pages . '" ><span class="dashicons dashicons-controls-skipforward"></span></a>';
			else				
				$output = $output . '<span class="disabled mx-1 d-block p-1"><span class="dashicons dashicons-controls-forward"></span></span><span class="disabled mx-1 d-block p-1"><span class="dashicons dashicons-controls-skipforward"></span></span>';
			
			
		}


		return $output;
		
	}


	public static function related_posts() {
		$current_id = get_the_ID();
		$category_post = fw_get_db_post_option($current_id, 'category_post');
		$category = (!empty($category_post))?get_category($category_post[0]):false;
		if($category) {
			$numberposts = intval(fw_get_db_post_option($current_id, 'number_show_post', 30));

			$args = [
				'post_type' => 'post',
				'posts_per_page' => $numberposts,
				'cat' => $category->term_id,
				'post_status' => 'publish',
				'post__not_in' => [$current_id]
			];

			$query = new \WP_Query($args);

			//debug_log($query);
			?>
			<div class="related px-3">
			<?php
			self::category_posts($query);
			?>
			</div>
			<?php
		}
	}

	public static function category_posts($query) {
		//$category = get_category( $category );
		//debug($query->request);
		if($query->have_posts()) {
		?>
		<div class="posts-masonry-section">
			<div class="posts-masonry-container container-xxl <?php if(!is_archive()) { ?>p-0<?php } ?>">
				<div class="row posts-masonry align-items-start">
				<?php
				while ($query->have_posts()) {
					$query->the_post();
					get_template_part('post','loop');
				}
				?>
				</div>

				<?php

				if($query->max_num_pages>1) {
					$exclude = 0;
					if(is_single()) {
						$exclude = get_the_ID();
					}

					$cat_exclude = isset($query->query_vars['category__not_in']) ? $query->query_vars['category__not_in'] : [];
					$location = isset($query->query_vars['tax_query']['location']) ? absint($query->query_vars['tax_query']['location']['terms']) : 0;
				?>
				<div class="posts-masonry-loadmore-wrap">
					<div class="load-bar"><div class="loaded-bar" style="width: <?=(100/$query->max_num_pages)?>%;"><div class="loaded-num"><div><span class="loaded-page">1</span>/<?=$query->max_num_pages?></div></div></div></div>
					<button class="posts-masonry-loadmore-button" type="button" data-cat="<?php echo absint($query->query_vars['cat']); ?>" data-location="<?=$location?>" data-catexc="<?=esc_attr(json_encode($cat_exclude))?>" data-page="1" data-pages="<?=$query->max_num_pages?>" data-per="<?=intval($query->query_vars['posts_per_page'])?>" data-exclude="<?=$exclude?>">Xem thêm</button>
				</div>
				<?php
				
				} // if pagination

				?>

			</div>
		</div>
		<?php
		} // $query->have_posts();
		wp_reset_postdata();
	}

	public static function folder_path($folder) {
		static $paths;
		$paths[] = $folder;
		if($folder->parent==0) {
			return $paths;
		} else {
			return self::folder_path(get_term_by( 'id', $folder->parent, 'media_folder' ));
		}
	}


	public static function folder_breadcrumbs($current_folder) {
		$root = get_posts( [
			'post_type'  => 'page',
			//'fields'     => 'ids',
			'posts_per_page'   => 1,
			//'nopaging'   => true,
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'folder.php'
		] );

		$paths = \HomeViet\Template_Tags::folder_path($current_folder);

		$paths = array_reverse($paths);

		?>
		<div class="media-folder-breadcrumbs">
			<?php
			if($root) {
				echo '<a class="crumb root" href="'.esc_url(get_permalink($root[0])).'">'.esc_html($root[0]->post_title).'</a> / ';
			}

			foreach ($paths as $key => $value) {
				if($key>0) echo ' / ';
				echo '<a class="crumb" href="'.esc_url(get_term_link($value, 'media_folder')).'">'.esc_html($value->name).'</a>';
			}
			?>
		</div>
		<?php
	}

	public static function display_folders($folders) {
		if($folders) {
			$editing = false;

			$user = wp_get_current_user();

			if ( $user->exists() ) {
				if(in_array('administrator',$user->roles)) {
					$editing = true;
				}
			}

			//debug($folders);
			?>
			<div class="list-folders row">
				<?php
				foreach ($folders as $id => $name) {

					$thumbnail = fw_get_db_term_option($id, 'media_folder', 'thumbnail');
					//debug($thumbnail);
					?>
					<div class="folder col-md-6 col-lg-4">
						<div class="inner">
						<?php
						if($editing) {
							$edit_link = add_query_arg([
								'taxonomy' => 'media_folder',
								'tag_ID' => $id,
								'post_type' => 'attachment',
								'wp_http_referer' => $_SERVER['REQUEST_URI']
							], admin_url('term.php'));
							?>
							<a href="<?php echo $edit_link; ?>" class="edit" target="_blank"><span class="dashicons dashicons-edit"></span> Chỉnh sửa</a>
							<?php
						}
						?>
						<a href="<?php echo get_term_link( $id, 'media_folder' ); ?>">
							<span class="thumbnail<?php echo (!isset($thumbnail['attachment_id']))? ' no-image': ''; ?>">
							<?php
							if(isset($thumbnail['attachment_id'])) {
								echo wp_get_attachment_image($thumbnail['attachment_id'],'large', false);
							}
							?>
							</span>
							<span class="title"><?php echo esc_html($name); ?></span>
						</a>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
	
}