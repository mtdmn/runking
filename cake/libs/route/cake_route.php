<?php
/**
 * A single Route used by the Router to connect requests to
 * parameter maps.
 *
 * Not normally created as a standalone.  Use Router::connect() to create
 * Routes for your application.
 *
 * PHP5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake.libs.route
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class CakeRoute {

/**
 * An array of named segments in a Route.
 * `/:controller/:action/:id` has 3 key elements
 *
 * @var array
 * @access public
 */
	public $keys = array();

/**
 * An array of additional parameters for the Route.
 *
 * @var array
 * @access public
 */
	public $options = array();

/**
 * Default parameters for a Route
 *
 * @var array
 * @access public
 */
	public $defaults = array();

/**
 * The routes template string.
 *
 * @var string
 * @access public
 */
	public $template = null;

/**
 * Is this route a greedy route?  Greedy routes have a `/*` in their
 * template
 *
 * @var string
 * @access protected
 */
	protected $_greedy = false;

/**
 * The compiled route regular expresssion
 *
 * @var string
 * @access protected
 */
	protected $_compiledRoute = null;

/**
 * HTTP header shortcut map.  Used for evaluating header-based route expressions.
 *
 * @var array
 * @access private
 */
	private $__headerMap = array(
		'type' => 'content_type',
		'method' => 'request_method',
		'server' => 'server_name'
	);

/**
 * Constructor for a Route
 *
 * @param string $template Template string with parameter placeholders
 * @param array $defaults Array of defaults for the route.
 * @param string $params Array of parameters and additional options for the Route
 * @return void
 */
	public function __construct($template, $defaults = array(), $options = array()) {
		$this->template = $template;
		$this->defaults = (array)$defaults;
		$this->options = (array)$options;
	}

/**
 * Check if a Route has been compiled into a regular expression.
 *
 * @return boolean
 */
	public function compiled() {
		return !empty($this->_compiledRoute);
	}

/**
 * Compiles the route's regular expression.  Modifies defaults property so all necessary keys are set
 * and populates $this->names with the named routing elements.
 *
 * @return array Returns a string regular expression of the compiled route.
 */
	public function compile() {
		if ($this->compiled()) {
			return $this->_compiledRoute;
		}
		$this->_writeRoute();
		return $this->_compiledRoute;
	}

/**
 * Builds a route regular expression.  Uses the template, defaults and options
 * properties to compile a regular expression that can be used to parse request strings.
 *
 * @return void
 */
	protected function _writeRoute() {
		if (empty($this->template) || ($this->template === '/')) {
			$this->_compiledRoute = '#^/*$#';
			$this->keys = array();
			return;
		}
		$route = $this->template;
		$names = $routeParams = array();
		$parsed = preg_quote($this->template, '#');

		preg_match_all('#:([A-Za-z0-9_-]+[A-Z0-9a-z])#', $route, $namedElements);
		foreach ($namedElements[1] as $i => $name) {
			$search = '\\' . $namedElements[0][$i];
			if (isset($this->options[$name])) {
				$option = null;
				if ($name !== 'plugin' && array_key_exists($name, $this->defaults)) {
					$option = '?';
				}
				$slashParam = '/\\' . $namedElements[0][$i];
				if (strpos($parsed, $slashParam) !== false) {
					$routeParams[$slashParam] = '(?:/(?P<' . $name . '>' . $this->options[$name] . ')' . $option . ')' . $option;
				} else {
					$routeParams[$search] = '(?:(?P<' . $name . '>' . $this->options[$name] . ')' . $option . ')' . $option;
				}
			} else {
				$routeParams[$search] = '(?:(?P<' . $name . '>[^/]+))';
			}
			$names[] = $name;
		}
		if (preg_match('#\/\*$#', $route, $m)) {
			$parsed = preg_replace('#/\\\\\*$#', '(?:/(?P<_args_>.*))?', $parsed);
			$this->_greedy = true;
		}
		krsort($routeParams);
		$parsed = str_replace(array_keys($routeParams), array_values($routeParams), $parsed);
		$this->_compiledRoute = '#^' . $parsed . '[/]*$#';
		$this->keys = $names;

		//remove defaults that are also keys. They can cause match failures
		foreach ($this->keys as $key) {
			unset($this->defaults[$key]);
		}
	}

