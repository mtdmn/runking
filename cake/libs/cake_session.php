<?php
/**
 * Session class for Cake.
 *
 * Cake abstracts the handling of sessions.
 * There are several convenient methods to access session information.
 * This class is the implementation of those methods.
 * They are mostly used by the Session Component.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs
 * @since         CakePHP(tm) v .0.10.0.1222
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Session class for Cake.
 *
 * Cake abstracts the handling of sessions. There are several convenient methods to access session information.
 * This class is the implementation of those methods. They are mostly used by the Session Component.
 *
 * @package       cake
 * @subpackage    cake.cake.libs
 */
class CakeSession {

/**
 * True if the Session is still valid
 *
 * @var boolean
 */
	public static $valid = false;

/**
 * Error messages for this session
 *
 * @var array
 */
	public static $error = false;

/**
 * User agent string
 *
 * @var string
 */
	protected static $_userAgent = '';

/**
 * Path to where the session is active.
 *
 * @var string
 */
	public static $path = '/';

/**
 * Error number of last occurred error
 *
 * @var integer
 */
	public static $lastError = null;

/**
 * 'Security.level' setting, "high", "medium", or "low".
 *
 * @var string
 */
	public static $security = null;

/**
 * Start time for this session.
 *
 * @var integer
 */
	public static $time = false;

/**
 * Cookie lifetime
 *
 * @var integer
 */
	public static $cookieLifeTime;

/**
 * Time when this session becomes invalid.
 *
 * @var integer
 */
	public static $sessionTime = false;

/**
 * Keeps track of keys to watch for writes on
 *
 * @var array
 */
	public static $watchKeys = array();

/**
 * Current Session id
 *
 * @var string
 */
	public static $id = null;

/**
 * Hostname
 *
 * @var string
 */
	public static $host = null;

/**
 * Session timeout multiplier factor
 *
 * @var integer
 */
	public static $timeout = null;

/**
 * Constructor.
 *
 * @param string $base The base path for the Session
 * @param boolean $start Should session be started right now
 */
	public static function init($base = null, $start = true) {
		App::import('Core', array('Set', 'Security'));
		self::$time = time();

		$checkAgent = Configure::read('Session.checkAgent');
		if (($checkAgent === true || $checkAgent === null) && env('HTTP_USER_AGENT') != null) {
			self::$_userAgent = md5(env('HTTP_USER_AGENT') . Configure::read('Security.salt'));
		}

		self::_setupDatabase();
		if ($start === true) {
			self::_setPath($base);
			self::_setHost(env('HTTP_HOST'));
			self::start();
		}
		if (isset($_SESSION) || $start === true) {
			self::$sessionTime = self::$time + (Security::inactiveMins() * Configure::read('Session.timeout'));
			self::$security = Configure::read('Security.level');
		}
	}

/**
 * Setup the Path variable
 *
 * @param string $base base path
 * @return void
 */
	protected static function _setPath($base = null) {
		if (empty($base)) {
			self::$path = '/';
			return;
		}
		if (strpos($base, 'index.php') !== false) {
		   $base = str_replace('index.php', '', $base);
		}
		if (strpos($base, '?') !== false) {
		   $base = str_replace('?', '', $base);
		}
		self::$path = $base;
	}

/**
 * Set the host name
 *
 * @param string $host Hostname
 * @return void
 */
	protected static function _setHost($host) {
		self::$host = $host;
		if (strpos(self::$host, ':') !== false) {
			self::$host = substr(self::$host, 0, strpos(self::$host, ':'));
		}
	}

/**
 * Setup database configuration for Session, if enabled.
 *
 * @return void
 */
	protected function _setupDatabase() {
		if (Configure::read('Session.defaults') !== 'database') {
			return;
		}
		$modelName = Configure::read('Session.handler.model');
		$database = Configure::read('Session.handler.database');
		$table = Configure::read('Session.handler.table');

		if (empty($database)) {
			$database = 'default';
		}
		$settings = array(
			'class' => 'Session',
			'alias' => 'Session',
			'table' => 'cake_sessions',
			'ds' => $database
		);
		if (!empty($modelName)) {
			$settings['class'] = $modelName;
		}
		if (!empty($table)) {
			$settings['table'] = $table;
		}
		ClassRegistry::init($settings);
	}

/**
 * Starts the Session.
 *
 * @return boolean True if session was started
 */
	public static function start() {
		if (self::started()) {
			return true;
		}

		session_write_close();
		self::_configureSession();
		self::_startSession();
		$started = self::started();
		
		if (!self::id() && $started) {
			self::_checkValid();
		}

		self::$error = array();
		return self::started();
	}

/**
 * Determine if Session has been started.
 *
 * @return boolean True if session has been started.
 */
	public static function started() {
		return isset($_SESSION) && session_id();
	}

/**
 * Returns true if given variable is set in session.
 *
 * @param string $name Variable name to check for
 * @return boolean True if variable is there
 */
	public static function check($name = null) {
		if (empty($name)) {
			return false;
		}
		$result = Set::classicExtract($_SESSION, $name);
		return isset($result);
	}

/**
 * Returns the Session id
 *
 * @param id $name string
 * @return string Session id
 */
	public static function id($id = null) {
		if ($id) {
			self::$id = $id;
			session_id(self::$id);
		}
		if (self::started()) {
			return session_id();
		}
		return self::$id;
	}

/**
 * Removes a variable from session.
 *
 * @param string $name Session variable to remove
 * @return boolean Success
 */
	public static function delete($name) {
		if (self::check($name)) {
			if (in_array($name, self::$watchKeys)) {
				trigger_error(sprintf(__('Deleting session key {%s}'), $name), E_USER_NOTICE);
			}
			self::__overwrite($_SESSION, Set::remove($_SESSION, $name));
			return (self::check($name) == false);
		}
		self::__setError(2, sprintf(__("%s doesn't exist"), $name));
		return false;
	}

/**
 * Used to write new data to _SESSION, since PHP doesn't like us setting the _SESSION var itself
 *
 * @param array $old Set of old variables => values
 * @param array $new New set of variable => value
 * @access private
 */
	function __overwrite(&$old, $new) {
		if (!empty($old)) {
			foreach ($old as $key => $var) {
				if (!isset($new[$key])) {
					unset($old[$key]);
				}
			}
		}
		foreach ($new as $key => $var) {
			$old[$key] = $var;
		}
	}

/**
 * Return error description for given error number.
 *
 * @param integer $errorNumber Error to set
 * @return string Error as string
 * @access private
 */
	function __error($errorNumber) {
		if (!is_array(self::$error) || !array_key_exists($errorNumber, self::$error)) {
			return false;
		} else {
			return self::$error[$errorNumber];
		}
	}

/**
 * Returns last occurred error as a string, if any.
 *
 * @return mixed Error description as a string, or false.
 */
	public static function error() {
		if (self::$lastError) {
			return self::__error(self::$lastError);
		}
		return false;
	}

/**
 * Returns true if session is valid.
 *
 * @return boolean Success
 */
	public static function valid() {
		if (self::read('Config')) {
			$validAgent = (
				Configure::read('Session.checkAgent') === false || 
				self::$_userAgent == self::read('Config.userAgent')
			);
			if ($validAgent && self::$time <= self::read('Config.time')) {
				if (self::$error === false) {
					self::$valid = true;
				}
			} else {
				self::$valid = false;
				self::__setError(1, 'Session Highjacking Attempted !!!');
			}
		}
		return self::$valid;
	}

/**
 * Get / Set the userAgent 
 *
 * @param string $userAgent Set the userAgent
 * @return void
 */
	public static function userAgent($userAgent = null) {
		if ($userAgent) {
			self::$_userAgent = $userAgent;
		}
		return self::$_userAgent;
	}

/**
 * Returns given session variable, or all of them, if no parameters given.
 *
 * @param mixed $name The name of the session variable (or a path as sent to Set.extract)
 * @return mixed The value of the session variable
 */
	public static function read($name = null) {
		if (is_null($name)) {
			return self::__returnSessionVars();
		}
		if (empty($name)) {
			return false;
		}
		$result = Set::classicExtract($_SESSION, $name);

		if (!is_null($result)) {
			return $result;
		}
		self::__setError(2, "$name doesn't exist");
		return null;
	}

/**
 * Returns all session variables.
 *
 * @return mixed Full $_SESSION array, or false on error.
 * @access private
 */
	function __returnSessionVars() {
		if (!empty($_SESSION)) {
			return $_SESSION;
		}
		self::__setError(2, 'No Session vars set');
		return false;
	}

/**
 * Tells Session to write a notification when a certain session path or subpath is written to
 *
 * @param mixed $var The variable path to watch
 * @return void
 */
	public static function watch($var) {
		if (empty($var)) {
			return false;
		}
		if (!in_array($var, self::$watchKeys, true)) {
			self::$watchKeys[] = $var;
		}
	}

/**
 * Tells Session to stop watching a given key path
 *
 * @param mixed $var The variable path to watch
 * @return void
 */
	public static function ignore($var) {
		if (!in_array($var, self::$watchKeys)) {
			debug("NOT");
			return;
		}
		foreach (self::$watchKeys as $i => $key) {
			if ($key == $var) {
				unset(self::$watchKeys[$i]);
				self::$watchKeys = array_values(self::$watchKeys);
				return;
			}
		}
	}

/**
 * Writes value to given session variable name.
 *
 * @param mixed $name Name of variable
 * @param string $value Value to write
 * @return boolean True if the write was successful, false if the write failed
 */
	public static function write($name, $value = null) {
		if (empty($name)) {
			return false;
		}
		$write = $name;
		if (!is_array($name)) {
			$write = array($name => $value);
		}
		foreach ($write as $key => $val) {
			if (in_array($key, self::$watchKeys)) {
				trigger_error(sprintf(__('Writing session key {%s}: %s'), $key, Debugger::exportVar($val)), E_USER_NOTICE);
			}
			self::__overwrite($_SESSION, Set::insert($_SESSION, $key, $val));
			if (Set::classicExtract($_SESSION, $key) !== $val) {
				return false;
			}
		}
		return true;
	}

/**
 * Helper method to destroy invalid sessions.
 *
 * @return void
 */
	public static function destroy() {
		$_SESSION = array();
		self::$id = null;
		self::init(self::$path);
		self::start();
		self::renew();
		self::_checkValid();
	}

/**
 * Helper method to initialize a session, based on Cake core settings.
 *
 * Sessions can be configured with a few shortcut names as well as have any number of ini settings declared.
 * 
 * ## Options
 *
 * - `Session.name` - The name of the cookie to use. Defaults to 'CAKEPHP'
 * - `Session.timeout` - The number of minutes you want sessions to live for. This timeout is handled by CakePHP
 * - `Session.cookieTimeout` - The number of minutes you want session cookies to live for.
 * - `Session.checkAgent` - Do you want the user agent to be checked when starting sessions?
 * - `Session.defaults` - The default configuration set to use as a basis for your session.
 *    There are four builtins: php, cake, cache, database.
 * - `Session.handler` - Can be used to enable a custom session handler.  Expects an array of of callables,
 *    that can be used with `session_save_handler`.  Using this option will automatically add `session.save_handler`
 *    to the ini array.
 * - `Session.ini` - An associative array of additional ini values to set.
 *
 * @return void
 * @throws Exception Throws exceptions when ini_set() fails.
 */
	protected static function _configureSession() {
		$sessionConfig = Configure::read('Session');
		$iniSet = function_exists('ini_set');

		if (isset($sessionConfig['defaults'])) {
			$defaults = self::_defaultConfig($sessionConfig['defaults']);
			if ($defaults) {
				$sessionConfig = Set::merge($defaults, $sessionConfig);
			}
		}
		if (!isset($sessionConfig['ini']['session.cookie_secure']) && env('HTTPS')) {
			$sessionConfig['ini']['session.cookie_secure'] = 1;
		}
		if (isset($sessionConfig['timeout']) && !isset($sessionConfig['cookieTimeout'])) {
			$sessionConfig['cookieTimeout'] = $sessionConfig['timeout'];
		}
		if (!isset($sessionConfig['ini']['session.cookie_lifetime'])) {
			$sessionConfig['ini']['session.cookie_lifetime'] = $sessionConfig['cookieTimeout'] * 60;
		}
		if (!isset($sessionConfig['ini']['session.name'])) {
			$sessionConfig['ini']['session.name'] = $sessionConfig['cookie'];
		}
		if (!empty($sessionConfig['handler'])) {
			$sessionConfig['ini']['session.save_handler'] = 'user';
		}

		if (empty($_SESSION)) {
			if (!empty($sessionConfig['ini']) && is_array($sessionConfig['ini'])) {
				foreach ($sessionConfig['ini'] as $setting => $value) {
					if (ini_set($setting, $value) === false) {
						throw new Exception(sprintf(
							__('Unable to configure the session, setting %s failed.'),
							$setting
						));
					}
				}
			}
		}
		if (!empty($sessionConfig['handler']) && !isset($sessionConfig['handler']['engine'])) {
			call_user_func_array('session_set_save_handler', $sessionConfig['handler']);
		}
		if (!empty($sessionConfig['handler']['engine'])) {
			$class = self::_getHandler($sessionConfig['handler']['engine']);
			session_set_save_handler(
				array($class, 'open'),
				array($class, 'close'),
				array($class, 'read'),
				array($class, 'write'),
				array($class, 'destroy'),
				array($class, 'gc')
			);
		}
	}

/**
 * Find the handler class and make sure it implements the correct interface.
 *
 * @return void
 */
	protected static function _getHandler($handler) {
		$class = $handler;
		$found = App::import('Lib', 'session/' . $class);
		if (!$found) {
			App::import('Core', 'session/' . $class);
		}
		if (!class_exists($class)) {
			throw new Exception(sprintf(__('Could not load %s to handle the session.'), $class));
		}
		$reflect = new ReflectionClass($class);
		if (!$reflect->implementsInterface('CakeSessionHandlerInterface')) {
			throw new Exception(__('Chosen SessionHandler does not implement CakeSessionHandlerInterface'));
		}
		return $class;
	}

/**
 * Get one of the prebaked default session configurations.
 *
 * @return void
 */
	protected static function _defaultConfig($name) {
		$defaults = array(
			'php' => array(
				'cookie' => 'CAKEPHP',
				'timeout' => 240,
				'cookieTimeout' => 240,
				'ini' => array(
					'session.use_trans_sid' => 0,
					'session.cookie_path' => self::$path,
					'session.save_handler' => 'files'
				)
			),
			'cake' => array(
				'cookie' => 'CAKEPHP',
				'timeout' => 240,
				'cookieTimeout' => 240,
				'ini' => array(
					'session.use_trans_sid' => 0,
					'url_rewriter.tags' => '',
					'session.serialize_handler' => 'php',
					'session.use_cookies' => 1,
					'session.cookie_path' => self::$path,
					'session.auto_start' => 0,
					'session.save_path' => TMP . 'sessions',
					'session.save_handler' => 'files'
				)
			),
			'cache' => array(
				'cookie' => 'CAKEPHP',
				'timeout' => 240,
				'cookieTimeout' => 240,
				'ini' => array(
					'session.use_trans_sid' => 0,
					'url_rewriter.tags' => '',
					'session.auto_start' => 0,
					'session.use_cookies' => 1,
					'session.cookie_path' => self::$path,
					'session.save_handler' => 'user',
				),
				'handler' => array(
					'engine' => 'CacheSession',
					'config' => 'default'
				)
			),
			'database' => array(
				'cookie' => 'CAKEPHP',
				'timeout' => 240,
				'cookieTimeout' => 240,
				'ini' => array(
					'session.use_trans_sid' => 0,
					'url_rewriter.tags' => '',
					'session.auto_start' => 0,
					'session.use_cookies' => 1,
					'session.cookie_path' => self::$path,
					'session.save_handler' => 'user',
					'session.serialize_handler' => 'php',
				),
				'handler' => array(
					'engine' => 'DatabaseSession',
					'model' => 'Session'
				)
			)
		);
		if (isset($defaults[$name])) {
			return $defaults[$name];
		}
		return false;
	}

/**
 * Helper method to start a session
 *
 * @return boolean Success
 */
	protected function _startSession() {
		if (headers_sent()) {
			if (empty($_SESSION)) {
				$_SESSION = array();
			}
		} elseif (!isset($_SESSION)) {
			session_cache_limiter ("must-revalidate");
			session_start();
			header ('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		} else {
			session_start();
		}
		return true;
	}

/**
 * Helper method to create a new session.
 *
 * @return void
 */
	protected static function _checkValid() {
		if (self::read('Config')) {
			if ((Configure::read('Session.checkAgent') === false || self::$_userAgent == self::read('Config.userAgent')) && self::$time <= self::read('Config.time')) {
				$time = self::read('Config.time');
				self::write('Config.time', self::$sessionTime);
				if (Configure::read('Security.level') === 'high') {
					$check = self::read('Config.timeout');
					$check -= 1;
					self::write('Config.timeout', $check);

					if (time() > ($time - (Security::inactiveMins() * Configure::read('Session.timeout')) + 2) || $check < 1) {
						self::renew();
						self::write('Config.timeout', Security::inactiveMins());
					}
				}
				self::$valid = true;
			} else {
				self::destroy();
				self::$valid = false;
				self::__setError(1, 'Session Highjacking Attempted !!!');
			}
		} else {
			self::write('Config.userAgent', self::$_userAgent);
			self::write('Config.time', self::$sessionTime);
			self::write('Config.timeout', Security::inactiveMins());
			self::$valid = true;
			self::__setError(1, 'Session is valid');
		}
	}

/**
 * Restarts this session.
 *
 * @return void
 */
	public static function renew() {
		if (session_id()) {
			if (session_id() != '' || isset($_COOKIE[session_name()])) {
				setcookie(Configure::read('Session.cookie'), '', time() - 42000, self::$path);
			}
			session_regenerate_id(true);
		}
	}

/**
 * Helper method to set an internal error message.
 *
 * @param integer $errorNumber Number of the error
 * @param string $errorMessage Description of the error
 * @return void
 * @access private
 */
	function __setError($errorNumber, $errorMessage) {
		if (self::$error === false) {
			self::$error = array();
		}
		self::$error[$errorNumber] = $errorMessage;
		self::$lastError = $errorNumber;
	}
}


/**
 * Interface for Session handlers.  Custom session handler classes should implement
 * this interface as it allows CakeSession know how to map methods to session_set_save_handler()
 *
 * @package cake.libs
 */
interface CakeSessionHandlerInterface {
/**
 * Method called on open of a session.
 *
 * @return boolean Success
 */
	public static function open();

/**
 * Method called on close of a session.
 *
 * @return boolean Success
 */
	public static function close();

/**
 * Method used to read from a session.
 *
 * @param mixed $id The key of the value to read
 * @return mixed The value of the key or false if it does not exist
 */
	public static function read($id);

/**
 * Helper function called on write for sessions.
 *
 * @param integer $id ID that uniquely identifies session in database
 * @param mixed $data The value of the data to be saved.
 * @return boolean True for successful write, false otherwise.
 */
	public static function write($id, $data);

/**
 * Method called on the destruction of a session.
 *
 * @param integer $id ID that uniquely identifies session in database
 * @return boolean True for successful delete, false otherwise.
 */
	public static function destroy($id);

/**
 * Run the Garbage collection on the session storage.  This method should vacuum all
 * expired or dead sessions.
 *
 * @param integer $expires Timestamp (defaults to current time)
 * @return boolean Success
 */
	public static function gc($expires = null);
}


// Initialize the session
CakeSession::init();
