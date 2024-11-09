<?php
declare( strict_types = 1 );

test( 'get', function () {
	$response = file_get_contents( 'http://localhost:17171' );
	$headers = parse_http_headers( $http_response_header );

	expect( $headers['response_code'] )->toBe( 200 );
} );

test( 'get returns expected content type', function () {
	$response = file_get_contents( 'http://localhost:17171' );
	$headers = parse_http_headers( $http_response_header );

	expect( $headers['content-type'] )->toBe( 'text/html; charset=UTF-8' );
} );

test( 'get with query parameters', function () {
	$response = file_get_contents( 'http://localhost:17171/json/?param=test' );
	$headers = parse_http_headers( $http_response_header );
	$data = json_decode( $response, true );

	expect( $headers['response_code'] )->toBe( 200 );

	expect( $data )->toHaveKey( 'get' );
	expect( $data['get'] )->toHaveKey( 'param' );
	expect( $data['get']['param'] )->toBe( 'test' );
} );

test( 'get non-existent endpoint returns 404', function () {
	$response = file_get_contents( 'http://localhost:17171/not-found/' );
	$headers = parse_http_headers( $http_response_header );
	expect( $headers['response_code'] )->toBe( 404 );
} );

test( 'get with invalid host returns error', function () {
	try {
		$response = file_get_contents( 'http://invalid-host:17171' );
		$this->fail( 'Expected exception was not thrown' );
	} catch ( \Exception $e ) {
		expect( $e )->toBeInstanceOf( \Exception::class );
	}
} );

test( 'get with timeout', function () {
	$ctx = stream_context_create( [
		'http' => [
			'timeout' => 1, // 1 second timeout
		],
	] );

	try {
		$response = file_get_contents( 'http://localhost:17171/slow-endpoint', false, $ctx );
		$this->fail( 'Expected timeout exception was not thrown' );
	} catch ( \Exception $e ) {
		expect( $e )->toBeInstanceOf( \Exception::class );
	}
} );
