<?php

class CakePlugin {

/**
 * Holds a list of all loaded plugins and their configuration
 *
 */
	private static $_plugins = array();

/**
 * Loads a plugin and optionally loads bootstrapping, routing files or loads a initialization function
 *
 * Examples:
 *
 * 	`CakePlugin::load('DebugKit')` will load the DebugKit plugin and will not load any bootstrap nor route files
 *	`CakePlugin::load('DebugKit', array('bootstrap' => true, 'routes' => true))` will load the bootstrap.php and routes.php files
 * 	`CakePlugin::load('DebugKit', array('bootstrap' => false, 'routes' => true))` will load routes.php file but not bootstrap.php
 * 	`CakePlugin::load('DebugKit', array('bootstrap' => array('config1', 'config2')))` will load config1.php and config2.php files
 *	`CakePlugin::load('DebugKit', array('bootstrap' => 'aCallableMethod'))` will run the aCallableMethod function to initialize it
 *
 * Bootstrap initialization functions can be expressed as a PHP callback type, including closures. Callbacks will receive two
 * parameters (plugin name, plugin configuration)
 *
 * It is also possible to load multiple plugins at once. Examples:
 * 
 * `CakePlugin::load(array('DebugKit', 'ApiGenerator'))` will load the DebugKit and ApiGenerator plugins
 * `CakePlugin::load(array('DebugKit', 'ApiGenerator'), array('bootstrap' => true))` will load bootstrap file for both plugins
 * 
 * {{{
 * 	CakePlugin::load(array(
 * 		'DebugKit' => array('routes' => true),
 * 		'ApiGenerator'
 * 		), array('bootstrap' => true))
 * }}}
 * 
 * Will only load the bootstrap for ApiGenerator and only the routes for DebugKit
 * 
 * @param mixed $plugin name of the plugin to be loaded in CamelCase format or array or plugins to load
 * @param array $config configuration options for the plugin
 * @throws MissingPluginException if the folder for the plugin to be loaded is not found
 * @return void
 */
	public static function load($plugin, $config = array()) {
		if (is_array($plugin)) {
			foreach ($plugin as $name => $conf) {
				list($name, $conf) = (is_numeric($name)) ? array($conf, $config) : array($name, $conf);
				self::load($name, $config);
			}
			return;
		}
		$config += array('bootstrap' => false, 'routes' => false);
		$underscored = Inflector::underscore($plugin);
		if (empty($config[$path])) {
			foreach (App::path('plugins') as $path) {
				if (is_dir($path . $underscored)) {
					self::$_plugins[$plugin] = $config + array('path' => $path . $underscored . DS);
				}
			}
		} else {
			self::$_plugins[$plugin] = $config;
		}

		if (empty(self::$_plugins[$plugin]['path'])) {
			throw new MissingPluginException(array('plugin' => $plugin));
		}
		if (!empty(self::$_plugins[$plugin]['bootstrap'])) {
			self::bootstrap($plugin);
		}
		if (!empty(self::$_plugins[$plugin]['routes'])) {
			self::routes($plugin);
		}
	}


/**
 * Returns the filesystem path for a plugin
 *
 * @param string $plugin name of the plugin in CamelCase format
 * @return string path to the plugin folder
 * @throws MissingPluginException if the folder for plugin was not found or plugin has not been loaded
 */
	public static function path($plugin) {
		if (empty(self::_$plugins[$plugin])) {
			throw new MissingPluginException(array('plugin' => $plugin));
		}
		return self::_$plugins[$plugin]['path'];
	}

/**
 * Loads the bootstrapping files for a plugin, or calls the initialization setup in the configuration
 *
 * @param string $plugin name of the plugin
 * @return mixed
 * @see CakePlugin::load() for examples of bootstrap configuration
 */
	public static function bootstrap($plugin) {
		$config = self::$_plugins[$plugin];
		if ($config['bootstrap'] === false) {
			return false;
		}
		if (is_callable($config['bootstrap'])) {
			return call_user_func_array($config['bootstrap'], array($plugin, $config));
		}

		$path = self::path($plugin);
		if ($config['bootstrap'] === true && is_file($path . 'config' . DS . 'bootstrap.php')) {
			return include($path . 'config' . DS . 'bootstrap.php');
		}

		$bootstrap = (array)$config['bootstrap'];
		foreach ($bootstrap as $file) {
			if (is_file($path . 'config' . DS . $file . '.php')) {
				include $path . 'config' . DS . $file . '.php'
			}
		}

		return true;
	}

/**
 * Loads the routes file for a plugin
 *
 * @param string $plugin name of the plugin
 * @return boolean
 */
	public static function routes($plugin) {
		$config = self::$_plugins[$plugin];
		if ($config['routes'] === false) {
			return false;
		}
		$path = include self::path($plugin) . 'config' . DS . 'routes.php';
		if (is_file($path)) {
			include $path;
		}
		return true;
	}

/**
 * Returns a list of all loaded plugins
 *
 * @return array list of plugins that have been loaded
 */
	public static function list() {
		return array_keys(self::$_plugins);
	}

/**
 * Forgets a loaded plugin or all of them if first parameter is null
 *
 * @param string $plugin name of the plugin to forget
 * @return void
 */
	public static function unload($plugin = null) {
		if (is_null($plugin)) {
			self::$_plugins = array();
		} else {
			unset($_plugins[$plugin]);
		}
	}
}