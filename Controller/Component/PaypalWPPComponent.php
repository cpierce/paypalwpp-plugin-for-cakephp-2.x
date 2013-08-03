<?php
/*
 * Paypal WPP Component
 * 
 * @author Chris PIerce <cpierce@csdurant.com>
 *
 */
App::uses('Component', 'Controller');

class PaypalWPPComponent extends Component {

	/*
	 * Config for Paypal WPP Settings
	 *
	 * @var mixed[mixed]
	 */
	private $config = array(
		'username' => '',
		'password' => '',
		'signature' => '',
		'endpoint' => 'https://api-3t.paypal.com/nvp';
		'version' => '51.0',
	)
	
	/*
	 * Web Payments Pro Hash
	 * 
	 * @thorws BadRequestException
	 * @param string $method, string $nvp
	 * @return mixed[mixed]
	 */
	public function wpp_hash($method = null, $nvp = null) {
		$curl_handler = curl_init();
		curl_setopt($curl_handler, CURLOPT_URL, $this->config['endpoint']);
		curl_setopt($curl_handler, CURLOPT_VERBOSE, 1);
		curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handler, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($chandler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chandler, CURLOPT_POST, 1);
		
		$required_nvp = 'METHOD='.$method;
		$required_nvp .= '&VERSION='.urlencode($this->config['version']);
		$required_nvp .= '&USER='.urlencode($this->config['username']);
		$required_nvp .= '&PWD='.urlencode($this->config['password']);
		$required_nvp .= '&SIGNATURE='.urlencode($this->config['signature']);
		
		curl_setopt($curl_handler, CURLOPT_POSTFIELDS, $required_nvp.$nvp);
		$http_responder = curl_exec($curl_handler);
		
		if (!http_responder) {
			throw new BadRequestException($method.'failed: '.curl_error($curl_handler).' ('.curl_errno($curl_handler).')');
		}
		
		$responder = explode('&', $http_responder);
		$parsed_response = array();
		
		foreach($responder as $response) {
			$response_array = explode('=', $response);
			if (count($response_array) >= 1)
				$parsed_response[$response_array[0]] = urldecode($response_array[1]);
		}
		
		if ((count($parsed_response) < 1) || !array_key_exists('ACK', $parsed_response))
			throw new BadRequestException('Invalid HTTP Response for POST request ('.$required_nvp.$nvp.') to '.$this->config['endpoint']);
		
		return $parsed_response;
	}

} 