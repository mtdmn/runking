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
 * @package       cake.tests.fixtures
 * @since         CakePHP(tm) v 1.2.0.5669
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Short description for class.
 *
 * @package       cake.tests.fixtures
 */
class TranslateFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'Translate'
 * @access public
 */
	public $name = 'Translate';

/**
 * table property
 *
 * @var string 'i18n'
 * @access public
 */
	public $table = 'i18n';

/**
 * fields property
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'locale' => array('type' => 'string', 'length' => 6, 'null' => false),
		'model' => array('type' => 'string', 'null' => false),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'field' => array('type' => 'string', 'null' => false),
		'content' => array('type' => 'text')
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	public $records = array(
		array('locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'title', 'content' => 'Title #1'),
		array('locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'content', 'content' => 'Content #1'),
		array('locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'title', 'content' => 'Titel #1'),
		array('locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'content', 'content' => 'Inhalt #1'),
		array('locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'title', 'content' => 'Titulek #1'),
		array('locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'content', 'content' => 'Obsah #1'),
		array('locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'title', 'content' => 'Title #2'),
		array('locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'content', 'content' => 'Content #2'),
		array('locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'title', 'content' => 'Titel #2'),
		array('locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'content', 'content' => 'Inhalt #2'),
		array('locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'title', 'content' => 'Titulek #2'),
		array('locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'content', 'content' => 'Obsah #2'),
		array('locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'title', 'content' => 'Title #3'),
		array('locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'content', 'content' => 'Content #3'),
		array('locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'title', 'content' => 'Titel #3'),
		array('locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'content', 'content' => 'Inhalt #3'),
		array('locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'title', 'content' => 'Titulek #3'),
		array('locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'content', 'content' => 'Obsah #3')
	);
}
