<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;

class Error404NotFoundHandler {

	public function __construct(private ResponseFactory $factory) {
	}

	public function __invoke(Request $request, \Slim\Exception\HttpNotFoundException $exception): Response {
		$error = [
			'success' => false,
			'error' => [
				'message' => 'Route not found. Please check the URL and try again.',
				'code' => 'INVALID_URL',
			],
		];
		$payload = json_encode($error, JSON_UNESCAPED_UNICODE);
		$response = $this->factory->createResponse();
		$response->getBody()->write($payload);
		return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
	}
}
