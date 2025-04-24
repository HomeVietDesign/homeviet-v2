<?php
get_header();

global $wp_query;

\HomeViet\Template_Tags::category_posts($wp_query);

get_footer();