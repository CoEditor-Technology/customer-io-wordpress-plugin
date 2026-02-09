<?php
/**
 * General settings for Customer.io integration
 */
?>

<h2>Customer.io connector settings</h2>

<form method="post" action="options.php">
	<?php
	settings_fields( 'customerio_settings_group' );
	do_settings_sections( 'customerio_settings_group' );
	?>
	<table class="form-table" role="presentation">


		<tr>
			<th scope="row">
				<label for="customerio_selected_site">Selected site</label>
			</th>
			<td>
				<select>
					<option value=""><?php echo esc_html( 'Select a site', 'customerio' ); ?></option>
					<?php
					$sites = \CustomerIO\Settings::get_customerio_sites();
					foreach ( $sites as $site ) {
						$selected = ( get_option( 'customerio_selected_site' ) === $site['id'] ) ? 'selected' : '';
						echo '<option value="' . esc_attr( $site['id'] ) . '" ' . $selected . '>' . esc_html( $site['name'] ) . '</option>';
					}
					?>
				</select>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="customerio_api_key">Customer.io API key</label>
			</th>
			<td>
				<input
					type="password"
					id="customerio_api_key"
					name="customerio_api_key"
					class="regular-text"
					value="<?php echo esc_attr( get_option( 'customerio_api_key' ) ); ?>"
				/>
				<p class="description">
					Enter your Customer.io site API key for JavaScript tracking.
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="customerio_site_id">Customer.io Site ID</label>
			</th>
			<td>
				<input
					type="text"
					id="customerio_site_id"
					name="customerio_site_id"
					class="regular-text"
					value="<?php echo esc_attr( get_option( 'customerio_site_id' ) ); ?>"
				/>
				<p class="description">
					Enter your Customer.io site ID found in your workspace settings.
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="customerio_track_api_key">Track API key</label>
			</th>
			<td>
				<input
					type="password"
					id="customerio_track_api_key"
					name="customerio_track_api_key"
					class="regular-text"
					value="<?php echo esc_attr( get_option( 'customerio_track_api_key' ) ); ?>"
				/>
				<p class="description">
					Enter your Customer.io site for the track API key found in your workspace settings
				</p>
			</td>
		</tr>

	</table>
	<?php submit_button( 'Save settings' ); ?>
</form>
