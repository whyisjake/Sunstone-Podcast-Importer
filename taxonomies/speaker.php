<?php

function speaker_init() {
	register_taxonomy( 'speaker', array( 'post' ), array(
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
			'name'                       => __( 'Speakers', 'sunstone-podcast-importer' ),
			'singular_name'              => _x( 'Speaker', 'taxonomy general name', 'sunstone-podcast-importer' ),
			'search_items'               => __( 'Search Speakers', 'sunstone-podcast-importer' ),
			'popular_items'              => __( 'Popular Speakers', 'sunstone-podcast-importer' ),
			'all_items'                  => __( 'All Speakers', 'sunstone-podcast-importer' ),
			'parent_item'                => __( 'Parent Speaker', 'sunstone-podcast-importer' ),
			'parent_item_colon'          => __( 'Parent Speaker:', 'sunstone-podcast-importer' ),
			'edit_item'                  => __( 'Edit Speaker', 'sunstone-podcast-importer' ),
			'update_item'                => __( 'Update Speaker', 'sunstone-podcast-importer' ),
			'add_new_item'               => __( 'New Speaker', 'sunstone-podcast-importer' ),
			'new_item_name'              => __( 'New Speaker', 'sunstone-podcast-importer' ),
			'separate_items_with_commas' => __( 'Speakers separated by comma', 'sunstone-podcast-importer' ),
			'add_or_remove_items'        => __( 'Add or remove Speakers', 'sunstone-podcast-importer' ),
			'choose_from_most_used'      => __( 'Choose from the most used Speakers', 'sunstone-podcast-importer' ),
			'menu_name'                  => __( 'Speakers', 'sunstone-podcast-importer' ),
		),
	) );

}
add_action( 'init', 'speaker_init' );
