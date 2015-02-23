<?php
/*
Plugin Name: Sunstone Podcast Importer
Version: 0.5
Description: Import tool to add posts from the Sunstone Symposium archives
Author: Jake Spurlock
Author URI: http://jakespurlock.com
Plugin URI: http://sunstonemagazine.org
*/
include_once( 'taxonomies/event.php' );
include_once( 'taxonomies/speaker.php' );

if ( defined( 'WP_CLI' ) && WP_CLI )
	include_once dirname( __FILE__ ) . '/classes/import.php';

/**
 * Sunstone Posts Importer
 */
class Sunstone_Posts_Importer {

	/**
	 * THE CONSTRUCT.
	 *
	 * All Hooks and Filter here.
	 * Anything else that needs to run when the class is instantiated, place them here.
	 * Maybe you'll get a cake if you do.
	 *
	 * @return  void
	 */
	public function __construct() {
	}

	/**
	 * Let's add all of our resouces to make our magic happen.
	 *
	 * @return  void
	 */
	public function load_resources( $hook ) {
	}

	/**
	 * Add all of the importer posts
	 * @return null
	 */
	public function install() {

		$posts = file_get_contents( dirname(__FILE__) . '/posts.js' );
		$obj = json_decode( $posts );
		$posts_arr = $obj->posts;

		foreach ( $posts_arr as $post ) {

			$post_content  = ( ! empty( $post->Description ) ) ? $post->Description : '';
			$post_content .= "\n\n";
			$post_content .= ( ! empty( $post->URL ) ) ? '[audio mp3="' . esc_url( $post->URL ) . '"][/audio]' : '';
			$post_content .=  "\n\n";
			$post_content .= ( ! empty( $post->Presenters ) ) ? $post->Presenters : '';

			WP_CLI::line( strtotime( $post->year ) );

			$post_arr = array(
				'post_title'	=> $post->Title,
				'post_status'	=> 'publish',
				'post_content'	=> $post_content,
				'post_date'		=> date( 'c', mktime( 0, 0, 0, 1, 1, $post->year ) ),
				'post_date_gmt' => date( 'c', mktime( 0, 0, 0, 1, 1, $post->year ) ),
			);

			// Add the post.
			$pid = wp_insert_post( $post_arr, true );
			WP_CLI::line( '____' );
			WP_CLI::line( '| Post: ' . $post->Title );

			// If it was added, let's start adding the terms.
			if ( ! is_wp_error( $pid ) ) {

				foreach ( $post->presenters as $presenter ) {
					// Let's set the presenter.
					$speaker = wp_set_object_terms( $pid, $presenter, 'speaker', true );
					if ( ! is_wp_error( $speaker  ) ) {
						if ( defined( 'WP_CLI' ) && WP_CLI ) {
							WP_CLI::line('| Speaker added: ' . $presenter );
						}
					}
				}

				$title = $post->event . ' Symposium ' . $post->year;

				$event = wp_set_object_terms( $pid, $title, 'event', true );
				if ( ! is_wp_error( $event ) ) {
					if ( defined( 'WP_CLI' ) && WP_CLI ) {
						WP_CLI::line('| Event added: ' . $post->event . ' Symposium ' . $post->year );
					}
				}

				$event = wp_set_object_terms( $pid, $title, 'event', true );
				if ( ! is_wp_error( $event ) ) {
					if ( defined( 'WP_CLI' ) && WP_CLI ) {
						WP_CLI::line('| Event added: ' . $post->event . ' Symposium ' . $post->year );
					}
				}

				$post_tag = wp_set_object_terms( $pid, 'Sunstone Talk', 'post_tag' );
				if ( ! is_wp_error( $post_tag ) ) {
					if ( defined( 'WP_CLI' ) && WP_CLI ) {
						WP_CLI::line('| Added the Sunstone Talk tag.' );
					}
				}

				$category = wp_set_object_terms( $pid, 'Podcasts', 'category' );
				if ( ! is_wp_error( $category ) ) {
					if ( defined( 'WP_CLI' ) && WP_CLI ) {
						WP_CLI::line('| Added the podcast category.' );
					}
				}

				// Add all of the other things as post meta. You know, for the kids.
				if( ! empty( $post->Audio ) )
					add_post_meta( $pid, 'audio', $post->Audio );
				if( ! empty( $post->URL ) )
					add_post_meta( $pid, 'archive_url', $post->URL );
				if( ! empty( $post->Presenters ) )
					add_post_meta( $pid, 'presenters', $post->Presenters );
				if( ! empty( $post->number ) )
					add_post_meta( $pid, 'event_number', $post->number );

			}
		}
	}

}
$wired_curator = new Sunstone_Posts_Importer();