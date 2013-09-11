<?php
/*
 * Load Config File Settings
 */
Configure::load('PaypalWPP.paypal');
 
/*
 * Paypal WPP Component
 * 
 * @author Chris Pierce <cpierce@csdurant.com>
 *
 */
App::uses('Component', 'Controller');

class PaypalWPPComponent extends Component {
	
	/*
	 * Web Payments Pro Hash
	 * 
	 * @thorws BadRequestException
	 * @param string $method, string $nvp
	 * @return mixed[mixed]
	 */
	public function wpp_hash($method = null, $nvp = null) {
		$curl_handler = curl_init();
		curl_setopt($curl_handler, CURLOPT_URL, Configure::read('Paypal.endpoint'));
		curl_setopt($curl_handler, CURLOPT_VERBOSE, 1);
		curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handler, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handler, CURLOPT_POST, 1);
				
		$required_nvp = 'METHOD='.$method;
		$required_nvp .= '&VERSION='.urlencode(Configure::read('Paypal.version'));
		$required_nvp .= '&USER='.urlencode(Configure::read('Paypal.username'));
		$required_nvp .= '&PWD='.urlencode(Configure::read('Paypal.password'));
		$required_nvp .= '&SIGNATURE='.urlencode(Configure::read('Paypal.signature'));
		
		curl_setopt($curl_handler, CURLOPT_POSTFIELDS, $required_nvp.$nvp);
		$http_responder = curl_exec($curl_handler);
		
		if (!$http_responder) {
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
			throw new BadRequestException('Invalid HTTP Response for POST request ('.$required_nvp.$nvp.') to '.Configure::read('Paypal.endpoint'));
		
		return $parsed_response;
	}

} 