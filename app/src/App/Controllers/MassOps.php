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
use App\Repositories\MassOpsRepository;
use App\Utils\Logcbrest;

class MassOps {
	private $logger = null;

	public function __construct(private MassOpsRepository $repository) {
		$this->logger = Logcbrest::getInstance()->getLogger();
	}

	public function list(Request $request, Response $response, array $args): Response {
		$rdo = $this->repository->getAll(self::getAPIKey($request), $args['ids']);
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function delete(Request $request, Response $response, array $args): Response {
		$rdo = $this->repository->delete(self::getAPIKey($request), $args['ids']);
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
