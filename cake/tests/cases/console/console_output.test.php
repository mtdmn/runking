<?php
/**
 * ShellDispatcherTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2010, Cake Software Foundation, Inc.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc.
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.tests.cases.console
 * @since         CakePHP(tm) v 1.2.0.5432
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once CAKE . 'console' .  DS . 'console_output.php';

class ConsoleOutputTest extends CakeTestCase {

/**
 * setup
 *
 * @return void
 */
	function setUp() {
		parent::setUp();
		$this->output = $this->getMock('ConsoleOutput', array('_write'));
	}

/**
 * tearDown
 *
 * @return void
 */
	function tearDown() {
		unset($this->output);
	}

/**
 * test writing with no new line
 *
 * @return void
 */
	function testWriteNoNewLine() {
		$this->output->expects($this->once())->method('_write')
			->with('Some output');

		$this->output->write('Some output', false);
	}

/**
 * test writing with no new line
 *
 * @return void
 */
	function testWriteNewLine() {
		$this->output->expects($this->once())->method('_write')
			->with('Some output' . PHP_EOL);

		$this->output->write('Some output');
	}

/**
 * test write() with multiple new lines
 *
 * @return void
 */
	function testWriteMultipleNewLines() {
		$this->output->expects($this->once())->method('_write')
			->with('Some output' . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL);

		$this->output->write('Some output', 4);
	}

/**
 * test writing an array of messages.
 *
 * @return void
 */
	function testWriteArray() {
		$this->output->expects($this->once())->method('_write')
			->with('Line' . PHP_EOL . 'Line' . PHP_EOL . 'Line' . PHP_EOL);

		$this->output->write(array('Line', 'Line', 'Line'));
	}

/**
 * test getting a style.
 *
 * @return void
 */
	function testStylesGet() {
		$result = $this->output->styles('error');
		$expected = array('text' => 'red');
		$this->assertEqual($result, $expected);

		$this->assertNull($this->output->styles('made_up_goop'));

		$result = $this->output->styles();
		$this->assertNotEmpty($result, 'error', 'Error is missing');
		$this->assertNotEmpty($result, 'warning', 'Warning is missing');
	}

/**
 * test adding a style.
 *
 * @return void
 */
	function testStylesAdding() {
		$this->output->styles('test', array('text' => 'red', 'background' => 'black'));
		$result = $this->output->styles('test');
		$expected = array('text' => 'red', 'background' => 'black');
		$this->assertEquals($expected, $result);
		
		$this->assertTrue($this->output->styles('test', false), 'Removing a style should return true.');
		$this->assertNull($this->output->styles('test'), 'Removed styles should be null.');
	}

/**
 * test formatting text with styles.
 *
 * @return void
 */
	function testFormattingSimple() {
		$this->output->expects($this->once())->method('_write')
			->with("\033[31mError:\033[0m Something bad");

		$this->output->write('<error>Error:</error> Something bad', false);
	}

/**
 * test formatting text with missing styles.
 *
 * @return void
 */
	function testFormattingMissingStyleName() {
		$this->output->expects($this->once())->method('_write')
			->with("Error: Something bad");

		$this->output->write('<not_there>Error:</not_there> Something bad', false);
	}

/**
 * test formatting text with multiple styles.
 *
 * @return void
 */
	function testFormattingMultipleStylesName() {
		$this->output->expects($this->once())->method('_write')
			->with("\033[31mBad\033[0m \033[33mWarning\033[0m Regular");

		$this->output->write('<error>Bad</error> <warning>Warning</warning> Regular', false);
	}

}