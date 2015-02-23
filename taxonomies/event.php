<?php

function event_init() {
	register_taxonomy( 'event', array( 'post' ), array(
		'hierarchical'      => false,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => true,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'            => array(
			'name'                       => __( 'Events', 'sunstone-podcast-importer' ),
			'singular_name'              => _x( 'Event', 'taxonomy general name', 'sunstone-podcast-importer' ),
			'search_items'               => __( 'Search Events', 'sunstone-podcast-importer' ),
			'popular_items'              => __( 'Popular Events', 'sunstone-podcast-importer' ),
			'all_items'                  => __( 'All Events', 'sunstone-podcast-importer' ),
			'parent_item'                => __( 'Parent Event', 'sunstone-podcast-importer' ),
			'parent_item_colon'          => __( 'Parent Event:', 'sunstone-podcast-importer' ),
			'edit_item'                  => __( 'Edit Event', 'sunstone-podcast-importer' ),
			'update_item'                => __( 'Update Event', 'sunstone-podcast-importer' ),
			'add_new_item'               => __( 'New Event', 'sunstone-podcast-importer' ),
			'new_item_name'              => __( 'New Event', 'sunstone-podcast-importer' ),
			'separate_items_with_commas' => __( 'Events separated by comma', 'sunstone-podcast-importer' ),
			'add_or_remove_items'        => __( 'Add or remove Events', 'sunstone-podcast-importer' ),
			'choose_from_most_used'      => __( 'Choose from the most used Events', 'sunstone-podcast-importer' ),
			'menu_name'                  => __( 'Events', 'sunstone-podcast-importer' ),
		),
	) );

}
add_action( 'init', 'event_init' );
