<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	'label' => array(
		'label' => 'Nhãn mở đầu',
		'desc' => '',
		'type' => 'text',
	),
	'categories' => [
		'type'  => 'multi-select',
	    'value' => array(),
	    'label' => 'Các chuyên mục',
	    /**
	     * Set population method
	     * Are available: 'posts', 'taxonomy', 'users', 'array'
	     */
	    'population' => 'taxonomy',
	    /**
	     * Set post types, taxonomies, user roles to search for
	     *
	     * 'population' => 'posts'
	     * 'source' => 'page',
	     *
	     * 'population' => 'taxonomy'
	     * 'source' => 'category',
	     *
	     * 'population' => 'users'
	     * 'source' => array( 'editor', 'subscriber', 'author' ),
	     *
	     * 'population' => 'array'
	     * 'source' => '' // will populate with 'choices' array
	     */
	    'source' => 'category',
	    /**
	     * Set the number of posts/users/taxonomies that multi-select will be prepopulated
	     * Or set the value to false in order to disable this functionality.
	     */
	    'prepopulate' => 10,
	    /**
	     * Set maximum items number that can be selected
	     */
	    'limit' => 100,
	],
	'sep' => array(
		'label' => 'Phân tách',
		'desc' => 'Ký tự phân tách giữa các chuyên mục',
		'type' => 'text',
	),
);
