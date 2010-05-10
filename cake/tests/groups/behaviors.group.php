<?php
/**
 * BehaviorsGroupTest file
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
 * @subpackage    cake.tests.groups
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */

/**
 * BehaviorsGroupTest class
 *
 * This test group will run all tests related to i18n/l10n and multibyte
 *
 * @package       cake
 * @subpackage    cake.tests.groups
 */
class BehaviorsGroupTest extends TestSuite {

/**
 * label property
 *
 * @var string
 * @access public
 */
	public $label = 'All core behavior test cases.';

/**
 * BehaviorsGroupTest method
 *
 * @access public
 * @return void
 */
	function BehaviorsGroupTest() {
		TestManager::addTestCasesFromDirectory($this, CORE_TEST_CASES . DS . 'libs' . DS . 'model' . DS . 'behaviors');
	}
}
