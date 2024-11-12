<?php
declare( strict_types = 1 );

test( 'post', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->post(
		url: 'http://localhost:17171',
		data: ['field' => 'value'],
	);
	$data = json_decode( $response->body, true );

	expect( $response->code )->toBe( 200 );
	expect( $response->headers['content-type'] )->toBe( 'text/html; charset=UTF-8' );
	expect( $data )->toHaveKey( 'post' );
	expect( $data['post'] )->toHaveKey( 'field' );
	expect( $data['post']['field'] )->toBe( 'value' );
} );

test( 'post with multiple fields', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->post(
		url: 'http://localhost:17171',
		data: ['field1' => 'value1', 'field2' => 'value2'],
	);

	$data = json_decode( $response->body, true );

	expect( $response->code )->toBe( 200 );
	expect( $data )->not()->toBeNull();
	expect( $data )->toHaveKey( 'post' );
	expect( $data['post'] )->toHaveKey( 'field1' );
	expect( $data['post'] )->toHaveKey( 'field2' );
	expect( $data['post']['field1'] )->toBe( 'value1' );
	expect( $data['post']['field2'] )->toBe( 'value2' );
} );

test( 'post to non-existent endpoint returns 404', function () {
	$request = new \T7\HTTP\Request();
	$response = $request->post(
		url: 'http://localhost:17171/not-found/',
		data: ['field' => 'value'],
	);
	expect( $response->code )->toBe( 404 );
} );
