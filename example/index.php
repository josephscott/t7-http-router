<?php
declare( strict_types = 1 );

require __DIR__ . '/../vendor/autoload.php';

class Speak {
	public function get( array $vars ) {
		header( 'Content-Type: text/plain' );
		echo "Speaking ...\n";
		print_r( $vars );
	}
}

$_router = new T7\HTTP\Router(
	routes: [
		[ '_404',  __DIR__ . '/404.php' ],
		[ 'GET', '/', __DIR__ . '/home.php' ],
		[ 'GET', '/hello/[{name}/]', __DIR__ . '/hello.php' ],
		[ 'GET', '/json/[{thing}/]', __DIR__ . '/json.php' ],
		[ 'GET', '/function/[{thing}/]', function ( $vars ) {
			echo "<pre>\n";
			print_r( $vars );
			echo "</pre>\n";
		} ],
		[ 'GET', '/speak/[{thing}/]', [ 'Speak', 'get' ] ],
		[ 'POST', '/', function () {
			header( 'Content-Type: text/html; charset=UTF-8' );

			$response = [
				'post' => $_POST,
				'get' => $_GET,
			];

			// Add JSON data if content-type is application/json
			if ( isset( $_SERVER['CONTENT_TYPE'] ) &&
				strpos( $_SERVER['CONTENT_TYPE'], 'application/json' ) !== false ) {
				$response['json'] = json_decode( file_get_contents( 'php://input' ), true );
			}

			echo json_encode( $response );
			exit;
		} ],
	]
);

$_router->start();
