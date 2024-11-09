<?php
declare( strict_types = 1 );

test( 'post', function () {
	$ctx = stream_context_create( [
		'http' => [
			'method' => 'POST',
			'header' => 'Content-Type: application/x-www-form-urlencoded',
			'content' => http_build_query( ['field' => 'value'] ),
		],
	] );

	$response = file_get_contents( 'http://localhost:17171', false, $ctx );
	$headers = parse_http_headers( $http_response_header );

	$data = json_decode( $response, true );

	expect( $headers['response_code'] )->toBe( 200 );
	expect( $headers['content-type'] )->toBe( 'text/html; charset=UTF-8' );
	expect( $data )->toHaveKey( 'post' );
	expect( $data['post'] )->toHaveKey( 'field' );
	expect( $data['post']['field'] )->toBe( 'value' );
} );

test( 'post with multiple fields', function () {
	$ctx = stream_context_create( [
		'http' => [
			'method' => 'POST',
			'header' => [
				'Content-Type: application/x-www-form-urlencoded',
				'Content-Length: ' . strlen( http_build_query( [
					'field1' => 'value1',
					'field2' => 'value2',
				] ) ),
			],
			'content' => http_build_query( [
				'field1' => 'value1',
				'field2' => 'value2',
			] ),
		],
	] );

	$response = file_get_contents( 'http://localhost:17171', false, $ctx );

	$headers = parse_http_headers( $http_response_header );
	$data = json_decode( $response, true );

	expect( $headers['response_code'] )->toBe( 200 );
	expect( $data )->not()->toBeNull();
	expect( $data )->toHaveKey( 'post' );
	expect( $data['post'] )->toHaveKey( 'field1' );
	expect( $data['post'] )->toHaveKey( 'field2' );
	expect( $data['post']['field1'] )->toBe( 'value1' );
	expect( $data['post']['field2'] )->toBe( 'value2' );
} );

test( 'post with json content', function () {
	$jsonData = json_encode( ['json_field' => 'json_value'] );

	$ctx = stream_context_create( [
		'http' => [
			'method' => 'POST',
			'header' => 'Content-Type: application/json',
			'content' => $jsonData,
		],
	] );

	$response = file_get_contents( 'http://localhost:17171', false, $ctx );
	$headers = parse_http_headers( $http_response_header );
	$data = json_decode( $response, true );

	expect( $headers['response_code'] )->toBe( 200 );
	expect( $data )->toHaveKey( 'json' );
	expect( $data['json'] )->toHaveKey( 'json_field' );
	expect( $data['json']['json_field'] )->toBe( 'json_value' );
} );

test( 'post to non-existent endpoint returns 404', function () {
	$ctx = stream_context_create( [
		'http' => [
			'method' => 'POST',
			'content' => http_build_query( ['field' => 'value'] ),
		],
	] );

	$response = file_get_contents( 'http://localhost:17171/not-found/', false, $ctx );
	$headers = parse_http_headers( $http_response_header );
	expect( $headers['response_code'] )->toBe( 404 );
} );
