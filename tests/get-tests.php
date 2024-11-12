<?php
declare( strict_types = 1 );

test( 'get', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171'
	);

	expect( $response->code )->toBe( 200 );
} );

test( 'get returns expected content type', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171'
	);
	$data = json_decode( $response->body, true );

	expect( $response->headers['content-type'] )->toBe( 'text/html; charset=UTF-8' );
} );

test( 'get with query parameters', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171/json/?param=test'
	);
	$data = json_decode( $response->body, true );

	expect( $response->code )->toBe( 200 );
	expect( $data )->not()->toBeNull();
	expect( is_array( $data ) )->toBeTrue();
	expect( $data )->toHaveKey( 'get' );
	expect( $data['get'] )->toHaveKey( 'param' );
	expect( $data['get']['param'] )->toBe( 'test' );
} );

test( 'get non-existent endpoint returns 404', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171/not-found/'
	);

	expect( $response->code )->toBe( 404 );
} );

test( 'get with invalid host returns error', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://invalid-host:17171'
	);

	expect( $response->error )->toBe( true );
} );

test( 'get with timeout', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->get(
		url: 'http://localhost:17171/json/?sleep=2',
		options: [
			'timeout' => 1,
		]
	);

	expect( $response->error )->toBe( true );
} );
