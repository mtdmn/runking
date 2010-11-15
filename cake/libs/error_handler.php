<?php
/**
 * Error handler
 *
 * Provides Error Capturing for Framework errors.
 *
 * PHP 5
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
 * @since         CakePHP(tm) v 0.10.5.1732
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Error Handler.
 *
 * Captures and handles all unhandled exceptions. Displays helpful framework errors when debug > 1.
 * When debug < 1 a CakeException will render 404 or  500 errors.  If an uncaught exception is thrown
 * and it is a type that ErrorHandler does not know about it will be treated as a 500 error.
 *
 * ### Implementing application specific exception handling
 *
 * You can implement application specific exception handling in one of a few ways:
 *
 * - Create a AppController::appError();
 * - Create an AppError class.
 *
 * #### Using AppController::appError();
 *
 * This controller method is called instead of the default exception handling.  It receives the 
 * thrown exception as its only argument.  You should implement your error handling in that method.
 *
 * #### Using an AppError class
 *
 * This approach gives more flexibility and power in how you handle exceptions.  You can create 
 * `app/libs/app_error.php` and create a class called `AppError`.  The core ErrorHandler class
 * will attempt to construct this class and let it handle the exception. This provides a more
 * flexible way to handle exceptions in your application.
 *
 * Finally, in your `app/config/bootstrap.php` you can configure use `set_exception_handler()`
 * to take total control over application exception handling.
 *
 * @package       cake
 * @subpackage    cake.cake.libs
 */
class ErrorHandler {

/**
 * Controller instance.
 *
 * @var Controller
 * @access public
 */
	public $controller = null;

/**
 * template to render for CakeException
 *
 * @var string
 */
	public $template = '';

/**
 * The method corresponding to the Exception this object is for.
 *
 * @var string
 */
	public $method = '';

/**
 * The exception being handled.
 *
 * @var Exception
 */
	public $error = null;

/**
 * Creates the controller to perform rendering on the error response.
 * If the error is a CakeException it will be converted to either a 400 or a 500
 * code error depending on the code used to construct the error.
 *
 * @param string $method Method producing the error
 * @param array $messages Error messages
 */
	function __construct(Exception $exception) {
		App::import('Core', 'Sanitize');

		$this->controller = $this->_getController($exception);

		if (method_exists($this->controller, 'apperror')) {
			return $this->controller->appError($exception);
		}
		$method = $template = Inflector::variable(str_replace('Exception', '', get_class($exception)));
		$code = $exception->getCode();

		$methodExists = method_exists($this, $method);

		if ($exception instanceof CakeException && !$methodExists) {
			$method = '_cakeError';
			if ($template == 'internalError') {
				$template = 'error500';
			}
		} elseif (!$methodExists) {
			$method = 'error500';
			if ($code >= 400) {
				$method = 'error400';
			}
		}

		if (Configure::read('debug') == 0) {
			$parentClass = get_parent_class($this);
			if ($parentClass != 'ErrorHandler') {
				$method = 'error400';
			}
			$parentMethods = (array)get_class_methods($parentClass);
			if (in_array($method, $parentMethods)) {
				$method = 'error400';
			}
			if ($code == 500) {
				$method = 'error500';
			}
		}
		$this->template = $template;
		$this->method = $method;
		$this->error = $exception;
	}

/**
 * Get the controller instance to handle the exception.
 * Override this method in subclasses to customize the controller used. 
 * This method returns the built in `CakeErrorController` normally, or if an error is repeated
 * a bare controller will be used.
 *
 * @param Exception $exception The exception to get a controller for.
 * @return Controller
 */
	protected function _getController($exception) {
		static $__previousError = null;
		App::import('Controller', 'CakeError');

		if ($__previousError != $exception) {
			$__previousError = $exception;
			$controller = new CakeErrorController();
		} else {
			$controller = new Controller();
			$controller->viewPath = 'errors';
		}
		return $controller;
	}

/**
 * Set as the default exception handler by the CakePHP bootstrap process.
 *
 * This will either use an AppError class if your application has one,
 * or use the default ErrorHandler.
 *
 * @return void
 * @see http://php.net/manual/en/function.set-exception-handler.php
 */
	public static function handleException(Exception $exception) {
		if (file_exists(APP . 'app_error.php') || class_exists('AppError')) {
			if (!class_exists('AppError')) {
				require(APP . 'app_error.php');
			}
			$AppError = new AppError($exception);
			return $AppError->render();
		}
		$error = new ErrorHandler($exception);
		$error->render();
	}

/**
 * Set as the default error handler by CakePHP. Use Configure::write('Error.handler', $callback), to use your own
 * error handling methods.  This function will use Debugger to display errors when debug > 0.  And 
 * will log errors to CakeLog, when debug == 0.
 *
 * You can use Configure::write('Error.level', $value); to set what type of errors will be handled here.
 *
 * @param integer $code Code of error
 * @param string $description Error description
 * @param string $file File on which error occurred
 * @param integer $line Line that triggered the error
 * @param array $context Context
 * @return boolean true if error was handled
 */
	public static function handleError($code, $description, $file = null, $line = null, $context = null) {
		$debug = Configure::read('debug');
		if ($debug) {
			return Debugger::handleError($code, $description, $file, $line, $context);
		} else {
			return CakeLog::handleError($code, $description, $file, $line, $context);
		}
	}

/**
 * Renders the response for the exception.
 *
 * @return void
 */
	public function render() {
		call_user_func_array(array($this, $this->method), array($this->error));
	}

/**
 * Generic handler for the internal framework errors CakePHP can generate.
 *
 * @param CakeExeption $error
 * @return void
 */
	protected function _cakeError(CakeException $error) {
		$url = Router::normalize($this->controller->request->here);
		$code = $error->getCode();
		$this->controller->response->statusCode($code);
		$this->controller->set(array(
			'code' => $code,
			'url' => h($url),
			'name' => $error->getMessage(),
			'error' => $error,
		));
		$this->controller->set($error->getAttributes());
		$this->_outputMessage($this->template);
	}

/**
 * Convenience method to display a 400 series page.
 *
 * @param array $params Parameters for controller
 */
	public function error400($error) {
		$message = $error->getMessage();
		if (Configure::read('debug') == 0 && $error instanceof CakeException) {
			$message = __('Not Found');
		}
		$url = Router::normalize($this->controller->request->here);
		$this->controller->response->statusCode($error->getCode());
		$this->controller->set(array(
			'name' => $message,
			'url' => h($url),
			'error' => $error,
		));
		$this->_outputMessage('error400');
	}

/**
 * Convenience method to display a 500 page.
 *
 * @param array $params Parameters for controller
 */
	public function error500($error) {
		$url = Router::normalize($this->controller->request->here);
		$code = ($error->getCode() > 500) ? $error->getCode() : 500;
		$this->controller->response->statusCode($code);
		$this->controller->set(array(
			'name' => __('An Internal Error Has Occurred'),
			'message' => h($url),
			'error' => $error,
		));
		$this->_outputMessage('error500');
	}

/**
 * Generate the response using the controller object.
 *
 * @param string $template The template to render.
 */
	protected function _outputMessage($template) {
		$this->controller->render($template);
		$this->controller->afterFilter();
		$this->controller->response->send();
	}
}
