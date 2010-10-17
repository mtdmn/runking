<?php
/**
 * Test for Schema database management
 *
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
 * @subpackage    cake.tests.cases.libs
 * @since         CakePHP(tm) v 1.2.0.5550
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Model', 'CakeSchema', false);

/**
 * Test for Schema database management
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs
 */
class MyAppSchema extends CakeSchema {

/**
 * name property
 *
 * @var string 'MyApp'
 * @access public
 */
	public $name = 'MyApp';

/**
 * connection property
 *
 * @var string 'test'
 * @access public
 */
	public $connection = 'test';

/**
 * comments property
 *
 * @var array
 * @access public
 */
	public $comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
		'post_id' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'user_id' => array('type' => 'integer', 'null' => false),
		'title' => array('type' => 'string', 'null' => false, 'length' => 100),
		'comment' => array('type' => 'text', 'null' => false, 'default' => null),
		'published' => array('type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
	);

/**
 * posts property
 *
 * @var array
 * @access public
 */
	public $posts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
		'author_id' => array('type' => 'integer', 'null' => true, 'default' => ''),
		'title' => array('type' => 'string', 'null' => false, 'default' => 'Title'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null),
		'summary' => array('type' => 'text', 'null' => true),
		'published' => array('type' => 'string', 'null' => true, 'default' => 'Y', 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
	);

/**
 * _foo property
 *
 * @var array
 * @access protected
 */
	protected $_foo = array('bar');

/**
 * setup method
 *
 * @param mixed $version
 * @access public
 * @return void
 */
	function setup($version) {
	}

/**
 * teardown method
 *
 * @param mixed $version
 * @access public
 * @return void
 */
	function teardown($version) {
	}

/**
 * getVar method
 *
 * @param string $var Name of var
 * @return mixed
 */
	public function getVar($var) {
		if (!isset($this->$var)) {
			return null;
		}
		return $this->$var;
	}
}

/**
 * TestAppSchema class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class TestAppSchema extends CakeSchema {

/**
 * name property
 *
 * @var string 'MyApp'
 * @access public
 */
	public $name = 'MyApp';

/**
 * comments property
 *
 * @var array
 * @access public
 */
	public $comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => 0,'key' => 'primary'),
		'article_id' => array('type' => 'integer', 'null' => false),
		'user_id' => array('type' => 'integer', 'null' => false),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null),
		'published' => array('type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
		'tableParameters' => array(),
	);

/**
 * posts property
 *
 * @var array
 * @access public
 */
	public $posts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
		'author_id' => array('type' => 'integer', 'null' => false),
		'title' => array('type' => 'string', 'null' => false),
		'body' => array('type' => 'text', 'null' => true, 'default' => null),
		'published' => array('type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
		'tableParameters' => array(),
	);

/**
 * posts_tags property
 *
 * @var array
 * @access public
 */
	public $posts_tags = array(
		'post_id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
		'tag_id' => array('type' => 'string', 'null' => false, 'key' => 'primary'),
		'indexes' => array('posts_tag' => array('column' => array('tag_id', 'post_id'), 'unique' => 1)),
		'tableParameters' => array()
	);

/**
 * tags property
 *
 * @var array
 * @access public
 */
	public $tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
		'tag' => array('type' => 'string', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
		'tableParameters' => array()
	);

/**
 * datatypes property
 *
 * @var array
 * @access public
 */
	public $datatypes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
		'float_field' => array('type' => 'float', 'null' => false, 'length' => '5,2', 'default' => '', 'collate' => null, 'comment' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
		'tableParameters' => array()
	);

/**
 * setup method
 *
 * @param mixed $version
 * @access public
 * @return void
 */
	function setup($version) {
	}

/**
 * teardown method
 *
 * @param mixed $version
 * @access public
 * @return void
 */
	function teardown($version) {
	}
}

/**
 * SchmeaPost class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaPost extends CakeTestModel {

/**
 * name property
 *
 * @var string 'SchemaPost'
 * @access public
 */
	public $name = 'SchemaPost';

/**
 * useTable property
 *
 * @var string 'posts'
 * @access public
 */
	public $useTable = 'posts';

