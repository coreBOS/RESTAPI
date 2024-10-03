<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full copyright and license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
namespace App\Utils;

use App\Utils\CURLHTTPClient;

class HTTPClient extends CURLHTTPClient {
	public $_serviceurl = '';

	public function __construct($url, $credentialtoken = false) {
		if (!function_exists('curl_exec')) {
			die('HTTPClient: Curl extension not enabled!');
		}
		parent::__construct();
		$this->_serviceurl = $url;
		$useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
		$this->set_user_agent($useragent);

		// Escape SSL certificate hostname verification
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		if ($credentialtoken) {
			$headers = [
				'corebos-authorization: '.$credentialtoken,
			];
			// Set the headers in the cURL request
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}
	}

	public function doPost($postdata = false, $decodeResponseJSON = false, $timeout = 20) {
		if ($postdata === false) {
			$postdata = array();
		}
		$resdata = $this->send_post_data($this->_serviceurl, $postdata, null, $timeout);
		if ($resdata && $decodeResponseJSON) {
			$resdata = json_decode($resdata, true);
		}
		return $resdata;
	}

	public function doGet($getdata = false, $decodeResponseJSON = false, $timeout = 20) {
		if ($getdata === false) {
			$getdata = array();
		}
		$queryString = http_build_query($getdata);
		$resdata = $this->fetch_url("$this->_serviceurl?$queryString", null, $timeout);
		if ($resdata && $decodeResponseJSON) {
			$resdata = json_decode($resdata, true);
		}
		return $resdata;
	}
}
?>
