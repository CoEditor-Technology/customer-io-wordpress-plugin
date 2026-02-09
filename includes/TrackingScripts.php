<?php
/**
 * Customer.io tracking scripts injection and logic
 */
namespace CustomerIO;

use function ceml_edition;

/**
 * Customer.io tracking scripts class
 */
class TrackingScripts {
	/**
	 * Fire hooks and actions
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', [ $this, 'inject_main_tracking_script' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_footer_scripts' ] );
	}

	/**
	 * Enqueue the Customer.io core script with inline config
	 * 
	 * @return void
	 */
	public function inject_main_tracking_script() {
		$api_key = get_option( 'customerio_api_key', '' );
		$selected_site = get_option( 'customerio_selected_site', '' );

		if ( empty( $api_key ) || empty( $selected_site ) ) {
			return;
		}

		$script_url = CUSTOMER_IO_PLUGIN_ASSETS . 'js/customer-io.js';

		wp_enqueue_script(
			'customerio-tracking-script',
			$script_url,
			[],
			CUSTOMER_IO_PLUGIN_VERSION,
			false
		);

		$this->load_config_options();
		$this->load_page_metadata();
	}

	/**
	 * Load and output Customer.io config options as inline script
	 * 
	 * @return void
	 */
	public function load_config_options() {
		$api_key = get_option( 'customerio_api_key', '' );

		$config = [
			'apiKey' => esc_js( $api_key ),
		];

		$config_pairs = [];
		foreach ( $config as $key => $value ) {
			$config_pairs[] = sprintf( '%s: "%s"', $key, $value );
		}

		$config_script = sprintf(
			'window.customerIOConfig = { %s };',
			implode( ', ', $config_pairs )
		);

		wp_add_inline_script(
			'customerio-tracking-script',
			$config_script,
			'before'
		);
	}

	/**
	 * Load and output page metadata as inline script
	 * 
	 * @return void
	 */
	public function load_page_metadata() {

		global $wp_query;

		$post_id        = get_the_ID();
		$page_slug      = \SpectatorTheme\get_current_page_type();
		$queried_object = get_queried_object();

		$author      = '';
		$post_tags   = [];
		$post_topics = [];

		// Single article metadata
		if ( is_single() ) {

			// Author
			$author_data = \SpectatorTheme\get_authors( $post_id )[0] ?? [];
			$author      = $author_data['author_name'] ?? '';

			// Tags
			$query_post_tags = get_the_tags( $post_id );
			if ( ! empty( $query_post_tags ) ) {
				foreach ( $query_post_tags as $tag ) {
					$post_tags[] = $tag->name;
				}
			}

			// Topics (categories)
			$post_categories = get_the_terms( $post_id, 'category' );
			if ( ! empty( $post_categories ) ) {
				foreach ( $post_categories as $c ) {
					$post_topics[] = $c->name;
				}
			}
		}

		// section / section_2
		$section   = '';
		$section_2 = '';

		if ( is_single() ) {

			// Base section from page slug
			$section_parts = explode( '-', $page_slug );
			$section       = ucwords( implode( ' ', $section_parts ) );

			// Illustration override
			if ( in_array( 'illustration', $post_tags, true ) ) {
				$section = 'cartoons';
			}

			// Magazine subsection
			if ( 'magazine' === $page_slug ) {
				$magazines = wp_get_post_terms( $post_id, 'magazines', [ 'fields' => 'names' ] );
				if ( ! empty( $magazines ) ) {
					$section_2 = implode( ', ', $magazines );
				}
			}

		} elseif ( is_post_type_archive() ) {

			$section = $queried_object->label ?? '';

		} elseif ( is_archive() || $wp_query->get( 'is_archive' ) ) {

			$section   = $queried_object->taxonomy ?? '';
			$section_2 = $queried_object->name ?? '';

			if ( ! empty( $wp_query->get( 'coffee_house_archive' ) ) ) {
				$section = 'Coffee House';
			}

			if ( ! empty( $wp_query->get( 'life_archive' ) ) || ! empty( $wp_query->get( 'life' ) ) ) {
				$section   = 'life';
				$section_2 = $wp_query->get( 'life' ) ?? 'life';
			}

			if ( ! empty( $wp_query->get( 'the_critics_archive' ) ) || ! empty( $wp_query->get( 'the-critics' ) ) ) {
				$section   = 'The Critics';
				$section_2 = $wp_query->get( 'the-critics' ) ?? 'The Critics';
			}

		} elseif ( $wp_query->get( 'podcasts_taxonomy_archive' ) ) {

			$section = 'podcast';

		} elseif ( $wp_query->get( 'magazine_archive' ) ) {

			$section   = 'magazine';
			$section_2 = sprintf( 'magazine : %s', $wp_query->get( 'magazine_term' ) );

		} elseif ( $wp_query->get( 'writers_archive' ) ) {

			$section = 'news';

		} elseif ( is_page() ) {

			if ( is_page( 'spectator-briefing' ) ) {
				$section = 'briefings';
			} elseif ( is_page( 'join' ) ) {
				$section = 'subscription';
			} elseif ( is_page( 'account' ) ) {
				$section   = 'my account';
				$section_2 = 'my account';
			}
		}

		// Poems magazine taxonomy
		if ( is_tax( 'magazines' ) && 'poems' === $wp_query->get( 'magazines' ) ) {
			$section   = 'magazine';
			$section_2 = sprintf( 'magazine : %s', $wp_query->get( 'magazines' ) );
		}

		// Illustration tag archive
		if ( is_tag( 'illustration' ) ) {
			$section   = 'illustration';
			$section_2 = '';
		}

		// Homepage
		$front_page = (int) get_option( 'page_on_front' );
		if ( $post_id === $front_page ) {
			$section   = 'homepage';
			$section_2 = '';
		}

		// Edtion
		$edition = '';
		if ( function_exists( 'ceml_edition' ) ) {
			$edition = ceml_edition();
			if ( $edition == 'EN' ) { $edition = 'UK'; }
		}

		// Inject script
		$metadata_script = sprintf(
			'window.customerIOPageMetadata = {
				edition: "%s",
				article_author: "%s",
				article_section: "%s",
				article_section_2: "%s",
				article_tags: %s,
				article_topics: %s
			};',
			esc_js( $edition ),
			esc_js( $author ),
			esc_js( $section ),
			esc_js( $section_2 ),
			wp_json_encode( $post_tags ),
			wp_json_encode( $post_topics )
		);

		wp_add_inline_script(
			'customerio-tracking-script',
			$metadata_script,
			'before'
		);
	}

	/**
	 * Loads the footer scripts for customer.io - Note: this relies on the piano-user-status.js file from the Spectator theme
	 *
	 * @return void
	 */
	public function load_footer_scripts() {
	
		$script_url = CUSTOMER_IO_PLUGIN_ASSETS . 'js/customer-io-footer.js';

		wp_enqueue_script(
			'customer-io-footer-script',
			$script_url,
			[],
			CUSTOMER_IO_PLUGIN_VERSION,
			false
		);

	}
}
