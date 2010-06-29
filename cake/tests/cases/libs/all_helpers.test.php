<?php
/**
 * HelpersGroupTest file
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
 * @subpackage    cake.tests.cases
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * HelpersGroupTest class
 *
 * This test group will run all test in the cases/libs/view/helpers directory.
 *
 * @package       cake
 * @subpackage    cake.tests.groups
 */
class AllHelpersTest extends PHPUnit_Framework_TestSuite {

/**
 * suite declares tests to run
 *
 * @access public
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Helper tests');

		$suite->addTestFile(CORE_TEST_CASES . DS . 'libs' . DS . 'view' . DS . 'helper.test.php');

		$helperIterator = new DirectoryIterator(CORE_TEST_CASES . DS . 'libs' . DS . 'view' . DS . 'helpers' . DS);

		// The following test cases cause segfaults for me.
		$segfaulty = array('form.test.php', 'cache.test.php', 'session.test.php');

		foreach ($helperIterator as $i => $file) {
			if (!$file->isDot() && !in_array($file->getFilename(), $segfaulty)) {
				$suite->addTestfile($file->getPathname());
			}
		}
		return $suite;
	}
}
