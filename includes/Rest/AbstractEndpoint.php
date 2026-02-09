<?php
/**
 * Class AbstractEndpoint
 *
 * @package CoeditorCustomerIO
 */

namespace CustomerIO\Rest;

/**
 * Class AbstractEndpoint
 *
 * @package CustomerIO\Rest
 */
abstract class AbstractEndpoint {

	/**
	 * Sets the endpoint route name.
	 *
	 * @return string Route name.
	 */
	abstract public function get_name(): string;

	/**
	 * The callback function to trigger when endpoint is hooked.
	 *
	 * @param \WP_REST_Request $request The request parameters.
	 *
	 * @return mixed
	 */
	abstract public function callback( \WP_REST_Request $request );

	/**
	 * The arguments to pass to register_rest_route.
	 *
	 * @return array
	 */
	abstract public function get_route_args(): array;

	/**
	 * The namespace in which to place said route.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return 'coeditor-customerio';
	}

	/**
	 * The version of the namespace. Eg /coeditor-customerio/v1/.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return 'v1';
	}

	/**
	 * Builds the final endpoint path.
	 *
	 * @param string $endpoint        The endpoint name.
	 * @param bool   $include_namespace Whether to include the namespace in the endpoint path or not.
	 *
	 * @return string
	 */
	public function get_endpoint_path( $endpoint, $include_namespace = false ): string {
		return ( $include_namespace ? $this->get_namespace() : '' ) . '/' . $this->get_version() . '/' . $endpoint;
	}

	/**
	 * The entry point into this class, allowing self instantiation within plugin_support.
	 *
	 * @return void
	 */
	public function register(): void {
		$route_args = $this->get_route_args();

		register_rest_route(
			$this->get_namespace(),
			$this->get_endpoint_path( $this->get_name() ),
			$route_args
		);
	}

	/**
	 * REST support must be present.
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return function_exists( 'register_rest_route' );
	}

}
