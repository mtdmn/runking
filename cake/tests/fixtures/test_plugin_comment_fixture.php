<?php
/**
 * Short description for file.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       cake
 * @subpackage    cake.tests.fixtures
 * @since         CakePHP(tm) v 7660
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */

/**
 * Short description for class.
 *
 * @package       cake
 * @subpackage    cake.tests.fixtures
 */
class TestPluginCommentFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'Comment'
 * @access public
 */
	public $name = 'TestPluginComment';

/**
 * fields property
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'article_id' => array('type' => 'integer', 'null'=>false),
		'user_id' => array('type' => 'integer', 'null'=>false),
		'comment' => 'text',
		'published' => array('type' => 'string', 'length' => 1, 'default' => 'N'),
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
		array('id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Plugin Article', 'published' => 'Y', 'created' => '2008-09-24 10:45:23', 'updated' => '2008-09-24 10:47:31'),
		array('id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Plugin Article', 'published' => 'Y', 'created' => '2008-09-24 10:47:23', 'updated' => '2008-09-24 10:49:31'),
		array('id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Plugin Article', 'published' => 'Y', 'created' => '2008-09-24 10:49:23', 'updated' => '2008-09-24 10:51:31'),
		array('id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Plugin Article', 'published' => 'N', 'created' => '2008-09-24 10:51:23', 'updated' => '2008-09-24 10:53:31'),
		array('id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Plugin Article', 'published' => 'Y', 'created' => '2008-09-24 10:53:23', 'updated' => '2008-09-24 10:55:31'),
		array('id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Plugin Article', 'published' => 'Y', 'created' => '2008-09-24 10:55:23', 'updated' => '2008-09-24 10:57:31')
	);
}
