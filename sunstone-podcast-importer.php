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
		add_action( 'admin_init', array( $this, 'manual_install' ) );
	}

	/**
	 * Let's add all of our resouces to make our magic happen.
	 *
	 * @return  void
	 */
	public function load_resources( $hook ) {
	}

	/**
	 * Bluehost has some bugs in WP-CLI and we need a manual method to imporrt posts.
	 * On admin_init we are going to kick off one process, and then after it is done,
	 * kill it, so that it doesn't go again.
	 */
	public function manual_install() {
		$default = false;
		$option = 'have_archive_posts_been_added_manually';
		$installed = get_option( $option, $default );

		if ( $installed )
			return;

		// Add the posts.
		$this->install();

		// Update the option
		update_option( $option, true );

	}


	/**
	 * Where do we have the most sessions?
	 * @return null
	 */
	public function events( $args, $assoc_args ) {

		$events = get_terms( 'event', array( 'orderby' => 'count', 'order' => 'DESC', ) );

		foreach ( $events as $event ) {
			$length = strlen( $event->name );
			$spaces = 50 - $length;
			WP_CLI::out( $event->name );
			for ( $i=0; $i < $spaces; $i++ ) {
				WP_CLI::out( " " );
			}
			WP_CLI::out( $event->count );
			WP_CLI::line();
		}

	}

	/**
	 * Where do we have the most sessions?
	 * @return null
	 */
	public function speakers( $args, $assoc_args ) {

		$speakers = get_terms( 'speaker', array( 'orderby' => 'count', 'order' => 'DESC', ) );

		foreach ( $speakers as $speaker ) {
			$length = strlen( $speaker->name );
			$spaces = 50 - $length;
			WP_CLI::out( $speaker->name );
			for ( $i=0; $i < $spaces; $i++ ) {
				WP_CLI::out( " " );
			}
			WP_CLI::out( $speaker->count );
			WP_CLI::line();
		}

	}


	/**
	 * Output popular words
	 * @return null
	 */
	public function words( $args, $assoc_args ) {

		$bad_words = array( 'the','be','to','of','and','a','in','that','have','I','it','for','not','on','with','he','as','you','do','at','this','but','his','by','from','they','we','say','her','she','or','an','will','my','one','all','would','there','their','what','so','up','out','if','about','who','get','which','go','me','when','make','can','like','time','no','just','him','know','take','person','into','year','your','good','some','could','them','see','other','than','then','now','look','only','come','its','over','think','also','back','after','use','two','how','our','work','first','well','way','even','new','want','because','any','these','give','day','most','us', '-', '&', '-the', '\'the', '.', 'is', 'are', 'i', 'has', 'was', 'many', 'more', 'been', 'such' );

		$args = array(
			'posts_per_page' => -1,
			'cat'            => 'podcast',
		);

		$query = new WP_Query( $args );

		$words = array();

		$type = ( isset( $assoc_args['words'] ) ) ? $assoc_args['words'] : 'both';

		WP_CLI::line( $type );

		foreach ( $query->posts as $post ) {

			$title_words = array();

			if ( $type == 'title' ) {
				$title_words = explode( " ", $post->post_title );
			}

			elseif ( $type == 'post' ) {
				if ( ! empty( $post->post_content ) ) {
					$title_words = explode( " ", wp_strip_all_tags( strip_shortcodes( $post->post_content ), true ) );
				}
			}

			else {
				$title_words = explode( " ", $post->post_title );
				if ( ! empty( $post->post_content ) ) {
					$title_words = explode( " ", wp_strip_all_tags( strip_shortcodes( $post->post_content ), true ) );
				}
			}

			foreach ( $title_words as $word ) {
				$word = strtolower( $word );
				if ( ! array_key_exists( $word, $words ) && ! in_array( strtolower( $word ), $bad_words ) ) {
					$words[ $word ] = 1;
				} else {
					if ( ! in_array( strtolower( $word ), $bad_words ) ) {
						$words[ $word ]++;
					}
				}
			}
		}

		arsort( $words );

		$count = count( $words );

		$i = 0;

		foreach ( $words as $word => $number ) {

			if ( $word == 'jesus' ) {
				WP_CLI::success( $word .': '. $number );
			} else {
				WP_CLI::line( $word .': '. $number );
			}


			$number = $number / 10;
			while ( $number > 0 ) {
				WP_CLI::out( 'X' );
				$number--;
			}

			WP_CLI::line();

			$i++;

			if ( $i > 100 )
				break;
		}

		WP_CLI::line( $count . ' total words' );

		// WP_CLI::line( $count );

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
				'post_author'   => 0,
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
$Sunstone_Posts_Importer = new Sunstone_Posts_Importer();