/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	public $hasMany = array('SchemaComment');

/**
 * hasAndBelongsToMany property
 *
 * @var array
 * @access public
 */
	public $hasAndBelongsToMany = array('SchemaTag');
}

/**
 * SchemaComment class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaComment extends CakeTestModel {

/**
 * name property
 *
 * @var string 'SchemaComment'
 * @access public
 */
	public $name = 'SchemaComment';

/**
 * useTable property
 *
 * @var string 'comments'
 * @access public
 */
	public $useTable = 'comments';

/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	public $belongsTo = array('SchemaPost');
}

/**
 * SchemaTag class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaTag extends CakeTestModel {

/**
 * name property
 *
 * @var string 'SchemaTag'
 * @access public
 */
	public $name = 'SchemaTag';

/**
 * useTable property
 *
 * @var string 'tags'
 * @access public
 */
	public $useTable = 'tags';

/**
 * hasAndBelongsToMany property
 *
 * @var array
 * @access public
 */
	public $hasAndBelongsToMany = array('SchemaPost');
}

/**
 * SchemaDatatype class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaDatatype extends CakeTestModel {

/**
 * name property
 *
 * @var string 'SchemaDatatype'
 * @access public
 */
	public $name = 'SchemaDatatype';

/**
 * useTable property
 *
 * @var string 'datatypes'
 * @access public
 */
	public $useTable = 'datatypes';
}

/**
 * Testdescribe class
 *
 * This class is defined purely to inherit the cacheSources variable otherwise
 * testSchemaCreatTable will fail if listSources has already been called and
 * its source cache populated - I.e. if the test is run within a group
 *
 * @uses          CakeTestModel
 * @package
 * @subpackage    cake.tests.cases.libs.model
 */
class Testdescribe extends CakeTestModel {

/**
 * name property
 *
 * @var string 'Testdescribe'
 * @access public
 */
	public $name = 'Testdescribe';
}

/**
 * SchemaCrossDatabase class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaCrossDatabase extends CakeTestModel {

/**
 * name property
 *
 * @var string 'SchemaCrossDatabase'
 * @access public
 */
	public $name = 'SchemaCrossDatabase';

/**
 * useTable property
 *
 * @var string 'posts'
 * @access public
 */
	public $useTable = 'cross_database';

/**
 * useDbConfig property
 *
 * @var string 'test2'
 * @access public
 */
	public $useDbConfig = 'test2';
}

/**
 * SchemaCrossDatabaseFixture class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaCrossDatabaseFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'CrossDatabase'
 * @access public
 */
	public $name = 'CrossDatabase';

/**
 * table property
 *
 * @access public
 */
	public $table = 'cross_database';

/**
 * fields property
 *
 * @var array
 * @access public
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => 'string'
	);

/**
 * records property
 *
 * @var array
 * @access public
 */
	public $records = array(
		array('id' => 1, 'name' => 'First'),
		array('id' => 2, 'name' => 'Second'),
	);
}

/**
 * SchemaPrefixAuthUser class
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs.model
 */
class SchemaPrefixAuthUser extends CakeTestModel {
/**
 * name property
 *
 * @var string
 */
	public $name = 'SchemaPrefixAuthUser';
/**
 * table prefix
 *
 * @var string
 */
	public $tablePrefix = 'auth_';
/**
 * useTable
 *
 * @var string
 */
	public $useTable = 'users';
}

/**
 * CakeSchemaTest
 *
 * @package       cake
 * @subpackage    cake.tests.cases.libs
 */
class CakeSchemaTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	public $fixtures = array(
		'core.post', 'core.tag', 'core.posts_tag', 'core.test_plugin_comment', 
		'core.datatype', 'core.auth_user', 'core.author',
		'core.test_plugin_article', 'core.user', 'core.comment'
	);

/**
 * setUp method
 *
 * @return void
 */
	function setUp() {
		parent::setUp();
		ConnectionManager::getDataSource('test')->cacheSources = false;
		$this->Schema = new TestAppSchema();
	}

/**
 * tearDown method
 *
 * @return void
 */
	function tearDown() {
		parent::tearDown();
		@unlink(TMP . 'tests' . DS .'schema.php');
		unset($this->Schema);
	}

