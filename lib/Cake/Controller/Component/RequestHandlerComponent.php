<?php
/**
 * Request object for handling alternative HTTP requests
 *
 * Alternative HTTP requests can come from wireless units like mobile phones, palmtop computers,
 * and the like.  These units have no use for Ajax requests, and this Component can tell how Cake
 * should respond to the different needs of a handheld computer and a desktop machine.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller.Component
 * @since         CakePHP(tm) v 0.10.4.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Xml', 'Utility');

/**
 * Request object for handling HTTP requests
 *
 * @package       Cake.Controller.Component
 * @link http://book.cakephp.org/view/1291/Request-Handling
 *
 */
class RequestHandlerComponent extends Component {

/**
 * The layout that will be switched to for Ajax requests
 *
 * @var string
 * @see RequestHandler::setAjax()
 */
	public $ajaxLayout = 'ajax';

/**
 * Determines whether or not callbacks will be fired on this component
 *
 * @var boolean
 */
	public $enabled = true;

/**
 * Holds the reference to Controller::$request
 *
 * @var CakeRequest
 */
	public $request;

/**
 * Holds the reference to Controller::$response
 *
 * @var CakeResponse
 */
	public $response;

/**
 * Contains the file extension parsed out by the Router
 *
 * @var string
 * @see Router::parseExtensions()
 */
	public $ext = null;

/**
 * The template to use when rendering the given content type.
 *
 * @var string
 */
	private $__renderType = null;

/**
 * A mapping between extensions and deserializers for request bodies of that type.
 * By default only JSON and XML are mapped, use RequestHandlerComponent::addInputType()
 *
 * @var array
 */
	private $__inputTypeMap = array(
		'json' => array('json_decode', true)
	);

/**
 * Constructor. Parses the accepted content types accepted by the client using HTTP_ACCEPT
 *
 * @param ComponentCollection $collection ComponentCollection object.
 * @param array $settings Array of settings.
 */
	function __construct(ComponentCollection $collection, $settings = array()) {
		$this->addInputType('xml', array(array($this, '_convertXml')));
		parent::__construct($collection, $settings);
	}

/**
 * Initializes the component, gets a reference to Controller::$parameters, and
 * checks to see if a file extension has been parsed by the Router.  Or if the 
 * HTTP_ACCEPT_TYPE is set to a single value that is a supported extension and mapped type.
 * If yes, RequestHandler::$ext is set to that value
 *
 * @param object $controller A reference to the controller
 * @param array $settings Array of settings to _set().
 * @return void
 * @see Router::parseExtensions()
 */
	public function initialize($controller, $settings = array()) {
		$this->request = $controller->request;
		$this->response = $controller->response;
		if (isset($this->request->params['url']['ext'])) {
			$this->ext = $this->request->params['url']['ext'];
		}
		if (empty($this->ext) || $this->ext == 'html') {
			$accepts = $this->request->accepts();
			$extensions = Router::extensions();
			if (count($accepts) == 1) {
				$mapped = $this->mapType($accepts[0]);
				if (in_array($mapped, $extensions)) {
					$this->ext = $mapped;
				}
			}
		}
		$this->params = $controller->params;
		$this->_set($settings);
	}

/**
 * The startup method of the RequestHandler enables several automatic behaviors
 * related to the detection of certain properties of the HTTP request, including:
 *
 * - Disabling layout rendering for Ajax requests (based on the HTTP_X_REQUESTED_WITH header)
 * - If Router::parseExtensions() is enabled, the layout and template type are
 *   switched based on the parsed extension or Accept-Type header.  For example, if `controller/action.xml`
 *   is requested, the view path becomes `app/View/Controller/xml/action.ctp`. Also if
 *   `controller/action` is requested with `Accept-Type: application/xml` in the headers
 *   the view path will become `app/View/Controller/xml/action.ctp`.
 * - If a helper with the same name as the extension exists, it is added to the controller.
 * - If the extension is of a type that RequestHandler understands, it will set that
 *   Content-type in the response header.
 * - If the XML data is POSTed, the data is parsed into an XML object, which is assigned
 *   to the $data property of the controller, which can then be saved to a model object.
 *
 * @param object $controller A reference to the controller
 * @return void
 */
	public function startup($controller) {
		$controller->request->params['isAjax'] = $this->request->is('ajax');
		$isRecognized = (
			!in_array($this->ext, array('html', 'htm')) &&
			$this->response->getMimeType($this->ext)
		);

		if (!empty($this->ext) && $isRecognized) {
			$this->renderAs($controller, $this->ext);
		} elseif ($this->request->is('ajax')) {
			$this->renderAs($controller, 'ajax');
		} elseif (empty($this->ext) || in_array($this->ext, array('html', 'htm'))) {
			$this->respondAs('html', array('charset' => Configure::read('App.encoding')));
		}

		foreach ($this->__inputTypeMap as $type => $handler) {
			if ($this->requestedWith($type)) {
				$input = call_user_func_array(array($controller->request, 'input'), $handler);
				$controller->request->data = $input;
			}
		}
	}

/**
 * Helper method to parse xml input data, due to lack of anonymous functions
 * this lives here.
 *
 * @param string $xml 
 * @return array Xml array data
 */
	public function _convertXml($xml) {
		try {
			$xml = Xml::build($xml);
			if (isset($xml->data)) {
				return Xml::toArray($xml->data);
			}
			return Xml::toArray($xml);
		 } catch (XmlException $e) {
			return array();
		 }
	}

/**
 * Handles (fakes) redirects for Ajax requests using requestAction()
 *
 * @param Controller $controller A reference to the controller
 * @param string|array $url A string or array containing the redirect location
 * @param mixed $status HTTP Status for redirect
 * @param boolean $exit
 * @return void
 */
	public function beforeRedirect($controller, $url, $status = null, $exit = true) {
		if (!$this->request->is('ajax')) {
			return;
		}
		foreach ($_POST as $key => $val) {
			unset($_POST[$key]);
		}
		if (is_array($url)) {
			$url = Router::url($url + array('base' => false));
		}
		if (!empty($status)) {
			$statusCode = $this->response->httpCodes($status);
			$code = key($statusCode);
			$this->response->statusCode($code);
		}
		$this->response->body($this->requestAction($url, array('return', 'bare' => false)));
		$this->response->send();
		$this->_stop();
	}

/**
 * Returns true if the current HTTP request is Ajax, false otherwise
 *
 * @return boolean True if call is Ajax
 * @deprecated use `$this->request->is('ajax')` instead.
 */
	public function isAjax() {
		return $this->request->is('ajax');
	}

/**
 * Returns true if the current HTTP request is coming from a Flash-based client
 *
 * @return boolean True if call is from Flash
 * @deprecated use `$this->request->is('flash')` instead.
 */
	public function isFlash() {
		return $this->request->is('flash');
	}

/**
 * Returns true if the current request is over HTTPS, false otherwise.
 *
 * @return bool True if call is over HTTPS
 * @deprecated use `$this->request->is('ssl')` instead.
 */
	public function isSSL() {
		return $this->request->is('ssl');
	}

/**
 * Returns true if the current call accepts an XML response, false otherwise
 *
 * @return boolean True if client accepts an XML response
 */
	public function isXml() {
		return $this->prefers('xml');
	}

/**
 * Returns true if the current call accepts an RSS response, false otherwise
 *
 * @return boolean True if client accepts an RSS response
 */
	public function isRss() {
		return $this->prefers('rss');
	}

/**
 * Returns true if the current call accepts an Atom response, false otherwise
 *
 * @return boolean True if client accepts an RSS response
 */
	public function isAtom() {
		return $this->prefers('atom');
	}

/**
 * Returns true if user agent string matches a mobile web browser, or if the
 * client accepts WAP content.
 *
 * @return boolean True if user agent is a mobile web browser
 */
	public function isMobile() {
		return $this->request->is('mobile') || $this->accepts('wap');
	}

/**
 * Returns true if the client accepts WAP content
 *
 * @return bool
 */
	public function isWap() {
		return $this->prefers('wap');
	}

/**
 * Returns true if the current call a POST request
 *
 * @return boolean True if call is a POST
 * @deprecated Use $this->request->is('post'); from your controller.
 */
	public function isPost() {
		return $this->request->is('post');
	}

/**
 * Returns true if the current call a PUT request
 *
 * @return boolean True if call is a PUT
 * @deprecated Use $this->request->is('put'); from your controller.
 */
	public function isPut() {
		return $this->request->is('put');
	}

/**
 * Returns true if the current call a GET request
 *
 * @return boolean True if call is a GET
 * @deprecated Use $this->request->is('get'); from your controller.
 */
	public function isGet() {
		return $this->request->is('get');
	}

/**
 * Returns true if the current call a DELETE request
 *
 * @return boolean True if call is a DELETE
 * @deprecated Use $this->request->is('delete'); from your controller.
 */
	public function isDelete() {
		return $this->request->is('delete');
	}

/**
 * Gets Prototype version if call is Ajax, otherwise empty string.
 * The Prototype library sets a special "Prototype version" HTTP header.
 *
 * @return string Prototype version of component making Ajax call
 */
	public function getAjaxVersion() {
		if (env('HTTP_X_PROTOTYPE_VERSION') != null) {
			return env('HTTP_X_PROTOTYPE_VERSION');
		}
		return false;
	}

/**
 * Adds/sets the Content-type(s) for the given name.  This method allows
 * content-types to be mapped to friendly aliases (or extensions), which allows
 * RequestHandler to automatically respond to requests of that type in the
 * startup method.
 *
 * @param string $name The name of the Content-type, i.e. "html", "xml", "css"
 * @param mixed $type The Content-type or array of Content-types assigned to the name,
 *    i.e. "text/html", or "application/xml"
 * @return void
 * @deprecated use `$this->response->type()` instead.
 */
	public function setContent($name, $type = null) {
		$this->response->type(array($name => $type));
	}

/**
 * Gets the server name from which this request was referred
 *
 * @return string Server address
 * @deprecated use $this->request->referer() from your controller instead
 */
	public function getReferer() {
		return $this->request->referer(false);
	}

/**
 * Gets remote client IP
 *
 * @param boolean $safe
 * @return string Client IP address
 * @deprecated use $this->request->clientIp() from your,  controller instead.
 */
	public function getClientIP($safe = true) {
		return $this->request->clientIp($safe);
	}

/**
 * Determines which content types the client accepts.  Acceptance is based on
 * the file extension parsed by the Router (if present), and by the HTTP_ACCEPT
 * header. Unlike CakeRequest::accepts() this method deals entirely with mapped content types.
 *
 * Usage:
 *
 * `$this->RequestHandler->accepts(array('xml', 'html', 'json'));`
 *
 * Returns true if the client accepts any of the supplied types.
 *
 * `$this->RequestHandler->accepts('xml');`
 *
 * Returns true if the client accepts xml.
 *
 * @param mixed $type Can be null (or no parameter), a string type name, or an
 *   array of types
 * @return mixed If null or no parameter is passed, returns an array of content
 *   types the client accepts.  If a string is passed, returns true
 *   if the client accepts it.  If an array is passed, returns true
 *   if the client accepts one or more elements in the array.
 * @see RequestHandlerComponent::setContent()
 */
	public function accepts($type = null) {
		$accepted = $this->request->accepts();

		if ($type == null) {
			return $this->mapType($accepted);
		} elseif (is_array($type)) {
			foreach ($type as $t) {
				$t = $this->mapAlias($t);
				if (in_array($t, $accepted)) {
					return true;
				}
			}
			return false;
		} elseif (is_string($type)) {
			$type = $this->mapAlias($type);
			return in_array($type, $accepted);
		}
		return false;
	}

/**
 * Determines the content type of the data the client has sent (i.e. in a POST request)
 *
 * @param mixed $type Can be null (or no parameter), a string type name, or an array of types
 * @return mixed If a single type is supplied a boolean will be returned.  If no type is provided
 *   The mapped value of CONTENT_TYPE will be returned. If an array is supplied the first type
 *   in the request content type will be returned.
 */
	public function requestedWith($type = null) {
		if (!$this->request->is('post') && !$this->request->is('put')) {
			return null;
		}
	
		list($contentType) = explode(';', env('CONTENT_TYPE'));
		if ($type == null) {
			return $this->mapType($contentType);
		} elseif (is_array($type)) {
			foreach ($type as $t) {
				if ($this->requestedWith($t)) {
					return $t;
				}
			}
			return false;
		} elseif (is_string($type)) {
			return ($type == $this->mapType($contentType));
		}
	}

/**
 * Determines which content-types the client prefers.  If no parameters are given,
 * the content-type that the client most likely prefers is returned.  If $type is
 * an array, the first item in the array that the client accepts is returned.
 * Preference is determined primarily by the file extension parsed by the Router
 * if provided, and secondarily by the list of content-types provided in
 * HTTP_ACCEPT.
 *
 * @param mixed $type An optional array of 'friendly' content-type names, i.e.
 *   'html', 'xml', 'js', etc.
 * @return mixed If $type is null or not provided, the first content-type in the
 *    list, based on preference, is returned.
 * @see RequestHandlerComponent::setContent()
 */
	public function prefers($type = null) {
		$accepts = $this->accepts();

		if ($type == null) {
			if (empty($this->ext)) {
				if (is_array($accepts)) {
					return $accepts[0];
				}
				return $accepts;
			}
			return $this->ext;
		}

		$types = (array)$type;

		if (count($types) === 1) {
			if (!empty($this->ext)) {
				return ($types[0] == $this->ext);
			}
			return ($types[0] == $accepts[0]);
		}

		$intersect = array_values(array_intersect($accepts, $types));
		if (empty($intersect)) {
			return false;
		}
		return $intersect[0];
	}

/**
 * Sets the layout and template paths for the content type defined by $type.
 * 
 * ### Usage:
 *
 * Render the response as an 'ajax' response.
 *
 * `$this->RequestHandler->renderAs($this, 'ajax');`
 *
 * Render the response as an xml file and force the result as a file download.
 *
 * `$this->RequestHandler->renderAs($this, 'xml', array('attachment' => 'myfile.xml');`
 *
 * @param object $controller A reference to a controller object
 * @param string $type Type of response to send (e.g: 'ajax')
 * @param array $options Array of options to use
 * @return void
 * @see RequestHandlerComponent::setContent()
 * @see RequestHandlerComponent::respondAs()
 */
	public function renderAs($controller, $type, $options = array()) {
		$defaults = array('charset' => 'UTF-8');

		if (Configure::read('App.encoding') !== null) {
			$defaults['charset'] = Configure::read('App.encoding');
		}
		$options = array_merge($defaults, $options);

		if ($type == 'ajax') {
			$controller->layout = $this->ajaxLayout;
			return $this->respondAs('html', $options);
		}
		$controller->ext = '.ctp';

		if (empty($this->__renderType)) {
			$controller->viewPath .= DS . $type;
		} else {
			$remove = preg_replace("/([\/\\\\]{$this->__renderType})$/", DS . $type, $controller->viewPath);
			$controller->viewPath = $remove;
		}
		$this->__renderType = $type;
		$controller->layoutPath = $type;

		if ($this->response->getMimeType($type)) {
			$this->respondAs($type, $options);
		}

		$helper = ucfirst($type);
		$isAdded = (
			in_array($helper, $controller->helpers) ||
			array_key_exists($helper, $controller->helpers)
		);

		if (!$isAdded) {
			App::uses($helper . 'Helper', 'View/Helper');
			if (class_exists($helper . 'Helper')) {
				$controller->helpers[] = $helper;
			}
		}
	}

/**
 * Sets the response header based on type map index name.  This wraps several methods
 * available on CakeResponse. It also allows you to use Content-Type aliases.
 *
 * @param mixed $type Friendly type name, i.e. 'html' or 'xml', or a full content-type,
 *    like 'application/x-shockwave'.
 * @param array $options If $type is a friendly type name that is associated with
 *    more than one type of content, $index is used to select which content-type to use.
 * @return boolean Returns false if the friendly type name given in $type does
 *    not exist in the type map, or if the Content-type header has
 *    already been set by this method.
 * @see RequestHandlerComponent::setContent()
 */
	public function respondAs($type, $options = array()) {
		$defaults = array('index' => null, 'charset' => null, 'attachment' => false);
		$options = $options + $defaults;

		if (strpos($type, '/') === false) {
			$cType = $this->response->getMimeType($type);
			if ($cType === false) {
				return false;
			}
			if (is_array($cType) && isset($cType[$options['index']])) {
				$cType = $cType[$options['index']];
			}
			if (is_array($cType)) {
				if ($this->prefers($cType)) {
					$cType = $this->prefers($cType);
				} else {
					$cType = $cType[0];
				}
			}
		} else {
			$cType = $type;
		}

		if ($cType != null) {
			if (empty($this->request->params['requested'])) {
				$this->response->type($cType);
			}

			if (!empty($options['charset'])) {
				$this->response->charset($options['charset']);
			}
			if (!empty($options['attachment'])) {
				$this->response->download($options['attachment']);
			}
			return true;
		}
		return false;
	}

/**
 * Returns the current response type (Content-type header), or null if not alias exists
 *
 * @return mixed A string content type alias, or raw content type if no alias map exists,
 *	otherwise null
 */
	public function responseType() {
		return $this->mapType($this->response->type());
	}

/**
 * Maps a content-type back to an alias
 *
 * @param mixed $cType Either a string content type to map, or an array of types.
 * @return mixed Aliases for the types provided.
 * @deprecated Use $this->response->mapType() in your controller instead.
 */
	public function mapType($cType) {
		return $this->response->mapType($cType);
	}

/**
 * Maps a content type alias back to its mime-type(s)
 *
 * @param mixed $alias String alias to convert back into a content type. Or an array of aliases to map.
 * @return mixed Null on an undefined alias.  String value of the mapped alias type.  If an
 *   alias maps to more than one content type, the first one will be returned.
 */
	public function mapAlias($alias) {
		if (is_array($alias)) {
			return array_map(array($this, 'mapAlias'), $alias);
		}
		$type = $this->response->getMimeType($alias);
		if ($type) {
			if (is_array($type)) {
				return $type[0];
			}
			return $type;
		}
		return null;
	}

/**
 * Add a new mapped input type.  Mapped input types are automatically 
 * converted by RequestHandlerComponent during the startup() callback.
 *
 * @param string $type The type alias being converted, ie. json
 * @param array $handler The handler array for the type.  The first index should
 *    be the handling callback, all other arguments should be additional parameters
 *    for the handler.
 * @return void
 * @throws CakeException
 */
	public function addInputType($type, $handler) {
		if (!is_array($handler) || !isset($handler[0]) || !is_callable($handler[0])) {
			throw new CakeException(__d('cake_dev', 'You must give a handler callback.'));
		}
		$this->__inputTypeMap[$type] = $handler;
	}
}