/**
 * Checks to see if the given URL can be parsed by this route.
 * If the route can be parsed an array of parameters will be returned; if not
 * false will be returned. String urls are parsed if they match a routes regular expression.
 *
 * @param string $url The url to attempt to parse.
 * @return mixed Boolean false on failure, otherwise an array or parameters
 */
	public function parse($url) {
		if (!$this->compiled()) {
			$this->compile();
		}
		if (!preg_match($this->_compiledRoute, $url, $route)) {
			return false;
		}
		foreach ($this->defaults as $key => $val) {
			if ($key[0] === '[' && preg_match('/^\[(\w+)\]$/', $key, $header)) {
				if (isset($this->__headerMap[$header[1]])) {
					$header = $this->__headerMap[$header[1]];
				} else {
					$header = 'http_' . $header[1];
				}
				$header = strtoupper($header);

				$val = (array)$val;
				$h = false;

				foreach ($val as $v) {
					if (env($header) === $v) {
						$h = true;
					}
				}
				if (!$h) {
					return false;
				}
			}
		}
		array_shift($route);
		$count = count($this->keys);
		for ($i = 0; $i <= $count; $i++) {
			unset($route[$i]);
		}
		$route['pass'] = $route['named'] = array();

		// Assign defaults, set passed args to pass
		foreach ($this->defaults as $key => $value) {
			if (isset($route[$key])) {
				continue;
			}
			if (is_integer($key)) {
				$route['pass'][] = $value;
				continue;
			}
			$route[$key] = $value;
		}
		
		if (isset($route['_args_'])) {
			list($pass, $named) = $this->parseArgs($route['_args_'], $route['controller'], $route['action']);
			$route['pass'] = array_merge($route['pass'], $pass);
			$route['named'] = $named;
			unset($route['_args_']);
		}

		// restructure 'pass' key route params
		if (isset($this->options['pass'])) {
			$j = count($this->options['pass']);
			while($j--) {
				if (isset($route[$this->options['pass'][$j]])) {
					array_unshift($route['pass'], $route[$this->options['pass'][$j]]);
				}
			}
		}
		return $route;
	}

/**
 * Parse passed and Named parameters into a list of passed args, and a hash of named parameters.
 * The local and global configuration for named parameters will be used.
 *
 * @param string $args A string with the passed & named params.  eg. /1/page:2
 * @param string $controller The current actions controller name, used to match named parameter rules
 * @param string $action The current action name, used to match named parameter rules.
 * @return array Array of ($pass, $named)
 */
	public function parseArgs($args, $controller = null, $action = null) {
		$pass = $named = array();
		$args = explode('/', $args);

		$context = compact('controller', 'action');
		$namedConfig = Router::namedConfig();
		$greedy = $namedConfig['greedyNamed'];
		$rules = $namedConfig['rules'];
		if (!empty($this->options['named'])) {
			$greedy = isset($this->options['greedyNamed']) && $this->options['greedyNamed'] === true;
			foreach ((array)$this->options['named'] as $key => $val) {
				if (is_numeric($key)) {
					$rules[$val] = true;
					continue;
				}
				$rules[$key] = $val;
			}
		}

		foreach ($args as $param) {
			if (empty($param) && $param !== '0' && $param !== 0) {
				continue;
			}

			$separatorIsPresent = strpos($param, $namedConfig['separator']) !== false;
			if ((!isset($this->options['named']) || !empty($this->options['named'])) && $separatorIsPresent) {
				list($key, $val) = explode($namedConfig['separator'], $param, 2);
				$hasRule = isset($rules[$key]);
				$passIt = (!$hasRule && !$greedy) || ($hasRule && !$this->_matchNamed($key, $val, $rules[$key], $context));
				if ($passIt) {
					$pass[] = $param;
				} else {
					if (preg_match_all('/\[([A-Za-z0-9_-]+)?\]/', $key, $matches, PREG_SET_ORDER)) {
						$matches = array_reverse($matches);
						$parts = explode('[', $key);
						$key = array_shift($parts);
						$arr = $val;
						foreach ($matches as $match) {
							if (empty($match[1])) {
								$arr = array($arr);
							} else {
								$arr = array(
									$match[1] => $arr
								);
							}
						}
						$val = $arr;
					}
					$named = array_merge_recursive($named, array($key => $val));
				}
			} else {
				$pass[] = $param;
			}
		}
		return array($pass, $named);
	}

/**
 * Return true if a given named $param's $val matches a given $rule depending on $context. Currently implemented
 * rule types are controller, action and match that can be combined with each other.
 *
 * @param string $param The name of the named parameter
 * @param string $val The value of the named parameter
 * @param array $rule The rule(s) to apply, can also be a match string
 * @param string $context An array with additional context information (controller / action)
 * @return boolean
 */
	protected function _matchNamed($param, $val, $rule, $context) {
		if ($rule === true || $rule === false) {
			return $rule;
		}
		if (is_string($rule)) {
			$rule = array('match' => $rule);
		}
		if (!is_array($rule)) {
			return false;
		}

		$controllerMatches = (
			!isset($rule['controller'], $context['controller']) || 
			in_array($context['controller'], (array)$rule['controller'])
		);
		if (!$controllerMatches) {
			return false;
		}
		$actionMatches = (
			!isset($rule['action'], $context['action']) ||
			in_array($context['action'], (array)$rule['action'])
		);
		if (!$actionMatches) {
			return false;
		}
		return (!isset($rule['match']) || preg_match('/' . $rule['match'] . '/', $val));
	}

