<?php
/**
 * REST API Endopoints factory.
 *
 * @package CoeditorCustomerIO
 */

namespace CustomerIO\Rest;

/**
 * Class EndpointFactory
 *
 * @package CustomerIO\Rest
 */
class EndpointFactory {

	/**
	 * Array of endpoints that were registered.
	 *
	 * @var array
	 */
	public $endpoints = [];
	/**
	 * Initialize endpoints
	 *
	 * @return void
	 */
	public function register() {
		 $this->register_endpoints();
	}
	/**
	 * Determines which endpoint objects should be enqueued for registration.
	 *
	 * @return array A list of endpoint Objects to register.
	 */
	public function get_endpoints(): array {
		return [
			StripeWebhookEndpoint::class,
		];
	}
	/**
	 * Builds all endpoints.
	 *
	 * @return void
	 */
	public function register_endpoints(): void {
		foreach ( $this->get_endpoints() as $endpoint ) {
			if ( ! $this->exists( $endpoint ) ) {
				$this->endpoints[ $endpoint ] = $this->build( $endpoint );
			}
		}
	}
	/**
	 * Instantiates the endpoint object.
	 *
	 * @param string $class The endpoint class name.
	 *
	 * @return mixed bool or class instance.
	 */
	public function build( string $class ) {
		if ( class_exists( $class ) ) {
			$instance = new $class();
			$instance->register();
		} else {
			$instance = false;
			//phpcs:disable
			error_log('Endpoint not found: ' . $class);
			//phpcs:enable
		}
		return $instance;
	}
	/**
	 * Checks if the endpoint object has already been registered.
	 *
	 * @param string $name The name of the endpoint object.
	 *
	 * @return bool
	 */
	public function exists( string $name ): bool {
		return ! empty( $this->endpoints[ $name ] );
	}
	/**
	 * Get instance of EndpointFactory.
	 *
	 * @return EndpointFactory
	 */
	public static function instance(): EndpointFactory {
		static $instance;
		if ( null === $instance ) {
			$instance = new self();
		}
		return $instance;
	}
}
