<?php
/**
 * Short description for file.
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.tests.fixtures
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Short description for class.
 *
 * @package       cake
 * @subpackage    cake.tests.fixtures
 */
class CategoryFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'Category'
 * @access public
 */
	public $name = 'Category';

/**
 * fields property
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => false),
		'name' => array('type' => 'string', 'null' => false),
		'created' => 'datetime',
		'updated' => 'datetime'
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	public $records = array(
		array('parent_id' => 0, 'name' => 'Category 1', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 1, 'name' => 'Category 1.1', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 1, 'name' => 'Category 1.2', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 0, 'name' => 'Category 2', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 0, 'name' => 'Category 3', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 5, 'name' => 'Category 3.1', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 2, 'name' => 'Category 1.1.1', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
		array('parent_id' => 2, 'name' => 'Category 1.1.2', 'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'),
	);
}
