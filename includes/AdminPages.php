<?php
/**
 * Class AdminPages
 */

namespace CustomerIO;


/**
 * Class for managing admin pages for the plugin
 */
class AdminPages {

	/**
	 * Fire hooks and actions
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'register_admin_menu_page' ] );
	}

	/**
	 * Register an admin menu page under "Settings"
	 */
	public function register_admin_menu_page() {
		add_options_page(
			'Customer.io settings',
			'Customer.io',
			'manage_options',
			'customer-io-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Get admin tabs
	 *
	 * @return array
	 */
	public function get_admin_tabs() {
		return [
			[
				'slug'     => 'general',
				'title'    => 'General settings',
				'template' => CUSTOMER_IO_PLUGIN_PAGES . 'general-settings.php',
			],
		];
	}

	/**
	 * Render the admin settings pages
	 *
	 * @return void
	 */
	public function render_settings_page() {
		$tabs = $this->get_admin_tabs();

		if ( empty( $tabs ) ) {
			return;
		}

		$tabs_by_slug = [];
		foreach ( $tabs as $tab ) {
			$tabs_by_slug[ $tab['slug'] ] = $tab;
		}

		$current_tab = isset( $_GET['tab'] )
			? sanitize_key( $_GET['tab'] )
			: $tabs[0]['slug'];

		if ( ! isset( $tabs_by_slug[ $current_tab ] ) ) {
			$current_tab = $tabs[0]['slug'];
		}
		?>
		<div class="wrap">
			<h1>Customer.io settings</h1>

			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab ) : ?>
					<?php
					$is_active = $tab['slug'] === $current_tab;
					$tab_url   = add_query_arg(
						[
							'page' => 'customer-io-settings',
							'tab'  => $tab['slug'],
						],
						admin_url( 'options-general.php' )
					);
					?>
					<a
						href="<?php echo esc_url( $tab_url ); ?>"
						class="nav-tab <?php echo $is_active ? 'nav-tab-active' : ''; ?>"
						style="<?php echo $is_active ? 'font-weight: bold; background-color: white;' : ''; ?>"
					>
						<?php echo esc_html( $tab['title'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="customer-io-tab-content" style="background-color: white; padding: 1rem; border: 1px solid #c3c4c7; border-top: none;">
				<?php
				$template = $tabs_by_slug[ $current_tab ]['template'];

				if ( file_exists( $template ) ) {
					include $template;
				} else {
					printf(
						'<div class="notice notice-error"><p>Template not found: <code>%s</code></p></div>',
						esc_html( $template )
					);
				}
				?>
			</div>
		</div>
		<?php
	}
}
