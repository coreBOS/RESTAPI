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

class AddJsonResponseHeader {
	public function __invoke(Request $request, RequestHandler $handler): Response {
		$response = $handler->handle($request);
		return $response->withHeader('Content-Type', 'application/json');
	}
}
