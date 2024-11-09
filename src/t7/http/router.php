<?php
declare( strict_types = 1 );

namespace T7\HTTP;

use function header;
use function rawurldecode;
use function str_starts_with;
use function strpos;
use function substr;

class Router {
	public string $cache_file = '/dev/null';

	public bool $cache_disabled = true;

	public bool $redirect_on_no_trailing_slash = true;

	private array $routes = [];

	private array $special_routes = [];

	public function __construct( array $routes ) {
		$this->routes = $routes;
	}

	public function start() : void {
		$dispatcher = \FastRoute\cachedDispatcher(
			function ( \FastRoute\RouteCollector $r ) {
				foreach( $this->routes as $i => $s ) {
					// Set aside special routes, they all start with '_'
					if ( str_starts_with( $s[0], '_' ) ) {
						$this->special_routes[$s[0]] = $s[1];
						unset( $this->routes[$i] );
						continue;
					}

					// Array:
					// [0] - method
					// [1] - path/pattern
					// [2] - handler
					$r->addRoute( $s[0], $s[1], $s[2] );
				}
			},
			[
				'cacheFile' => $this->cache_file,
				'cacheDisabled' => $this->cache_disabled,
			]
		);

		$http_method = $_SERVER['REQUEST_METHOD'] ?? '';
		$uri = $_SERVER['REQUEST_URI'] ?? '';
		$pos = strpos( $uri, '?' );
		if ( $pos !== false ) {
			$uri = substr( $uri, 0, $pos );
		}

		$uri = rawurldecode( $uri );
		$route_info = $dispatcher->dispatch( $http_method, $uri );

		// Route info
		// [0] - status
		// [1] - handle/allowed methods
		// [2] - route vars, captured from the route pattern
		switch ( $route_info[0] ) {
			case \FastRoute\Dispatcher::NOT_FOUND:
				if ( $this->redirect_on_no_trailing_slash ) {
					if ( substr( $uri, -1 ) !== '/' ) {
						$qs = '';
						if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
							$qs = '?' . $_SERVER['QUERY_STRING'];
						}
						header( 'Location: ' . $uri . '/' . $qs, true, 301 );
						exit();
					}
				}

				// Still here, then 404
				header( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );
				if ( isset( $this->speical_routes['_404'] ) ) {
					Route_Handler::run( handler: $this->special_routes['_404'] );
				} else {
					echo '404 - Not Found';
				}
				exit();

			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				header( '405 Method Not Allowed' );
				echo '405 - Method Not Allowed';
				$allowed_methods = $route_info[1];
				break;

			case \FastRoute\Dispatcher::FOUND:
				$route_handler = Route_Handler::run(
					handler: $route_info[1],
					vars: $route_info[2]
				);
				break;
		}
	}
}