/**
 * testSchemaName method
 *
 * @access public
 * @return void
 */
	function testSchemaName() {
		$Schema = new CakeSchema();
		$this->assertEqual(strtolower($Schema->name), strtolower(APP_DIR));

		Configure::write('App.dir', 'Some.name.with.dots');
		$Schema = new CakeSchema();
		$this->assertEqual($Schema->name, 'SomeNameWithDots');

		Configure::write('App.dir', 'app');
	}

/**
 * testSchemaRead method
 *
 * @access public
 * @return void
 */
	function testSchemaRead() {
		$read = $this->Schema->read(array(
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => array('SchemaPost', 'SchemaComment', 'SchemaTag', 'SchemaDatatype')
		));
		unset($read['tables']['missing']);

		$expected = array('comments', 'datatypes', 'posts', 'posts_tags', 'tags');
		foreach ($expected as $table) {
			$this->assertTrue(isset($read['tables'][$table]), 'Missing table ' . $table);
		}
		foreach ($this->Schema->tables as $table => $fields) {
			$this->assertEqual(array_keys($fields), array_keys($read['tables'][$table]));
		}

		$this->assertEqual(
			$read['tables']['datatypes']['float_field'],
			$this->Schema->tables['datatypes']['float_field']
		);

		$db = ConnectionManager::getDataSource('test');
		$config = $db->config;
		$config['prefix'] = 'schema_test_prefix_';
		ConnectionManager::create('schema_prefix', $config);
		$read = $this->Schema->read(array('connection' => 'schema_prefix', 'models' => false));
		$this->assertTrue(empty($read['tables']));

		$SchemaPost = ClassRegistry::init('SchemaPost');
		$SchemaPost->table = 'sts';
		$SchemaPost->tablePrefix = 'po';
		$read = $this->Schema->read(array(
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => array('SchemaPost')
		));
		$this->assertFalse(isset($read['tables']['missing']['posts']), 'Posts table was not read from tablePrefix %s');

		$read = $this->Schema->read(array(
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => array('SchemaComment', 'SchemaTag', 'SchemaPost')
		));
		$this->assertFalse(isset($read['tables']['missing']['posts_tags']), 'Join table marked as missing %s');
	}

/**
 * test read() with tablePrefix properties.
 *
 * @return void
 */
	function testSchemaReadWithTablePrefix() {
		$model = new SchemaPrefixAuthUser();

		$Schema = new CakeSchema();
		$read = $Schema->read(array(
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => array('SchemaPrefixAuthUser')
		));
		unset($read['tables']['missing']);
		$this->assertTrue(isset($read['tables']['auth_users']), 'auth_users key missing %s');

	}

/**
 * test reading schema from plugins.
 *
 * @return void
 */
	function testSchemaReadWithPlugins() {
		App::objects('model', null, false);
		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS)
		));

		$Schema = new CakeSchema();
		$Schema->plugin = 'TestPlugin';
		$read = $Schema->read(array(
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => true
		));
		unset($read['tables']['missing']);
		$this->assertTrue(isset($read['tables']['auth_users']));
		$this->assertTrue(isset($read['tables']['authors']));
		$this->assertTrue(isset($read['tables']['test_plugin_comments']));
		$this->assertTrue(isset($read['tables']['posts']));
		$this->assertTrue(count($read['tables']) >= 4);

		App::build();
	}

