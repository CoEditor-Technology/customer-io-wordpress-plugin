<?php
/**
 * CustomerIOClient class
 * 
 * @package CustomerIO\Clients
 */

namespace CustomerIO\Clients;

use Exception;

/**
 * CustomerIOClient class
 */
class CustomerIOClient {


	/**
	 * API endpoint for Customer.io
	 *
	 * @var string
	 */
	private $base_url = 'https://track.customer.io/api/v1/';


	/**
	 * Get the site ID based on the site URL.
	 *
	 * @return string The customerIO site ID.
	 */
	private function get_site_id() {
		$site_id = get_option( 'customerio_site_id' );

		if ( empty( $site_id ) ) {
			throw new Exception( esc_html( __( 'Customer.io Site ID is not set. Please configure it in the plugin settings.' ) ) );
		}

		return $site_id;
		
	}

	/**
	 * Get the API key based on the site URL.
	 *
	 * @return string The Customer.io API key.
	 * @throws Exception If the API key is not set.
	 */
	private function get_api_key() {
		$api_key = get_option( 'customerio_track_api_key' );

		if ( empty( $api_key ) ) {
			throw new Exception( esc_html( __( 'Customer.io API key is not set. Please configure it in the plugin settings.' ) ) );
		}

		return $api_key;
	}


	/**
	 * Make a request to the Customer.io API.
	 *
	 * @param string $endpoint The target customer.io endpoint.
	 * @param string $method The HTTP method to use for the request (default is 'POST').
	 * @param array  $body The body of the request, if applicable (default is an empty array).
	 * @return array The response from the Customer.io API, decoded from JSON.
	 * @throws Exception If there is an error making the request or if the API key is not set.
	 */
	public function make_request( $endpoint, $method = 'POST', $body = [] ) {
		$url = $this->base_url . $endpoint;

		$args = [
			'method'  => $method,
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( $this->get_site_id() . ':' . $this->get_api_key() ),
			],
			'body'   => json_encode( $body ),
		];

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new Exception( esc_html( __( 'Error making request to Customer.io API: ' ) ) . $response->get_error_message() );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}


	/**
	 * Update user attributes by email address
	 *
	 * @param string $user_email The user's email address.
	 * @param array $user_data An associative array of user attributes to update.
	 * @return array The response from the Customer.io API.
	 */
	public static function update_user_attributes( $user_email, $user_data = [] ) {

		$client = new self();

		$body = array_merge(
			[
				'userId' => $user_email,
				'email'  => $user_email,
			],
			$user_data
		);

		return $client->make_request(
			'customers/' . $user_email,
			'PUT',
			$body
		);
	}

}