/**
 * Apply persistent parameters to a url array. Persistant parameters are a special 
 * key used during route creation to force route parameters to persist when omitted from 
 * a url array.
 *
 * @param array $url The array to apply persistent parameters to.
 * @param array $params An array of persistent values to replace persistent ones.
 * @return array An array with persistent parameters applied.
 */
	public function persistParams($url, $params) {
		foreach ($this->options['persist'] as $persistKey) {
			if (array_key_exists($persistKey, $params) && !isset($url[$persistKey])) {
				$url[$persistKey] = $params[$persistKey];
			}
		}
		return $url;
	}

/**
 * Attempt to match a url array.  If the url matches the route parameters and settings, then
 * return a generated string url.  If the url doesn't match the route parameters, false will be returned.
 * This method handles the reverse routing or conversion of url arrays into string urls.
 *
 * @param array $url An array of parameters to check matching with.
 * @return mixed Either a string url for the parameters if they match or false.
 */
	public function match($url) {
		if (!$this->compiled()) {
			$this->compile();
		}
		$defaults = $this->defaults;

		if (isset($defaults['prefix'])) {
			$url['prefix'] = $defaults['prefix'];
		}

		//check that all the key names are in the url
		$keyNames = array_flip($this->keys);
		if (array_intersect_key($keyNames, $url) !== $keyNames) {
			return false;
		}

		// Missing defaults is a fail.
		if (array_diff_key($defaults, $url) !== array()) {
			return false;
		}

		$namedConfig = Router::namedConfig();
		$greedyNamed = $namedConfig['greedyNamed'];
		$allowedNamedParams = $namedConfig['rules'];

		$named = $pass = $_query = array();

		foreach ($url as $key => $value) {

			// keys that exist in the defaults and have different values is a match failure.
			$defaultExists = array_key_exists($key, $defaults);
			if ($defaultExists && $defaults[$key] != $value) {
				return false;
			} elseif ($defaultExists) {
				continue;
			}
			
			// If the key is a routed key, its not different yet.
			if (array_key_exists($key, $keyNames)) {
				continue;
			}

			// pull out passed args
			$numeric = is_numeric($key);
			if ($numeric && isset($defaults[$key]) && $defaults[$key] == $value) {
				continue;
			} elseif ($numeric) {
				$pass[] = $value;
				unset($url[$key]);
				continue;
			}

			// pull out named params if named params are greedy or a rule exists.
			if (
				($greedyNamed || isset($allowedNamedParams[$key])) &&
				($value !== false && $value !== null)
			) {
				$named[$key] = $value;
				continue;
			}

			// keys that don't exist are different.
			if (!$defaultExists && !empty($value)) {
				return false;
			}
		}

		//if a not a greedy route, no extra params are allowed.
		if (!$this->_greedy && (!empty($pass) || !empty($named))) {
			return false;
		}

		//check patterns for routed params
		if (!empty($this->options)) {
			foreach ($this->options as $key => $pattern) {
				if (array_key_exists($key, $url) && !preg_match('#^' . $pattern . '$#', $url[$key])) {
					return false;
				}
			}
		}
		return $this->_writeUrl(array_merge($url, compact('pass', 'named')));
	}

/**
 * Converts a matching route array into a url string. Composes the string url using the template
 * used to create the route.
 *
 * @param array $params The params to convert to a string url.
 * @return string Composed route string.
 */
	protected function _writeUrl($params) {
		if (isset($params['prefix'], $params['action'])) {
			$params['action'] = str_replace($params['prefix'] . '_', '', $params['action']);
			unset($params['prefix']);
		}

		if (is_array($params['pass'])) {
			$params['pass'] = implode('/', $params['pass']);
		}

		$namedConfig = Router::namedConfig();
		$separator = $namedConfig['separator'];

		if (!empty($params['named']) && is_array($params['named'])) {
			$named = array();
			foreach ($params['named'] as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $namedKey => $namedValue) {
						$named[] = $key . "[$namedKey]" . $separator . $namedValue;
					}
				} else {
					$named[] = $key . $separator . $value;
				}
			}
			$params['pass'] = $params['pass'] . '/' . implode('/', $named);
		}
		$out = $this->template;

		$search = $replace = array();
		foreach ($this->keys as $key) {
			$string = null;
			if (isset($params[$key])) {
				$string = $params[$key];
			} elseif (strpos($out, $key) != strlen($out) - strlen($key)) {
				$key .= '/';
			}
			$search[] = ':' . $key;
			$replace[] = $string;
		}
		$out = str_replace($search, $replace, $out);

		if (strpos($this->template, '*')) {
			$out = str_replace('*', $params['pass'], $out);
		}
		$out = str_replace('//', '/', $out);
		return $out;
	}

}