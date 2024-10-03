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

class RequireAPIKey {

	public function __construct(private ResponseFactory $factory) {
	}

	public function __invoke(Request $request, RequestHandler $handler): Response {
		if (!$request->hasHeader('X-API-Key')) {
			$response = $this->factory->createResponse();
			$error = [
				'success' => false,
				'error' => [
					'message' => 'X-API-Key missing from request.',
					'code' => 'AUTHENTICATION_REQUIRED',
				],
			];
			$response->getBody()->write(json_encode($error));
			return $response->withStatus(400);
		}
		return $handler->handle($request);
	}
}
