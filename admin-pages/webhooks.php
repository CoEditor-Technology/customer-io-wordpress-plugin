<?php

/**
 * Webhook URL settings page
 */
?>

<h2>Webhook URLs</h2>

<p>Please add the following webhooks into the relevant platforms to enable real-time event tracking. </p><strong>Note:</strong> Please ensure the API credentials are configured, otherwise the webhooks will not function correctly.</p>
<br />
<h3>Stripe webhooks</h3>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row">
			<label for="customerio_api_key">Customer created/updated</label>
		</th>
		<td>
			<?php echo esc_html( site_url( '/wp-json/coeditor-customerio/v1/stripe-webhooks/customer-updated' ) ); ?>
		</td>
	</tr>

</table>