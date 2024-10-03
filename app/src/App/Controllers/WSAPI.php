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
use App\Utils\Logcbrest;
use App\Utils\WSClient;

class WSAPI {
	private $logger = null;

	public function __construct() {
		$this->logger = Logcbrest::getInstance()->getLogger();
	}

	public function query(Request $request, Response $response, array $args): Response {
		if (empty($args['query'])) {
			$rdo = $this->getErrorResponse('no query', 'QUERY_SYNTAX_ERROR');
		} else {
			$cb = new WSClient($_ENV['cburl'], self::getAPIKey($request));
			$rdo = $cb->doQuery($args['query']);
			if ($rdo === false) {
				$rdo = $this->getErrorResponse('query error', 'QUERY_SYNTAX_ERROR');
			} else {
				$rdo = [
					'success' => true,
					'result' => $rdo,
				];
			}
		}
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function login(Request $request, Response $response, array $args): Response {
		$cb = new WSClient($_ENV['cburl'], self::getAPIKey($request));
		$rdo = $cb->doLogin($args['username'], $args['password'], false);
		if (!$rdo) {
			$rdo = $cb->doLogin($args['username'], $args['password'], true);
		}
		if ($rdo) {
			$rdo = [
				'success' => true,
				'result' => $cb->_sessionid,
			];
		} else {
			$rdo = $this->getErrorResponse('Authentication failure', 'AUTHENTICATION_FAILURE');
		}
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function listtypes(Request $request, Response $response, array $args): Response {
		$cb = new WSClient($_ENV['cburl'], self::getAPIKey($request));
		$rdo = $cb->doListTypes();
		if ($rdo === false) {
			$rdo = $this->getErrorResponse('Access denied', 'ACCESS_DENIED');
		} else {
			$rdo = [
				'success' => true,
				'result' => $rdo,
			];
		}
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public function describe(Request $request, Response $response, array $args): Response {
		if (empty($args['module'])) {
			$rdo = $this->getErrorResponse('Invalid module(s)', 'INVALID_MODULE');
		} else {
			$cb = new WSClient($_ENV['cburl'], self::getAPIKey($request));
			$rdo = $cb->doDescribe($args['module']);
			if ($rdo === false) {
				$rdo = $this->getErrorResponse('Invalid module(s)', 'INVALID_MODULE');
			} else {
				$rdo = [
					'success' => true,
					'result' => $rdo,
				];
			}
		}
		$response = $this->writeOutput($response, $rdo);
		return $response->withStatus($this->getResponseCode($rdo['success']));
	}

	public static function getAPIKey($request) {
		if ($request->hasHeader('X-API-Key')) {
			return $request->getHeaderLine('X-API-Key');
		}
		return false;
	}

	private function writeOutput(Response $response, array $output): Response {
		$response->getBody()->write(json_encode($output));
		return $response;
	}

	private function getResponseCode(bool $status): int {
		return $status ? 200 : 400;
	}

	private function getErrorResponse(string $msg, string $code): array {
		return [
			'success' => false,
			'error' => [
				'message' => $msg,
				'code' => $code,
			],
		];
	}
}
