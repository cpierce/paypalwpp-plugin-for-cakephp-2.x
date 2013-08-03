PayPal WebPaymentsPro (WPP) Component for CakePHP 2.x
===================================

CakePHP 2.x Component for interfacing with Paypal WPP

Usage:
===================================
Load component in the Controller using:

Configure Component Username, Password and Signature.  

```
	private $config = array(
		'username' => 'username_api1.domain.com',
		'password' => 'THGSWS658IKUN79S',
		'signature' => 'AFYn4irhcVyzOOiJkc.H2zPIuztlArzO7mr5uXMO6DLICAE85JF.H5PPp',
		'endpoint' => 'https://api-3t.paypal.com/nvp';
		'version' => '51.0',
	)
```

Load the Component into the controller of your choice.
```
	public $components = array(
		'PaypalWPP',
	),
```