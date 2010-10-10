<?php
/**
 * Base class for Shells
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
 * @subpackage    cake.cake.console.libs
 * @since         CakePHP(tm) v 1.2.0.5012
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Shell', 'TaskCollection');
require_once CAKE . 'console' . DS . 'console_output.php';
require_once CAKE . 'console' . DS . 'console_input.php';

/**
 * Base class for command-line utilities for automating programmer chores.
 *
 * @package       cake
 * @subpackage    cake.cake.console.libs
 */
class Shell extends Object {

/**
 * An instance of the ShellDispatcher object that loaded this script
 *
 * @var ShellDispatcher
 * @access public
 */
	public $Dispatch = null;

/**
 * An instance of ConsoleOptionParser that has been configured for this class.
 *
 * @var ConsoleOptionParser
 */
	public $OptionParser;

/**
 * If true, the script will ask for permission to perform actions.
 *
 * @var boolean
 * @access public
 */
	public $interactive = true;

/**
 * Contains command switches parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $params = array();

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	public $args = array();

/**
 * Shell paths
 *
 * @var string
 */
	public $shellPaths = array();

/**
 * The file name of the shell that was invoked.
 *
 * @var string
 * @access public
 */
	public $shell = null;

/**
 * The command called if public methods are available.
 *
 * @var string
 * @access public
 */
	public $command = null;

/**
 * The name of the shell in camelized.
 *
 * @var string
 * @access public
 */
	public $name = null;

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 * @access public
 */
	public $tasks = array();

/**
 * Contains the loaded tasks
 *
 * @var array
 * @access public
 */
	public $taskNames = array();

/**
 * Contains models to load and instantiate
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * Task Collection for the command, used to create Tasks.
 *
 * @var TaskCollection
 */
	public $Tasks;

/**
 * Normalized map of tasks.
 *
 * @var string
 */
	protected $_taskMap = array();

/**
 * stdout object.
 *
 * @var ConsoleOutput
 */
	public $stdout;

/**
 * stderr object.
 *
 * @var ConsoleOutput
 */
	public $stderr;

/**
 * stdin object
 *
 * @var ConsoleInput
 */
	public $stdin;

/**
 *  Constructs this Shell instance.
 *
 */
	function __construct(&$dispatch, $stdout = null, $stderr = null, $stdin = null) {
		$vars = array('shell', 'shellCommand' => 'command', 'shellPaths');

		foreach ($vars as $key => $var) {
			if (is_string($key)) {
				$this->{$var} = $dispatch->{$key};
			} else {
				$this->{$var} = $dispatch->{$var};
			}
		}

		if ($this->name == null) {
			$this->name = Inflector::underscore(str_replace(array('Shell', 'Task'), '', get_class($this)));
		}

		$this->Dispatch =& $dispatch;
		$this->Tasks = new TaskCollection($this, $dispatch);

		$this->stdout = $stdout;
		$this->stderr = $stderr;
		$this->stdin = $stdin;
		if ($this->stdout == null) {
			$this->stdout = new ConsoleOutput('php://stdout');
		}
		if ($this->stderr == null) {
			$this->stderr = new ConsoleOutput('php://stderr');
		}
		if ($this->stdin == null) {
			$this->stdin = new ConsoleInput('php://stdin');
		}
	}

/**
 * Initializes the Shell
 * acts as constructor for subclasses
 * allows configuration of tasks prior to shell execution
 *
 */
	public function initialize() {
		$this->_loadModels();
	}

/**
 * Starts up the Shell
 * allows for checking and configuring prior to command or main execution
 * can be overriden in subclasses
 *
 */
	public function startup() {
		$this->_welcome();
	}

/**
 * Displays a header for the shell
 *
 */
	protected function _welcome() {
		$this->clear();
		$this->out();
		$this->out('<info>Welcome to CakePHP v' . Configure::version() . ' Console</info>');
		$this->hr();
		$this->out('App : '. $this->Dispatch->params['app']);
		$this->out('Path: '. $this->Dispatch->params['working']);
		$this->hr();
	}

/**
 * if public $uses = true
 * Loads AppModel file and constructs AppModel class
 * makes $this->AppModel available to subclasses
 * if public $uses is an array of models will load those models
 *
 * @return bool
 */
	protected function _loadModels() {
		if ($this->uses === null || $this->uses === false) {
			return;
		}

		if ($this->uses !== true && !empty($this->uses)) {
			$uses = is_array($this->uses) ? $this->uses : array($this->uses);

			$modelClassName = $uses[0];
			if (strpos($uses[0], '.') !== false) {
				list($plugin, $modelClassName) = explode('.', $uses[0]);
			}
			$this->modelClass = $modelClassName;

			foreach ($uses as $modelClass) {
				list($plugin, $modelClass) = pluginSplit($modelClass, true);
				$this->{$modelClass} = ClassRegistry::init($plugin . $modelClass);
			}
			return true;
		}
		return false;
	}

/**
 * Loads tasks defined in public $tasks
 *
 * @return bool
 */
	public function loadTasks() {
		if ($this->tasks === true || empty($this->tasks) || empty($this->Tasks)) {
			return true;
		}
		$this->_taskMap = TaskCollection::normalizeObjectArray((array)$this->tasks);
		foreach ($this->_taskMap as $task => $properties) {
			$this->taskNames[] = $task;
		}
		return true;
	}

/**
 * Check to see if this shell has a task with the provided name.
 *
 * @param string $task The task name to check.
 * @return boolean Success
 */
	public function hasTask($task) {
		return isset($this->_taskMap[Inflector::camelize($task)]);
	}

/**
 * Check to see if this shell has a callable method by the given name.
 *
 * @param string $name The method name to check.
 * @return boolean
 */
	public function hasMethod($name) {
		if (empty($this->_reflection)) {
			$this->_reflection = new ReflectionClass($this);
		}
		try {
			$method = $this->_reflection->getMethod($name);
			if (!$method->isPublic() || substr($name, 0, 1) === '_') {
				return false;
			}
			if ($method->getDeclaringClass() != $this->_reflection) {
				return false;
			}
			return true;
		} catch (ReflectionException $e) {
			return false;
		}
	}

/**
 * Runs the Shell with the provided argv
 *
 * @param array $argv Array of arguments to run the shell with. This array should be missing the shell name.
 * @return void
 */
	public function runCommand($command, $argv) {
		$isTask = $this->hasTask($command);
		$isMethod = $this->hasMethod($command);
		$isMain = $this->hasMethod('main');

		if ($isTask || $isMethod && $command !== 'execute') {
			array_shift($argv);
		}

		$this->OptionParser = $this->getOptionParser();
		list($this->params, $this->args) = $this->OptionParser->parse($argv);

		if (($isTask || $isMethod || $isMain) && $command !== 'execute' ) {
			$this->startup();
		}
		if (isset($this->params['help'])) {
			return $this->out($this->OptionParser->help($command));
		}
		if ($isTask) {
			$command = Inflector::camelize($command);
			return $this->{$command}->runCommand('execute', $argv);
		}
		if ($isMethod) {
			return $this->{$command}();
		}
		if ($isMain) {
			return $this->main();
		}
		throw new MissingShellMethodException(array('shell' => get_class($this), 'method' => $command));
	}

/**
 * Gets the option parser instance and configures it.
 * By overriding this method you can configure the ConsoleOptionParser before returning it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = new ConsoleOptionParser($this->name);
		return $parser;
	}

/**
 * Overload get for lazy building of tasks
 *
 * @return void
 */
	public function __get($name) {
		if (empty($this->{$name}) && in_array($name, $this->taskNames)) {
			$properties = $this->_taskMap[$name];
			$this->{$name} = $this->Tasks->load($properties['class'], $properties['settings']);
			$this->{$name}->initialize();
			$this->{$name}->loadTasks();
		}
		return $this->{$name};
	}

/**
 * Prompts the user for input, and returns it.
 *
 * @param string $prompt Prompt text.
 * @param mixed $options Array or string of options.
 * @param string $default Default input value.
 * @return Either the default value, or the user-provided input.
 */
	public function in($prompt, $options = null, $default = null) {
		if (!$this->interactive) {
			return $default;
		}
		$in = $this->_getInput($prompt, $options, $default);

		if ($options && is_string($options)) {
			if (strpos($options, ',')) {
				$options = explode(',', $options);
			} elseif (strpos($options, '/')) {
				$options = explode('/', $options);
			} else {
				$options = array($options);
			}
		}
		if (is_array($options)) {
			while ($in == '' || ($in && (!in_array(strtolower($in), $options) && !in_array(strtoupper($in), $options)) && !in_array($in, $options))) {
				$in = $this->_getInput($prompt, $options, $default);
			}
		}
		if ($in) {
			return $in;
		}
	}

/**
 * Prompts the user for input, and returns it.
 *
 * @param string $prompt Prompt text.
 * @param mixed $options Array or string of options.
 * @param string $default Default input value.
 * @return Either the default value, or the user-provided input.
 */
	protected function _getInput($prompt, $options, $default) {
		if (!is_array($options)) {
			$printOptions = '';
		} else {
			$printOptions = '(' . implode('/', $options) . ')';
		}

		if ($default === null) {
			$this->stdout->write($prompt . " $printOptions \n" . '> ', 0);
		} else {
			$this->stdout->write($prompt . " $printOptions \n" . "[$default] > ", 0);
		}
		$result = $this->stdin->read();

		if ($result === false) {
			$this->_stop(1);
		}
		$result = trim($result);

		if ($default != null && empty($result)) {
			return $default;
		}
		return $result;
	}

/**
 * Outputs a single or multiple messages to stdout. If no parameters
 * are passed outputs just a newline.
 *
 * @param mixed $message A string or a an array of strings to output
 * @param integer $newlines Number of newlines to append
 * @return integer Returns the number of bytes returned from writing to stdout.
 */
	public function out($message = null, $newlines = 1) {
		return $this->stdout->write($message, $newlines);
	}

/**
 * Outputs a single or multiple error messages to stderr. If no parameters
 * are passed outputs just a newline.
 *
 * @param mixed $message A string or a an array of strings to output
 * @param integer $newlines Number of newlines to append
 */
	public function err($message = null, $newlines = 1) {
		$this->stderr->write($message, $newlines);
	}

/**
 * Returns a single or multiple linefeeds sequences.
 *
 * @param integer $multiplier Number of times the linefeed sequence should be repeated
 * @access public
 * @return string
 */
	function nl($multiplier = 1) {
		return str_repeat(ConsoleOutput::LF, $multiplier);
	}

/**
 * Outputs a series of minus characters to the standard output, acts as a visual separator.
 *
 * @param integer $newlines Number of newlines to pre- and append
 * @param integer $width Width of the line, defaults to 63
 */
	public function hr($newlines = 0, $width = 63) {
		$this->out(null, $newlines);
		$this->out(str_repeat('-', $width));
		$this->out(null, $newlines);
	}

/**
 * Displays a formatted error message
 * and exits the application with status code 1
 *
 * @param string $title Title of the error
 * @param string $message An optional error message
 */
	public function error($title, $message = null) {
		$this->err(sprintf(__('<error>Error:</error> %s'), $title));

		if (!empty($message)) {
			$this->err($message);
		}
		$this->_stop(1);
	}

/**
 * Clear the console
 *
 * @return void
 */
	public function clear() {
		if (empty($this->params['noclear'])) {
			if ( DS === '/') {
				passthru('clear');
			} else {
				passthru('cls');
			}
		}
	}

/**
 * Will check the number args matches otherwise throw an error
 *
 * @param integer $expectedNum Expected number of paramters
 * @param string $command Command
 */
	protected function _checkArgs($expectedNum, $command = null) {
		if (!$command) {
			$command = $this->command;
		}
		if (count($this->args) < $expectedNum) {
			$message[] = "Got: " . count($this->args);
			$message[] = "Expected: {$expectedNum}";
			$message[] = "Please type `cake {$this->shell} help` for help";
			$message[] = "on usage of the {$this->name} {$command}.";
			$this->error('Wrong number of parameters', $message);
		}
	}

/**
 * Creates a file at given path
 *
 * @param string $path Where to put the file.
 * @param string $contents Content to put in the file.
 * @return boolean Success
 */
	public function createFile($path, $contents) {
		$path = str_replace(DS . DS, DS, $path);

		$this->out();
		$this->out(sprintf(__('Creating file %s'), $path));

		if (is_file($path) && $this->interactive === true) {
			$prompt = sprintf(__('<warning>File `%s` exists</warning>, overwrite?'), $path);
			$key = $this->in($prompt,  array('y', 'n', 'q'), 'n');

			if (strtolower($key) == 'q') {
				$this->out(__('<error>Quitting</error>.'), 2);
				$this->_stop();
			} elseif (strtolower($key) != 'y') {
				$this->out(sprintf(__('Skip `%s`'), $path), 2);
				return false;
			}
		}
		if (!class_exists('File')) {
			require LIBS . 'file.php';
		}

		if ($File = new File($path, true)) {
			$data = $File->prepare($contents);
			$File->write($data);
			$this->out(sprintf(__('<success>Wrote</success> `%s`'), $path));
			return true;
		} else {
			$this->err(sprintf(__('<error>Could not write to `%s`</error>.'), $path), 2);
			return false;
		}
	}

/**
 * Action to create a Unit Test
 *
 * @return boolean Success
 */
	protected function _checkUnitTest() {
		if (App::import('vendor', 'simpletest' . DS . 'simpletest')) {
			return true;
		}
		$prompt = 'SimpleTest is not installed. Do you want to bake unit test files anyway?';
		$unitTest = $this->in($prompt, array('y','n'), 'y');
		$result = strtolower($unitTest) == 'y' || strtolower($unitTest) == 'yes';

		if ($result) {
			$this->out();
			$this->out('You can download SimpleTest from http://simpletest.org');
		}
		return $result;
	}

/**
 * Makes absolute file path easier to read
 *
 * @param string $file Absolute file path
 * @return sting short path
 */
	public function shortPath($file) {
		$shortPath = str_replace(ROOT, null, $file);
		$shortPath = str_replace('..' . DS, '', $shortPath);
		return str_replace(DS . DS, DS, $shortPath);
	}

/**
 * Creates the proper controller path for the specified controller class name
 *
 * @param string $name Controller class name
 * @return string Path to controller
 */
	protected function _controllerPath($name) {
		return strtolower(Inflector::underscore($name));
	}

/**
 * Creates the proper controller plural name for the specified controller class name
 *
 * @param string $name Controller class name
 * @return string Controller plural name
 */
	protected function _controllerName($name) {
		return Inflector::pluralize(Inflector::camelize($name));
	}

/**
 * Creates the proper controller camelized name (singularized) for the specified name
 *
 * @param string $name Name
 * @return string Camelized and singularized controller name
 */
	protected function _modelName($name) {
		return Inflector::camelize(Inflector::singularize($name));
	}

/**
 * Creates the proper underscored model key for associations
 *
 * @param string $name Model class name
 * @return string Singular model key
 */
	protected function _modelKey($name) {
		return Inflector::underscore($name) . '_id';
	}

/**
 * Creates the proper model name from a foreign key
 *
 * @param string $key Foreign key
 * @return string Model name
 */
	protected function _modelNameFromKey($key) {
		return Inflector::camelize(str_replace('_id', '', $key));
	}

/**
 * creates the singular name for use in views.
 *
 * @param string $name
 * @return string $name
 */
	protected function _singularName($name) {
		return Inflector::variable(Inflector::singularize($name));
	}

/**
 * Creates the plural name for views
 *
 * @param string $name Name to use
 * @return string Plural name for views
 */
	protected function _pluralName($name) {
		return Inflector::variable(Inflector::pluralize($name));
	}

/**
 * Creates the singular human name used in views
 *
 * @param string $name Controller name
 * @return string Singular human name
 */
	protected function _singularHumanName($name) {
		return Inflector::humanize(Inflector::underscore(Inflector::singularize($name)));
	}

/**
 * Creates the plural human name used in views
 *
 * @param string $name Controller name
 * @return string Plural human name
 */
	protected function _pluralHumanName($name) {
		return Inflector::humanize(Inflector::underscore($name));
	}

/**
 * Find the correct path for a plugin. Scans $pluginPaths for the plugin you want.
 *
 * @param string $pluginName Name of the plugin you want ie. DebugKit
 * @return string $path path to the correct plugin.
 */
	function _pluginPath($pluginName) {
		return App::pluginPath($pluginName);
	}
}
