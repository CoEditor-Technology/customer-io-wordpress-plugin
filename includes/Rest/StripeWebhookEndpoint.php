<?php
/**
 * Stripe webhook endpoint
 *
 * Endpoint to handle stripe webhook events.
 *
 * @package CoeditorCustomerIO
 */

namespace CustomerIO\Rest;

use CustomerIO\Clients\CustomerIOClient;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class StripeWebhookEndpoint.
 *
 * @package CoeditorCustomerIO\Rest
 */
class StripeWebhookEndpoint extends AbstractEndpoint {
	/**
	 * Gets the route name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'stripe-webhooks/(?P<operation>[\w-]+)';
	}

	/**
	 * Handle the various operations for this endpoint.
	 *
	 * @param WP_REST_Request $request Request instance.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function callback( WP_REST_Request $request ) {
		$operation = $request->get_param( 'operation' );

		switch ( $operation ) {
			case 'customer-updated':
				$response = $this->customer_updated( $request );
				break;

			default:
				$response = new WP_Error( 'invalid_request', __( 'There has been an error. Please try again.' ), [ 'status' => 400 ] );
		}

		return rest_ensure_response( $response );
	}


	/**
	 * Handle the customer updated event.
	 *
	 * @param WP_REST_Request $request Request instance.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	private function customer_updated( WP_REST_Request $request ) {
		$request_body = $request->get_body();
		$data         = json_decode( $request_body, true );

		if ( null === $data ) {
			return new WP_Error( 'invalid_json', __( 'Invalid JSON payload.' ), [ 'status' => 400 ] );
		} 

		$data = $data['data']['object'] ?? null;

		if ( null === $data ) {
			return new WP_Error( 'missing_data', __( 'Missing data in payload.' ), [ 'status' => 400 ] );
		}
		
		$email = $data['email'] ?? '';

		if ( empty( $email ) ) {
			return new WP_Error( 'missing_email', __( 'Email is a required parameter.' ), [ 'status' => 400 ] );
		}

		$email            = sanitize_email( $email );
		$billing_name     = $data['name'] ?? '';
		$billing_add1     = $data['address']['line1'] ?? '';
		$billing_add2     = $data['address']['line2'] ?? '';
		$billing_city     = $data['address']['city'] ?? '';
		$billing_postcode = $data['address']['postal_code'] ?? '';
		$billing_state    = $data['address']['state'] ?? '';
		$billing_country  = $data['address']['country'] ?? '';
		$billing_phone    = $data['phone'] ?? '';
		$contact_pref     = $data['metadata']['contact_pref'] ?? '';

		$customer_data = [
			'email'            => sanitize_email( $email ),
			'billing_name'     => $billing_name,
			'billing_add1'     => $billing_add1,
			'billing_add2'     => $billing_add2,
			'billing_city'     => $billing_city,
			'billing_postcode' => $billing_postcode,
			'billing_state'    => $billing_state,
			'billing_country'  => $billing_country,
			'billing_phone'    => $billing_phone,
			'contact_pref'     => $contact_pref,
		];

		try {
			CustomerIOClient::update_user_attributes( $email, $customer_data );
		} catch ( Exception $e ) {
			return new WP_Error( 'update_failed', __( 'Failed to update customer profile: ' ) . $e->getMessage(), [ 'status' => 500 ] );
		}

		return rest_ensure_response( [ 'message' => 'Customer updated event successfully received.' ] );
	}

	/**
	 * Gets the arguments for route registartion.
	 *
	 * @return array
	 */
	public function get_route_args(): array {
		return [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'callback' ],
				'permission_callback' => '__return_true',
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'callback' ],
				'permission_callback' => '__return_true',
			],
		];
	}
}
