<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
declare(strict_types=1);

namespace App\Utils;

use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;

class Logcbrest {
	private static $instance = null;
	private $logger = null;
	private const LOGFILE = '../logs/cbrest.log';

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getLogger(): Logger {
		if ($this->logger === null) {
			$this->logger = new Logger();
			$writer = new Stream(self::LOGFILE);
			$this->logger->addWriter($writer);
		}
		return $this->logger;
	}
}