/**
 * test reading schema with tables from another database.
 *
 * @return void
 */
	function testSchemaReadWithCrossDatabase() {
		$config = new DATABASE_CONFIG();
		$skip = $this->skipIf(
			!isset($config->test) || !isset($config->test2),
			 '%s Primary and secondary test databases not configured, skipping cross-database '
			.'join tests.'
			.' To run these tests, you must define $test and $test2 in your database configuration.'
		);
		if ($skip) {
			return;
		}

		$db2 = ConnectionManager::getDataSource('test2');
		$fixture = new SchemaCrossDatabaseFixture();
		$fixture->create($db2);
		$fixture->insert($db2);

		$read = $this->Schema->read(array(
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => array('SchemaCrossDatabase', 'SchemaPost')
		));
		$this->assertTrue(isset($read['tables']['posts']));
		$this->assertFalse(isset($read['tables']['cross_database']), 'Cross database should not appear');
		$this->assertFalse(isset($read['tables']['missing']['cross_database']), 'Cross database should not appear');

		$read = $this->Schema->read(array(
			'connection' => 'test2',
			'name' => 'TestApp',
			'models' => array('SchemaCrossDatabase', 'SchemaPost')
		));
		$this->assertFalse(isset($read['tables']['posts']), 'Posts should not appear');
		$this->assertFalse(isset($read['tables']['posts']), 'Posts should not appear');
		$this->assertTrue(isset($read['tables']['cross_database']));

		$fixture->drop($db2);
	}

/**
 * test that tables are generated correctly
 *
 * @return void
 */
	function testGenerateTable() {
		$posts = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
			'author_id' => array('type' => 'integer', 'null' => false),
			'title' => array('type' => 'string', 'null' => false),
			'body' => array('type' => 'text', 'null' => true, 'default' => null),
			'published' => array('type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true)),
		);
		$result = $this->Schema->generateTable('posts', $posts);
		$this->assertPattern('/var \$posts/', $result);
	}
/**
 * testSchemaWrite method
 *
 * @access public
 * @return void
 */
	function testSchemaWrite() {
		$write = $this->Schema->write(array('name' => 'MyOtherApp', 'tables' => $this->Schema->tables, 'path' => TMP . 'tests'));
		$file = file_get_contents(TMP . 'tests' . DS .'schema.php');
		$this->assertEqual($write, $file);

		require_once( TMP . 'tests' . DS .'schema.php');
		$OtherSchema = new MyOtherAppSchema();
		$this->assertEqual($this->Schema->tables, $OtherSchema->tables);
	}

/**
 * testSchemaComparison method
 *
 * @access public
 * @return void
 */
	function testSchemaComparison() {
		$New = new MyAppSchema();
		$compare = $New->compare($this->Schema);
		$expected = array(
			'comments' => array(
				'add' => array(
					'post_id' => array('type' => 'integer', 'null' => false, 'default' => 0),
					'title' => array('type' => 'string', 'null' => false, 'length' => 100),
				),
				'drop' => array(
					'article_id' => array('type' => 'integer', 'null' => false),
					'tableParameters' => array(),
				),
				'change' => array(
					'comment' => array('type' => 'text', 'null' => false, 'default' => null),
				)
			),
			'posts' => array(
				'add' => array(
					'summary' => array('type' => 'text', 'null' => true),
				),
				'drop' => array(
					'tableParameters' => array(),
				),
				'change' => array(
					'author_id' => array('type' => 'integer', 'null' => true, 'default' => ''),
					'title' => array('type' => 'string', 'null' => false, 'default' => 'Title'),
					'published' => array('type' => 'string', 'null' => true, 'default' => 'Y', 'length' => 1)
				)
			),
		);
		$this->assertEqual($expected, $compare);
		$this->assertNull($New->getVar('comments'));
		$this->assertEqual($New->getVar('_foo'), array('bar'));

		$tables = array(
			'missing' => array(
				'categories' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
					'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
				)
			),
			'ratings' => array(
				'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
				'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => NULL),
				'model' => array('type' => 'varchar', 'null' => false, 'default' => NULL),
				'value' => array('type' => 'float', 'null' => false, 'length' => '5,2', 'default' => NULL),
				'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
				'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
				'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
				'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
			)
		);
		$compare = $New->compare($this->Schema, $tables);
		$expected = array(
			'ratings' => array(
				'add' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
					'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => NULL),
					'model' => array('type' => 'varchar', 'null' => false, 'default' => NULL),
					'value' => array('type' => 'float', 'null' => false, 'length' => '5,2', 'default' => NULL),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
				)
			)
		);
		$this->assertEqual($expected, $compare);
	}

