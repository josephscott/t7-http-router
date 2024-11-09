<?php
declare( strict_types = 1 );

namespace T7\HTTP;

use function class_exists;
use function count;
use function is_array;
use function is_callable;
use function is_readable;
use function is_string;
use function substr;

class Route_Handler {
	public function __construct() {}

	public static function run(
		string|array|callable $handler,
		array $vars = []
	) {
		if (
			is_array( $handler )
			&& count( $handler ) === 2
			&& class_exists( $handler[0] )
		) {
			$class = new $handler[0];
			$class->{$handler[1]}( $vars );
		}

		if ( is_callable( $handler ) ) {
			$handler( $vars );
		}

		$call_file = function ( string $__file, array $vars ) {
			require $__file;
		};

		if (
			is_string( $handler )
			&& substr( $handler, 0, 1 ) === '/'
			&& is_readable( $handler )
		) {
			$call_file( $handler, $vars );
		}
	}
}
