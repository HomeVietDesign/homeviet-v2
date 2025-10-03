<?php
/*
 * Template Name: Panorama viewer
 *
 */
get_header();

while (have_posts()) {
	the_post();
	the_content();
}

get_footer();