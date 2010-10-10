<?php
/**
 * ConsoleOptionParser file
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
 * @subpackage    cake.cake.console
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Handles parsing the ARGV in the command line and provides support 
 * for GetOpt compatible option definition.  Provides a builder pattern implementation
 * for creating shell option parsers.
 *
 * @package       cake
 * @subpackage    cake.cake.console
 */
class ConsoleOptionParser {

/**
 * Description text - displays before options when help is generated
 *
 * @see ConsoleOptionParser::description()
 * @var string
 */
	protected $_description = null;

/**
 * Epilog text - displays after options when help is generated
 *
 * @see ConsoleOptionParser::epilog()
 * @var string
 */
	protected $_epilog = null;

/**
 * Option definitions.
 *
 * @see ConsoleOptionParser::addOption()
 * @var array
 */	
	protected $_options = array();

/**
 * Map of short -> long options, generated when using addOption()
 *
 * @var string
 */
	protected $_shortOptions = array();

/**
 * Positional argument definitions.
 *
 * @see ConsoleOptionParser::addArgument()
 * @var array
 */
	protected $_args = array();

/**
 * Construct an OptionParser so you can define its behavior
 *
 * ### Options
 *
 * Named arguments come in two forms, long and short. Long arguments are preceeded 
 * by two - and give a more verbose option name. i.e. `--version`. Short arguments are 
 * preceeded by one - and are only one character long.  They usually match with a long option, 
 * and provide a more terse alternative.
 *
 * #### Using Options
 * 
 * Options can be defined with both long and short forms.  By using `$parser->addOption()`
 * you can define new options.  The name of the option is used as its long form, and you 
 * can supply an additional short form, with the `short` option.
 *
 * Calling options can be done using syntax similar to most *nix command line tools. Long options
 * cane either include an `=` or leave it out.
 *
 * `cake myshell command --connection default --name=something`
 *
 * Short options can be defined singally or in groups.
 *
 * `cake myshell command -cn`
 *
 * ### Positional arguments
 *
 * If no positional arguments are defined, all of them will be parsed.  If you define positional 
 * arguments any arguments greater than those defined will cause exceptions.  Additionally you can 
 * declare arguments as optional, by setting the required param to false.
 *
 * `$parser->addArgument('model', array('required' => false));`
 *
 * ### Providing Help text
 *
 * By providing help text for your positional arguments and named arguments, the ConsoleOptionParser
 * can generate a help display for you.  You can view the help for shells by using the `--help` or `-h` switch.
 *
 */
	public function __construct($command = null, $defaultOptions = true) {
		$this->_command = $command;

		$this->addOption('help', array(
			'short' => 'h',
			'help' => 'Display this help.',
			'boolean' => true
		));

		if ($defaultOptions) {
			$this->addOption('verbose', array(
				'short' => 'v',
				'help' => __('Enable verbose output.')
			))->addOption('quiet', array(
				'short' => 'q',
				'help' => __('Enable quiet output.')
			));
		}
	}

/**
 * Get or set the description text for shell/task
 *
 * @param string $text The text to set, or null if you want to read
 * @return mixed If reading, the value of the description. If setting $this will be returned
 */
	public function description($text = null) {
		if ($text !== null) {
			$this->_description = $text;
			return $this;
		}
		return $this->_description;
	}

/**
 * Get or set an epilog to the parser.  The epilog is added to the end of
 * the options and arguments listing when help is generated.
 *
 * @param string $text Text when setting or null when reading.
 * @return mixed If reading, the value of the epilog. If setting $this will be returned.
 */
	public function epilog($text = null) {
		if ($text !== null) {
			$this->_epilog = $text;
			return $this;
		}
		return $this->_epilog;
	}

/**
 * Add an option to the option parser. Options allow you to define optional or required
 * parameters for your console application. Options are defined by the parameters they use.
 *
 * ### Params
 *
 * - `short` - The single letter variant for this option, leave undefined for none.
 * - `help` - Help text for this option.  Used when generating help for the option.
 * - `default` - The default value for this option.  If not defined the default will be true.
 * - `boolean` - The option uses no value, its just a boolean switch. Defaults to false.
 * 
 * @param string $name The long name you want to the value to be parsed out as when options are parsed.
 * @param array $params An array of parameters that define the behavior of the option
 * @return returns $this.
 */
	public function addOption($name, $params = array()) {
		$defaults = array(
			'name' => $name,
			'short' => null,
			'help' => '',
			'default' => true,
			'boolean' => false
		);
		$options = array_merge($defaults, $params);
		$this->_options[$name] = $options;
		if (!empty($options['short'])) {
			$this->_shortOptions[$options['short']] = $name;
		}
		return $this;
	}

/**
 * Add a positional argument to the option parser.
 *
 * ### Params
 *
 * - `help` The help text to display for this argument.
 * - `required` Whether this parameter is required.
 * - `index` The index for the arg, if left undefined the argument will be put
 *   onto the end of the arguments. If you define the same index twice the first
 *   option will be overwritten.
 *
 * @param string $name The name of the argument.
 * @param array $params Parameters for the argument, see above.
 * @return $this.
 */
	public function addArgument($name, $params = array()) {
		$index = count($this->_args);
		$defaults = array(
			'name' => $name,
			'help' => '',
			'index' => $index,
			'required' => false
		);
		$options = array_merge($defaults, $params);
		$this->_args[$options['index']] = $options;
		return $this;
	}

/**
 * Gets the arguments defined in the parser.
 *
 * @return array Array of argument descriptions
 */
	public function arguments() {
		return $this->_args;
	}

/**
 * Get the defined options in the parser.
 *
 * @return array
 */
	public function options() {
		return $this->_options;
	}

/**
 * Parse the argv array into a set of params and args.
 *
 * @param array $argv Array of args (argv) to parse
 * @return Array array($params, $args)
 * @throws InvalidArgumentException When an invalid parameter is encountered.
 *   RuntimeException when required arguments are not supplied.
 */
	public function parse($argv) {
		$params = $args = array();
		$this->_tokens = $argv;
		while ($token = array_shift($this->_tokens)) {
			if (substr($token, 0, 2) == '--') {
				$params = $this->_parseLongOption($token, $params);
			} elseif (substr($token, 0, 1) == '-') {
				$params = $this->_parseShortOption($token, $params);
			} else {
				$args = $this->_parseArg($token, $args);
			}
		}
		foreach ($this->_args as $i => $arg) {
			if ($arg['required'] && !isset($args[$i])) {
				throw new RuntimeException(
					sprintf(__('Missing required arguments. %s is required.'), $arg['name'])
				);
			}
		}
		return array($params, $args);
	}

/**
 * Gets formatted help for this parser object.
 * Generates help text based on the description, options, arguments and epilog
 * in the parser.
 *
 * @return string
 */
	public function help() {
		$out = array();
		if (!empty($this->_description)) {
			$out[] = $this->_description;
			$out[] = '';
		}
		$out[] = '<info>Usage:</info>';
		$out[] = $this->_generateUsage();
		$out[] = '';
		if (!empty($this->_options)) {
			$max = 0;
			foreach ($this->_options as $description) {
				$max = (strlen($description['name']) > $max) ? strlen($description['name']) : $max;
			}
			$max += 8;
			$out[] = '<info>Options:</info>';
			$out[] = '';
			foreach ($this->_options as $description) {
				$out[] = $this->_optionHelp($description, $max);
			}
			$out[] = '';
		}
		if (!empty($this->_args)) {
			$max = 0;
			foreach ($this->_args as $description) {
				$max = (strlen($description['name']) > $max) ? strlen($description['name']) : $max;
			}
			$max += 1;
			$out[] = '<info>Arguments:</info>';
			$out[] = '';
			foreach ($this->_args as $description) {
				$out[] = $this->_argumentHelp($description, $max);
			}
			$out[] = '';
		}
		if (!empty($this->_epilog)) {
			$out[] = $this->_epilog;
		}
		return implode("\n", $out);
	}

/**
 * Generate the usage for a shell based on its arguments and options.
 * Usage strings favour short options over the long ones. and optional args will 
 * be indicated with []
 *
 * @return string
 */
	protected function _generateUsage() {
		$usage = array('cake ' . $this->_command);
		foreach ($this->_options as $definition) {
			$name = empty($definition['short']) ? '--' . $definition['name'] : '-' . $definition['short'];
			$default = '';
			if (!empty($definition['default']) && $definition['default'] !== true) {
				$default = ' ' . $definition['default'];
			}
			$usage[] = sprintf('[%s%s]', $name, $default);
		}
		foreach ($this->_args as $definition) {
			$name = $definition['name'];
			if (!$definition['required']) {
				$name = '[' . $name . ']';
			}
			$usage[] = $name;
		}
		return implode(' ', $usage);
	}

/**
 * Generate the usage for a single option.
 *
 * @return string
 */
	protected function _optionHelp($definition, $nameWidth) {
		$default = $short = '';
		if (!empty($definition['default']) && $definition['default'] !== true) {
			$default = sprintf(__(' <comment>(default: %s)</comment>'), $definition['default']);
		}
		if (!empty($definition['short'])) {
			$short = ', -' . $definition['short'];
		}
		$name = sprintf('--%s%s', $definition['name'], $short);
		if (strlen($name) < $nameWidth) {
			$name = str_pad($name, $nameWidth, ' ');
		}
		return sprintf('%s%s%s', $name, $definition['help'], $default);
	}

/**
 * Generate the usage for a single argument.
 *
 * @return string
 */
	protected function _argumentHelp($definition, $width) {
		$name = $definition['name'];
		if (strlen($name) < $width) {
			$name = str_pad($name, $width, ' ');
		}
		$optional = '';
		if (!$definition['required']) {
			$optional = ' <comment>(optional)</comment>';
		}
		return sprintf('%s %s%s', $name, $definition['help'], $optional);
	}

/**
 * Parse the value for a long option out of $this->_tokens.  Will handle
 * options with an `=` in them.
 *
 * @param string $option The option to parse.
 * @param array $params The params to append the parsed value into
 * @return array Params with $option added in.
 */
	protected function _parseLongOption($option, $params) {
		$name = substr($option, 2);
		if (strpos($name, '=') !== false) {
			list($name, $value) = explode('=', $name, 2);
			array_unshift($this->_tokens, $value);
		}
		return $this->_parseOptionName($name, $params);
	}

/**
 * Parse the value for a short option out of $this->_tokens
 * If the $option is a combination of multiple shortcuts like -otf
 * they will be shifted onto the token stack and parsed individually.
 *
 * @param string $option The option to parse.
 * @param array $params The params to append the parsed value into
 * @return array Params with $option added in.
 */
	protected function _parseShortOption($option, $params) {
		$key = substr($option, 1);
		if (strlen($key) > 1) {
			$flags = str_split($key);
			$key = $flags[0];
			for ($i = 1, $len = count($flags); $i < $len; $i++) {
				array_unshift($this->_tokens, '-' . $flags[$i]);
			}
		}
		$name = $this->_shortOptions[$key];
		return $this->_parseOptionName($name, $params);
	}

/**
 * Parse an option by its name index.
 *
 * @param string $option The option to parse.
 * @param array $params The params to append the parsed value into
 * @return array Params with $option added in.
 */
	protected function _parseOptionName($name, $params) {
		if (!isset($this->_options[$name])) {
			throw new InvalidArgumentException(sprintf(__('Unknown option `%s`'), $name));
		}
		$definition = $this->_options[$name];
		$nextValue = $this->_nextToken();
		if (!$definition['boolean'] && !empty($nextValue) && $nextValue{0} != '-') {
			array_shift($this->_tokens);
			$value = $nextValue;
		} else {
			$value = $definition['default'];
		}
		$params[$name] = $value;
		return $params;
	}

/**
 * Checks that the argument doesn't exceed the declared arguments.
 *
 * @param string $argument The argument to append
 * @param array $args The array of parsed args to append to.
 * @return array Args
 */
	protected function _parseArg($argument, $args) {
		if (empty($this->_args)) {
			array_push($args, $argument);
			return $args;
		}
		$position = 0;
		$next = count($args);
		if (!isset($this->_args[$next])) {
			throw new InvalidArgumentException(__('Too many arguments.'));
		}
		array_push($args, $argument);
		return $args;
	}

/**
 * Find the next token in the argv set.
 *
 * @param string
 * @return next token or ''
 */
	protected function _nextToken() {
		return isset($this->_tokens[0]) ? $this->_tokens[0] : '';
	}
}