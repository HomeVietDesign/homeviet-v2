<?php
define('THEME_DIR', get_stylesheet_directory());
define('THEME_URI', get_stylesheet_directory_uri());

if(class_exists('ActionScheduler')) {
	require_once THEME_DIR.'/inc/action-scheduler-hooks.php';
}

require_once THEME_DIR.'/inc/class-theme.php';

\HomeViet\Theme::instance();