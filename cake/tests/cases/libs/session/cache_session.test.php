<?php
/**
 * CacheSessionTest
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.tests.cases.libs.session
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::import('Core', 'CakeSession');
App::import('Core', 'session/CacheSession');

class CacheSessionTest extends CakeTestCase {

	protected static $_sessionBackup;

/**
 * test case startup
 *
 * @return void
 */
	public static function setupBeforeClass() {
		Cache::config('session_test', array(
			'engine' => 'File',
			'prefix' => 'session_test_'
		));
		self::$_sessionBackup = Configure::read('Session');

		Configure::write('Session.handler.config', 'session_test');
	}

/**
 * cleanup after test case.
 *
 * @return void
 */
	public static function teardownAfterClass() {
		Cache::clear('session_test');
		Cache::drop('session_test');

		Configure::write('Session', self::$_sessionBackup);
	}

/**
 * test open
 *
 * @return void
 */
	function testOpen() {
		$this->assertTrue(CacheSession::open());
	}

/**
 * test write()
 *
 * @return void
 */
	function testWrite() {
		CacheSession::write('abc', 'Some value');
		$this->assertEquals('Some value', Cache::read('abc', 'session_test'), 'Value was not written.');
		$this->assertFalse(Cache::read('abc', 'default'), 'Cache should only write to the given config.');
	}

/**
 * test reading.
 *
 * @return void
 */
	function testRead() {
		CacheSession::write('test_one', 'Some other value');
		$this->assertEquals('Some other value', CacheSession::read('test_one'), 'Incorrect value.');
	}

/**
 * test destroy
 *
 * @return void
 */
	function testDestroy() {
		CacheSession::write('test_one', 'Some other value');
		$this->assertTrue(CacheSession::destroy('test_one'), 'Value was not deleted.');

		$this->assertFalse(Cache::read('test_one', 'session_test'), 'Value stuck around.');
	}

}