<?php
declare( strict_types = 1 );

test( 'get', function () {
	$response = file_get_contents( 'http://localhost:17171' );
	$headers = parse_http_headers( $http_response_header );

	expect( $headers['response_code'] )->toBe( 200 );
} );
