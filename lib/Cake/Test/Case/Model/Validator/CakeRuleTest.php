<?php
/**
 * CakeRuleTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       Cake.Test.Case.Model.Validator
 * @since         CakePHP(tm) v 2.2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeRule', 'Model/Validator');

/**
 * CakeRuleTest
 *
 * @package       Cake.Test.Case.Model.Validator
 */
class CakeRuleTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * Auxiliary method to test custom validators
 *
 * @return boolean
 **/
	public function myTestRule() {
		return false;
	}

/**
 * Auxiliary method to test custom validators
 *
 * @return boolean
 **/
	public function myTestRule2() {
		return true;
	}

/**
 * Auxiliary method to test custom validators
 *
 * @return string
 **/
	public function myTestRule3() {
		return 'string';
	}

/**
 * Test isValid method
 *
 * @return void
 */
	public function testIsValid() {
		$def = array('rule' => 'notEmpty', 'message' => 'Can not be empty');
		$data = array(
			'fieldName' => ''
		);
		$methods = array();

		$Rule = new CakeRule('notEmpty', $def);
		$Rule->process('fieldName', $data, $methods);
		$this->assertFalse($Rule->isValid());

		$data = array('fieldName' => 'not empty');
		$Rule->process('fieldName', $data, $methods);
		$this->assertTrue($Rule->isValid());
	}
/**
 * tests that passing custom validation methods work
 *
 * @return void
 */
	public function testCustomMethods() {
		$def = array('rule' => 'myTestRule');
		$data = array(
			'fieldName' => 'some data'
		);
		$methods = array('mytestrule' => array($this, 'myTestRule'));

		$Rule = new CakeRule('custom', $def);
		$Rule->process('fieldName', $data, $methods);
		$this->assertFalse($Rule->isValid());

		$methods = array('mytestrule' => array($this, 'myTestRule2'));
		$Rule->process('fieldName', $data, $methods);
		$this->assertTrue($Rule->isValid());

		$methods = array('mytestrule' => array($this, 'myTestRule3'));
		$Rule->process('fieldName', $data, $methods);
		$this->assertFalse($Rule->isValid());
	}

/**
 * Test isRequired method
 *
 * @return void
 */
	public function testIsRequired() {
		$def = array('rule' => 'notEmpty', 'required' => true);
		$Rule = new CakeRule('required', $def);
		$this->assertTrue($Rule->isRequired());

		$def = array('rule' => 'notEmpty', 'required' => false);
		$Rule = new CakeRule('required', $def);
		$this->assertFalse($Rule->isRequired());

		$def = array('rule' => 'notEmpty', 'required' => 'create');
		$Rule = new CakeRule('required', $def);
		$this->assertTrue($Rule->isRequired());

		$def = array('rule' => 'notEmpty', 'required' => 'update');
		$Rule = new CakeRule('required', $def);
		$this->assertFalse($Rule->isRequired());

		$Rule->isUpdate(true);
		$this->assertTrue($Rule->isRequired());
	}
}
