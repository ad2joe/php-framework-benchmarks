<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\net;

/**
 * Base message class for any URI based request/response.
 * @see http://tools.ietf.org/html/rfc3986#section-1.1.1
 * @see http://en.wikipedia.org/wiki/URI_scheme#Generic_syntax
 *
 */
class Message extends \lithium\core\Object {

	/**
	 * The uri scheme
	 *
	 * @var string
	 */
	public $scheme = 'tcp';

	/**
	 * The hostname for this endpoint.
	 *
	 * @var string
	 */
	public $host = 'localhost';

	/**
	 * The port
	 *
	 * @var string
	 */
	public $port = null;

	/**
	 * Absolute path of the request.
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * The username
	 *
	 * @var string
	 */
	public $username = null;

	/**
	 * Absolute path of the request.
	 *
	 * @var string
	 */
	public $password = null;

	/**
	 * The body of the message.
	 *
	 * @var array
	 */
	public $body = array();

	/**
	 * Adds config values to the public properties when a new object is created.
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		$defaults = array(
			'scheme' => 'tcp',
			'host' => 'localhost',
			'port' => null,
			'path' => null,
			'username' => null,
			'password' => null,
			'body' => null,
			'message' => null,
		);
		$config += $defaults;

		foreach (array_filter($config) as $key => $value) {
			$this->{$key} = $value;
		}
		parent::__construct($config);
	}

	/**
	 * Add body parts.
	 *
	 * @param mixed $data
	 * @param array $options
	 *        - `'buffer'`: split the body string
	 * @return array
	 */
	public function body($data = null, $options = array()) {
		$default = array('buffer' => null);
		$options += $default;
		$this->body = array_merge((array) $this->body, (array) $data);
		$body = trim(join("\r\n", $this->body));
		return ($options['buffer']) ? str_split($body, $options['buffer']) : $body;
	}

	/**
	 * Converts the data in the record set to a different format, i.e. an array. Available
	 * options: array, url, context, or string.
	 *
	 * @param string $format Format to convert to.
	 * @param array $options
	 * @return mixed
	 */
	public function to($format, array $options = array()) {
		switch ($format) {
			case 'array':
				$array = array();
				$r = new \ReflectionClass(get_class($this));
				foreach ($r->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
					$array[$prop->getName()] = $prop->getValue($this);
				}
				return $array;
			case 'url':
				$host = $this->host . ($this->port ? ":{$this->port}" : '');
				return "{$this->scheme}://{$host}{$this->path}";
			case 'context':
				$defaults = array('content' => $this->body(), 'ignore_errors' => true);
				return array($this->scheme => $options + $defaults);
			case 'string':
			default:
				return (string) $this;
		}
	}

	/**
	 * Magic method to convert object to string.
	 *
	 * @return string
	 */
	public function __toString() {
		return (string) $this->body();
	}
}

?>