<?php
/**
 * CacheTest file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.tests.cases.libs
 * @since         CakePHP(tm) v 1.2.0.5432
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
if (!class_exists('Cache')) {
	require LIBS . 'cache.php';
}

/**
 * CacheTest class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs
 */
class CacheTest extends CakeTestCase {

/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setUp() {
		$this->_cacheDisable = Configure::read('Cache.disable');
		Configure::write('Cache.disable', false);

		$this->_defaultCacheConfig = Cache::config('default');
		Cache::config('default', array('engine' => 'File', 'path' => TMP . 'tests'));

		Cache::engine('File', array('path' => TMP . 'tests'));
	}

/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	function tearDown() {
		Configure::write('Cache.disable', $this->_cacheDisable);
		Cache::config('default', $this->_defaultCacheConfig['settings']);
		Cache::engine('File');
	}

/**
 * testConfig method
 *
 * @access public
 * @return void
 */
	function testConfig() {
		$settings = array('engine' => 'File', 'path' => TMP . 'tests', 'prefix' => 'cake_test_');
		$results = Cache::config('new', $settings);
		$this->assertEqual($results, Cache::config('new'));
	}

/**
 * test configuring CacheEngines in App/libs
 *
 * @return void
 **/
	function testConfigWithLibAndPluginEngines() {
		App::build(array(
			'libs' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'libs' . DS),
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS)
		), true);

		$settings = array('engine' => 'TestAppCache', 'path' => TMP, 'prefix' => 'cake_test_');
		$result = Cache::config('libEngine', $settings);
		$this->assertEqual($result, Cache::config('libEngine'));

		$settings = array('engine' => 'TestPlugin.TestPluginCache', 'path' => TMP, 'prefix' => 'cake_test_');
		$result = Cache::config('pluginLibEngine', $settings);
		$this->assertEqual($result, Cache::config('pluginLibEngine'));
	}

/**
 * testInvalidConfig method
 *
 * Test that the cache class doesn't cause fatal errors with a partial path
 *
 * @access public
 * @return void
 */
	function testInvaidConfig() {
		Cache::config('Invalid', array(
			'engine' => 'File',
			'duration' => '+1 year',
			'prefix' => 'testing_invalid_',
			'path' => 'data/',
			'serialize' => true
		));
		$read = Cache::read('Test', 'Invalid');
		$this->assertEqual($read, null);
	}

/**
 * testConfigChange method
 *
 * @access public
 * @return void
 */
	function testConfigChange() {
		$_cacheConfigSessions = Cache::config('sessions');
		$_cacheConfigTests = Cache::config('tests');

		$result = Cache::config('sessions', array('engine'=> 'File', 'path' => TMP . 'sessions'));
		$this->assertEqual($result['settings'], Cache::settings('File'));

		$result = Cache::config('tests', array('engine'=> 'File', 'path' => TMP . 'tests'));
		$this->assertEqual($result['settings'], Cache::settings('File'));

		Cache::config('sessions', $_cacheConfigSessions['settings']);
		Cache::config('tests', $_cacheConfigTests['settings']);
	}

/**
 * testWritingWithConfig method
 *
 * @access public
 * @return void
 */
	function testWritingWithConfig() {
		$_cacheConfigSessions = Cache::config('sessions');

		Cache::write('test_somthing', 'this is the test data', 'tests');

		$expected = array(
			'path' => TMP . 'sessions',
			'prefix' => 'cake_',
			'lock' => false,
			'serialize' => true,
			'duration' => 3600,
			'probability' => 100,
			'engine' => 'File',
			'isWindows' => DIRECTORY_SEPARATOR == '\\'
		);
		$this->assertEqual($expected, Cache::settings('File'));

		Cache::config('sessions', $_cacheConfigSessions['settings']);
	}

/**
 * testInitSettings method
 *
 * @access public
 * @return void
 */
	function testInitSettings() {
		Cache::engine('File', array('path' => TMP . 'tests'));

		$settings = Cache::settings();
		$expecting = array(
			'engine' => 'File',
			'duration'=> 3600,
			'probability' => 100,
			'path'=> TMP . 'tests',
			'prefix'=> 'cake_',
			'lock' => false,
			'serialize'=> true,
			'isWindows' => DIRECTORY_SEPARATOR == '\\'
		);
		$this->assertEqual($settings, $expecting);

		Cache::engine('File');
	}

/**
 * testWriteEmptyValues method
 *
 * @access public
 * @return void
 */
	function testWriteEmptyValues() {
		Cache::write('App.falseTest', false);
		$this->assertIdentical(Cache::read('App.falseTest'), false);

		Cache::write('App.trueTest', true);
		$this->assertIdentical(Cache::read('App.trueTest'), true);

		Cache::write('App.nullTest', null);
		$this->assertIdentical(Cache::read('App.nullTest'), null);

		Cache::write('App.zeroTest', 0);
		$this->assertIdentical(Cache::read('App.zeroTest'), 0);

		Cache::write('App.zeroTest2', '0');
		$this->assertIdentical(Cache::read('App.zeroTest2'), '0');
	}

/**
 * testCacheDisable method
 *
 * Check that the "Cache.disable" configuration and a change to it
 * (even after a cache config has been setup) is taken into account.
 *
 * @link https://trac.cakephp.org/ticket/6236
 * @access public
 * @return void
 */
	function testCacheDisable() {
		Configure::write('Cache.disable', false);
		Cache::config('test_cache_disable_1', array('engine'=> 'File', 'path' => TMP . 'tests'));

		$this->assertTrue(Cache::write('key_1', 'hello'));
		$this->assertIdentical(Cache::read('key_1'), 'hello');

		Configure::write('Cache.disable', true);

		$this->assertFalse(Cache::write('key_2', 'hello'));
		$this->assertFalse(Cache::read('key_2'));

		Configure::write('Cache.disable', false);

		$this->assertTrue(Cache::write('key_3', 'hello'));
		$this->assertIdentical(Cache::read('key_3'), 'hello');

		Configure::write('Cache.disable', true);
		Cache::config('test_cache_disable_2', array('engine'=> 'File', 'path' => TMP . 'tests'));

		$this->assertFalse(Cache::write('key_4', 'hello'));
		$this->assertFalse(Cache::read('key_4'));

		Configure::write('Cache.disable', false);

		$this->assertTrue(Cache::write('key_5', 'hello'));
		$this->assertIdentical(Cache::read('key_5'), 'hello');

		Configure::write('Cache.disable', true);

		$this->assertFalse(Cache::write('key_6', 'hello'));
		$this->assertFalse(Cache::read('key_6'));
	}

/**
 * testSet method
 *
 * @access public
 * @return void
 */
	function testSet() {
		$_cacheSet = Cache::set();

		Cache::set(array('duration' => '+1 year'));
		$data = Cache::read('test_cache');
		$this->assertFalse($data);

		$data = 'this is just a simple test of the cache system';
		$write = Cache::write('test_cache', $data);
		$this->assertTrue($write);

		Cache::set(array('duration' => '+1 year'));
		$data = Cache::read('test_cache');
		$this->assertEqual($data, 'this is just a simple test of the cache system');

		Cache::delete('test_cache');

		$global = Cache::settings();

		Cache::set($_cacheSet);
	}
}
?>