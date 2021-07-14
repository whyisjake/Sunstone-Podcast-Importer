<?php

WP_CLI::add_command( 'sunstone', 'Sunstone_CLI' );

/**
 * Sunstone Core CLI Functions
 */
class Sunstone_CLI extends WP_CLI_Command {


	/**
	 * Create archive posts
	 * If you run `wp sunstone install --force`, it will bybass the installed check, and force the install.
	 *
	 * @subcommand install
	 * @param     [type] $args       No args at the moment.
	 * @param     [type] $assoc_args --force
	 * @return    string             CLI Output.
	 */
	public function install( $args, $assoc_args ) {

		$force     = isset( $assoc_args['force'] );
		$default   = false;
		$option    = 'have_archive_posts_been_added';
		$installed = get_option( $option, $default );

		WP_CLI::out( 'Have the posts been added? ' );
		if ( $installed or $force ) {
			WP_CLI::out( WP_CLI::colorize( 'Yes' ) );
		} else {
			WP_CLI::out( 'No' );
		}

		WP_CLI::line();

		// Have we intalled already?
		if ( ! $installed or $force ) {

			// Kick off the class.
			$sunstone_posts_importer = new sunstone_posts_importer();
			$sunstone_posts_importer->install();

			// Update the option
			update_option( $option, true );
		}

	}

	/**
	 * Create archive posts
	 * If you run `wp sunstone install --force`, it will bybass the installed check, and force the install.
	 *
	 * @subcommand words
	 * @param      [type] $args       No args at the moment.
	 * @param      [type] $assoc_args --force
	 * @return     string             CLI Output.
	 */
	public function words( $args, $assoc_args ) {

		// Kick off the class.
		$sunstone_posts_importer = new sunstone_posts_importer();
		$sunstone_posts_importer->words( $args, $assoc_args );

	}

	/**
	 * Create archive posts
	 * If you run `wp sunstone install --force`, it will bybass the installed check, and force the install.
	 *
	 * @subcommand events
	 * @param      [type] $args       No args at the moment.
	 * @param      [type] $assoc_args --force
	 * @return     string             CLI Output.
	 */
	public function events( $args, $assoc_args ) {

		// Kick off the class.
		$sunstone_posts_importer = new sunstone_posts_importer();
		$sunstone_posts_importer->events( $args, $assoc_args );

	}

	/**
	 * Create archive posts
	 * If you run `wp sunstone install --force`, it will bybass the installed check, and force the install.
	 *
	 * @subcommand speakers
	 * @param      [type] $args       No args at the moment.
	 * @param      [type] $assoc_args --force
	 * @return     string             CLI Output.
	 */
	public function speakers( $args, $assoc_args ) {

		// Kick off the class.
		$sunstone_posts_importer = new sunstone_posts_importer();
		$sunstone_posts_importer->speakers( $args, $assoc_args );

	}

}