/**
 * Test comparing tableParameters and indexes.
 *
 * @return void
 */
	function testTableParametersAndIndexComparison() {
		$old = array(
			'posts' => array(
				'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
				'author_id' => array('type' => 'integer', 'null' => false),
				'title' => array('type' => 'string', 'null' => false),
				'indexes' => array(
					'PRIMARY' => array('column' => 'id', 'unique' => true)
				),
				'tableParameters' => array(
					'charset' => 'latin1',
					'collate' => 'latin1_general_ci'
				)
			),
			'comments' => array(
				'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
				'post_id' => array('type' => 'integer', 'null' => false, 'default' => 0),
				'comment' => array('type' => 'text'),
				'indexes' => array(
					'PRIMARY' => array('column' => 'id', 'unique' => true),
					'post_id' => array('column' => 'post_id'),
				),
				'tableParameters' => array(
					'engine' => 'InnoDB',
					'charset' => 'latin1',
					'collate' => 'latin1_general_ci'
				)
			)
		);
		$new = array(
			'posts' => array(
				'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
				'author_id' => array('type' => 'integer', 'null' => false),
				'title' => array('type' => 'string', 'null' => false),
				'indexes' => array(
					'PRIMARY' => array('column' => 'id', 'unique' => true),
					'author_id' => array('column' => 'author_id'),
				),
				'tableParameters' => array(
					'charset' => 'utf8',
					'collate' => 'utf8_general_ci',
					'engine' => 'MyISAM'
				)
			),
			'comments' => array(
				'id' => array('type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'),
				'post_id' => array('type' => 'integer', 'null' => false, 'default' => 0),
				'comment' => array('type' => 'text'),
				'indexes' => array(
					'PRIMARY' => array('column' => 'id', 'unique' => true),
				),
				'tableParameters' => array(
					'charset' => 'utf8',
					'collate' => 'utf8_general_ci'
				)
			)
		);
		$compare = $this->Schema->compare($old, $new);
		$expected = array(
			'posts' => array(
				'add' => array(
					'indexes' => array('author_id' => array('column' => 'author_id')),
				),
				'change' => array(
					'tableParameters' => array(
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
						'engine' => 'MyISAM'
					)
				)
			),
			'comments' => array(
				'drop' => array(
					'indexes' => array('post_id' => array('column' => 'post_id')),
				),
				'change' => array(
					'tableParameters' => array(
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
					)
				)
			)
		);
		$this->assertEqual($compare, $expected);
	}

/**
 * testSchemaLoading method
 *
 * @access public
 * @return void
 */
	function testSchemaLoading() {
		$Other = $this->Schema->load(array('name' => 'MyOtherApp', 'path' => TMP . 'tests'));
		$this->assertEqual($Other->name, 'MyOtherApp');
		$this->assertEqual($Other->tables, $this->Schema->tables);
	}

/**
 * test loading schema files inside of plugins.
 *
 * @return void
 */
	function testSchemaLoadingFromPlugin() {
		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS)
		));
		$Other = $this->Schema->load(array('name' => 'TestPluginApp', 'plugin' => 'TestPlugin'));
		$this->assertEqual($Other->name, 'TestPluginApp');
		$this->assertEqual(array_keys($Other->tables), array('acos'));

		App::build();
	}

/**
 * testSchemaCreateTable method
 *
 * @access public
 * @return void
 */
	function testSchemaCreateTable() {
		$db = ConnectionManager::getDataSource('test');
		$db->cacheSources = false;

		$Schema = new CakeSchema(array(
			'connection' => 'test',
			'testdescribes' => array(
				'id' => array('type' => 'integer', 'key' => 'primary'),
				'int_null' => array('type' => 'integer', 'null' => true),
				'int_not_null' => array('type' => 'integer', 'null' => false),
			),
		));
		$sql = $db->createSchema($Schema);

		$col = $Schema->tables['testdescribes']['int_null'];
		$col['name'] = 'int_null';
		$column = $this->db->buildColumn($col);
		$this->assertPattern('/' . preg_quote($column, '/') . '/', $sql);

		$col = $Schema->tables['testdescribes']['int_not_null'];
		$col['name'] = 'int_not_null';
		$column = $this->db->buildColumn($col);
		$this->assertPattern('/' . preg_quote($column, '/') . '/', $sql);
	}
}
