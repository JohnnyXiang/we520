<?php
/**
 * Bcrypt algorithm using crypt() function of PHP
 */
class Bcrypt
{
	const MIN_SALT_SIZE = 16;

	/**
	 * @var string
	 *
	 * Changed from 14 to 10 to prevent possibile DOS attacks
	 * due to the high computational time
	 * @see http://timoh6.github.io/2013/11/26/Aggressive-password-stretching.html
	 */
	protected $cost = '10';

	/**
	 * @var string
	 */
	protected $salt;

	/**
	 * @var bool
	 */
	protected $backwardCompatibility = false;

	/**
	 * Constructor
	 *
	 * @param array|Traversable $options
	 * @throws Exception\InvalidArgumentException
	 */
	public function __construct($options = array())
	{
		if (!empty($options)) {
			if ($options instanceof Traversable) {
				$options = $this->iteratorToArray($options);
			} elseif (!is_array($options)) {
				throw new Exception(
						'The options parameter must be an array or a Traversable'
				);
			}
			foreach ($options as $key => $value) {
				switch (strtolower($key)) {
					case 'salt':
						$this->setSalt($value);
						break;
					case 'cost':
						$this->setCost($value);
						break;
				}
			}
		}
	}

	/**
	 * Bcrypt
	 *
	 * @param  string $password
	 * @throws Exception
	 * @return string
	 */
	public function create($password)
	{
		if (empty($this->salt)) {
			$salt = $this->getBytes(self::MIN_SALT_SIZE);
		} else {
			$salt = $this->salt;
		}
		$salt64 = substr(str_replace('+', '.', base64_encode($salt)), 0, 22);
		/**
		 * Check for security flaw in the bcrypt implementation used by crypt()
		 * @see http://php.net/security/crypt_blowfish.php
		*/
		if ((PHP_VERSION_ID >= 50307) && !$this->backwardCompatibility) {
			$prefix = '$2y$';
		} else {
			$prefix = '$2a$';
			// check if the password contains 8-bit character
			if (preg_match('/[\x80-\xFF]/', $password)) {
				throw new Exception(
						'The bcrypt implementation used by PHP can contain a security flaw ' .
						'using password with 8-bit character. ' .
						'We suggest to upgrade to PHP 5.3.7+ or use passwords with only 7-bit characters'
				);
			}
		}
		$hash = crypt($password, $prefix . $this->cost . '$' . $salt64);
		if (strlen($hash) < 13) {
			throw new Exception('Error during the bcrypt generation');
		}
		return $hash;
	}

	/**
	 * Verify if a password is correct against a hash value
	 *
	 * @param  string $password
	 * @param  string $hash
	 * @throws Exception\RuntimeException when the hash is unable to be processed
	 * @return bool
	 */
	public function verify($password, $hash)
	{
		$result = crypt($password, $hash);
		if ($result === $hash) {
			return true;
		}
		return false;
	}

	/**
	 * Set the cost parameter
	 *
	 * @param  int|string $cost
	 * @throws Exception\InvalidArgumentException
	 * @return Bcrypt
	 */
	public function setCost($cost)
	{
		if (!empty($cost)) {
			$cost = (int) $cost;
			if ($cost < 4 || $cost > 31) {
				throw new Exception(
						'The cost parameter of bcrypt must be in range 04-31'
				);
			}
			$this->cost = sprintf('%1$02d', $cost);
		}
		return $this;
	}

	/**
	 * Get the cost parameter
	 *
	 * @return string
	 */
	public function getCost()
	{
		return $this->cost;
	}

	/**
	 * Set the salt value
	 *
	 * @param  string $salt
	 * @throws Exception\InvalidArgumentException
	 * @return Bcrypt
	 */
	public function setSalt($salt)
	{
		if (strlen($salt) < self::MIN_SALT_SIZE) {
			throw new Exception(
					'The length of the salt must be at least ' . self::MIN_SALT_SIZE . ' bytes'
			);
		}
		$this->salt = $salt;
		return $this;
	}

	/**
	 * Get the salt value
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Set the backward compatibility $2a$ instead of $2y$ for PHP 5.3.7+
	 *
	 * @param bool $value
	 * @return Bcrypt
	 */
	public function setBackwardCompatibility($value)
	{
		$this->backwardCompatibility = (bool) $value;
		return $this;
	}

	/**
	 * Get the backward compatibility
	 *
	 * @return bool
	 */
	public function getBackwardCompatibility()
	{
		return $this->backwardCompatibility;
	}
	
	
	
	/**
	 * Convert an iterator to an array.
	 *
	 * Converts an iterator to an array. The $recursive flag, on by default,
	 * hints whether or not you want to do so recursively.
	 *
	 * @param  array|Traversable  $iterator     The array or Traversable object to convert
	 * @param  bool               $recursive    Recursively check all nested structures
	 * @throws Exception if $iterator is not an array or a Traversable object
	 * @return array
	 */
	public function iteratorToArray($iterator, $recursive = true)
	{
		if (!is_array($iterator) && !$iterator instanceof Traversable) {
			throw new Exception(__METHOD__ . ' expects an array or Traversable object');
		}
	
		if (!$recursive) {
			if (is_array($iterator)) {
				return $iterator;
			}
	
			return iterator_to_array($iterator);
		}
	
		if (method_exists($iterator, 'toArray')) {
			return $iterator->toArray();
		}
	
		$array = array();
		foreach ($iterator as $key => $value) {
			if (is_scalar($value)) {
				$array[$key] = $value;
				continue;
			}
	
			if ($value instanceof Traversable) {
				$array[$key] = $this->iteratorToArray($value, $recursive);
				continue;
			}
	
			if (is_array($value)) {
				$array[$key] = $this->iteratorToArray($value, $recursive);
				continue;
			}
	
			$array[$key] = $value;
		}
	
		return $array;
	}
	
	/**
	 * Generate random bytes using OpenSSL or Mcrypt and mt_rand() as fallback
	 *
	 * @param  int $length
	 * @param  bool $strong true if you need a strong random generator (cryptography)
	 * @return string
	 * @throws Exception
	 */
	public function getBytes($length, $strong = false)
	{
		$length = (int) $length;
	
		if ($length <= 0) {
			return false;
		}
	
		if (function_exists('openssl_random_pseudo_bytes')
				&& ((PHP_VERSION_ID >= 50304)
						|| strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		) {
			$bytes = openssl_random_pseudo_bytes($length, $usable);
			if (true === $usable) {
				return $bytes;
			}
		}
		if (function_exists('mcrypt_create_iv')
				&& ((PHP_VERSION_ID >= 50307)
						|| strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		) {
			$bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
			if ($bytes !== false && strlen($bytes) === $length) {
				return $bytes;
			}
		}
		
		return false;
		
	}
}