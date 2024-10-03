<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ModuleRepository;
use App\Utils\Logcbrest;

class Module {
	private $logger = null;

	public function __construct(private ModuleRepository $repository) {
		$this->logger = Logcbrest::getInstance()->getLogger();
	}

	public function list(Request $request, Response $response, array $args): Response {
		$rdo = $this->repository->getAll(self::getAPIKey($request), $args['module']);
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function create(Request $request, Response $response, array $args): Response {
		$body = $request->getParsedBody();
		$rdo = $this->repository->create(self::getAPIKey($request), $args['module'], $body);
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($rdo['success'] ? 201 : 400);
	}

	public function retrieve(Request $request, Response $response, array $args): Response {
		$rdo = $this->repository->retrieve(self::getAPIKey($request), $args['id']);
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function update(Request $request, Response $response, array $args): Response {
		$body = $request->getParsedBody();
		$rdo = $this->repository->update(self::getAPIKey($request), $args['module'], $body);
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function delete(Request $request, Response $response, array $args): Response {
		$rdo = $this->repository->delete(self::getAPIKey($request), $args['id']);
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	private function writeOutput(Response $response, array $output): Response {
		$response->getBody()->write(json_encode($output));
		return $response;
	}

	public static function getAPIKey($request) {
		if ($request->hasHeader('X-API-Key')) {
			return $request->getHeaderLine('X-API-Key');
		}
		return false;
	}

	private function getResponseCode(bool $status): int {
		return $status ? 200 : 400;
	}
}
