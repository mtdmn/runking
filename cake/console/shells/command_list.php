<?php
/**
 * CommandListTest file
 *
 * PHP 5
 *
 * CakePHP :  Rapid Development Framework (http://cakephp.org)
 * Copyright 2006-2010, Cake Software Foundation, Inc.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2006-2010, Cake Software Foundation, Inc.
 * @link          http://cakephp.org CakePHP Project
 * @package       cake
 * @subpackage    cake.tests.cases.console.libs
 * @since         CakePHP v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Shows a list of commands available from the console.
 *
 * @package cake.console.libs
 */
class CommandListShell extends Shell {
/**
 * Main function Prints out the list of shells.
 *
 * @return void
 */
	public function main() {
		$this->out("<info>Current Paths:</info>", 2);
		$this->out(" -app: ". $this->Dispatch->params['app']);
		$this->out(" -working: " . rtrim($this->Dispatch->params['working'], DS));
		$this->out(" -root: " . rtrim($this->Dispatch->params['root'], DS));
		$this->out(" -core: " . rtrim(CORE_PATH, DS));
		$this->out("");
		$this->out("<info>Changing Paths:</info>", 2);
		$this->out("Your working path should be the same as your application path");
		$this->out("to change your path use the '-app' param.");
		$this->out("Example: -app relative/path/to/myapp or -app /absolute/path/to/myapp", 2);

		$this->out("<info>Available Shells:</info>", 2);

		$shellList = $this->_getShellList();

		if ($shellList) {
			ksort($shellList);
			if (empty($this->params['xml'])) {
				$this->_asText($shellList);
			} else {
				$this->_asXml($shellList);
			}
		}
	}

/**
 * Gets the shell command listing.
 *
 * @return array 
 */
	protected function _getShellList() {
		$shellList = array();

		$corePaths = App::core('shells');
		$shellList = $this->_appendShells('CORE', $corePaths, $shellList);

		$appPaths = array_diff(App::path('shells'), $corePaths);
		$shellList = $this->_appendShells('app', $appPaths, $shellList);

		$plugins = App::objects('plugin');
		foreach ($plugins as $plugin) {
			$pluginPath = App::pluginPath($plugin) . 'console' . DS . 'shells' . DS;
			$shellList = $this->_appendShells($plugin, array($pluginPath), $shellList);
		}
		return $shellList;
	}

/**
 * Scan the provided paths for shells, and append them into $shellList
 *
 * @return array
 */
	protected function _appendShells($type, $paths, $shellList) {
		foreach ($paths as $path) {
			if (!is_dir($path)) {
				continue;
			}
 			$shells = App::objects('file', $path);

			if (empty($shells)) {
				continue;
			}
			foreach ($shells as $shell) {
				if ($shell !== 'shell.php') {
					$shell = str_replace('.php', '', $shell);
					$shellList[$shell][$type] = $type;
				}
			}
		}
		return $shellList;
	}

/**
 * Output text.
 *
 * @return void
 */
	protected function _asText($shellList) {
		if (DS === '/') {
			$width = exec('tput cols') - 2;
		}
		if (empty($width)) {
			$width = 80;
		}
		$columns = max(1, floor($width / 30));
		$rows = ceil(count($shellList) / $columns);

		foreach ($shellList as $shell => $types) {
			sort($types);
			$shellList[$shell] = str_pad($shell . ' [' . implode ($types, ', ') . ']', $width / $columns);
		}
		$out = array_chunk($shellList, $rows);
		for ($i = 0; $i < $rows; $i++) {
			$row = '';
			for ($j = 0; $j < $columns; $j++) {
				if (!isset($out[$j][$i])) {
					continue;
				}
				$row .= $out[$j][$i];
			}
			$this->out(" " . $row);
		}
		$this->out();
		$this->out("To run a command, type <info>cake shell_name [args]</info>");
		$this->out("To get help on a specific command, type <info>cake shell_name --help</info>", 2);
	}

/**
 * Output as XML
 *
 * @return void
 */
	protected function _asXml($shellList) {
		$plugins = App::objects('plugin');
		$shells = new SimpleXmlElement('<shells></shells>');
		foreach ($shellList as $name => $location) {
			$source = current($location);
			$callable = $name;
			if (in_array($source, $plugins)) {
				$callable = Inflector::underscore($source) . '.' . $name;
			}
			$shell = $shells->addChild('shell');
			$shell->addAttribute('name', $name);
			$shell->addAttribute('call_as', $callable);
			$shell->addAttribute('provider', $source);
			$shell->addAttribute('help', $callable . ' -h');
		}
		$this->out($shells->saveXml());
	}

/**
 * get the option parser
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description('Get the list of available shells for this CakePHP application.')
			->addOption('xml', array(
				'help' => __('Get the listing as XML.'),
				'boolean' => true
			));
	}
}
