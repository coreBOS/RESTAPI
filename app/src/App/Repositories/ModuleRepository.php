<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
declare(strict_types=1);

namespace App\Repositories;

use App\Utils\Logcbrest;
use App\Utils\WSClient;

class ModuleRepository {
	private $logger = null;

	public function __construct() {
		$this->logger = Logcbrest::getInstance()->getLogger();
	}

	public function getAll(string $apikey, string $module): array {
		$cb = new WSClient($_ENV['cburl'], $apikey);
		$rdo = $cb->doQuery("select * from $module");
		if ($rdo === false) {
			$rdo = $this->getErrorResponse('Invalid module(s)', 'INVALID_MODULE');
		} else {
			$rdo = [
				'success' => true,
				'result' => $rdo,
			];
		}
		return $rdo;
	}

	public function create(string $apikey, string $module, array $data): array {
		$cb = new WSClient($_ENV['cburl'], $apikey);
		$rdo = $cb->doCreate($module, $data);
		if ($rdo === false) {
			$rdo = $this->getErrorResponse('Error creating record', 'MANDATORY_FIELDS_MISSING');
		} else {
			$rdo = [
				'success' => true,
				'result' => $rdo,
			];
		}
		return $rdo;
	}

	public function retrieve(string $apikey, string $id): array {
		$cb = new WSClient($_ENV['cburl'], $apikey);
		$rdo = $cb->doRetrieve($id);
		if ($rdo === false) {
			$rdo = $this->getErrorResponse('Record not found', 'INVALID_ID_ATTRIBUTE');
		} else {
			$rdo = [
				'success' => true,
				'result' => $rdo,
			];
		}
		return $rdo;
	}

	public function update(string $apikey, string $module, array $data):  array {
		$cb = new WSClient($_ENV['cburl'], $apikey);
		$rdo = $cb->doRevise($module, $data);
		if ($rdo === false) {
			$rdo = $this->getErrorResponse('Record not found', 'INVALID_ID_ATTRIBUTE');
		} else {
			$rdo = [
				'success' => true,
				'result' => $rdo,
			];
		}
		return $rdo;
	}

	public function delete(string $apikey, string $id):  array {
		$cb = new WSClient($_ENV['cburl'], $apikey);
		$rdo = $cb->doDelete($id);
		if ($rdo === false) {
			$rdo = $this->getErrorResponse('Record not found', 'RECORD_NOT_FOUND');
		} else {
			$rdo = [
				'success' => true,
				'result' => $rdo,
			];
		}
		return $rdo;
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
