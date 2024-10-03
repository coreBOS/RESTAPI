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

class MassOpsRepository {
	private $logger = null;

	public function __construct() {
		$this->logger = Logcbrest::getInstance()->getLogger();
	}

	public function getAll(string $apikey, string $ids): array {
		$cb = new WSClient($_ENV['cburl'], $apikey);
		$rdo = $cb->doMassRetrieve($ids);
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
