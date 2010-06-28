<?php
/**
 * DispatcherTest file
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
 * @subpackage    cake.tests.cases
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
require_once CAKE . 'dispatcher.php';

if (!class_exists('AppController')) {
	require_once LIBS . 'controller' . DS . 'app_controller.php';
} elseif (!defined('APP_CONTROLLER_EXISTS')){
	define('APP_CONTROLLER_EXISTS', true);
}

/**
 * TestDispatcher class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class TestDispatcher extends Dispatcher {

/**
 * invoke method
 *
 * @param mixed $controller
 * @param mixed $params
 * @param mixed $missingAction
 * @return void
 */
	protected function _invoke(&$controller, $params) {
		restore_error_handler();
		if ($result = parent::_invoke($controller, $params)) {
			if ($result[0] === 'missingAction') {
				return $result;
			}
		}
		set_error_handler('simpleTestErrorHandler');

		return $controller;
	}

/**
 * cakeError method
 *
 * @param mixed $filename
 * @return void
 */
	public function cakeError($filename, $params) {
		return array($filename, $params);
	}

/**
 * _stop method
 *
 * @return void
 */
	protected function _stop() {
		$this->stopped = true;
		return true;
	}
}

/**
 * MyPluginAppController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class MyPluginAppController extends AppController {
}

/**
 * MyPluginController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class MyPluginController extends MyPluginAppController {

/**
 * name property
 *
 * @var string 'MyPlugin'
 * @access public
 */
	public $name = 'MyPlugin';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return true;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		return true;
	}

/**
 * admin_add method
 *
 * @param mixed $id
 * @return void
 */
	public function admin_add($id = null) {
		return $id;
	}
}

/**
 * SomePagesController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class SomePagesController extends AppController {

/**
 * name property
 *
 * @var string 'SomePages'
 * @access public
 */
	public $name = 'SomePages';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * display method
 *
 * @param mixed $page
 * @return void
 */
	public function display($page = null) {
		return $page;
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return true;
	}

/**
 * protected method
 *
 * @return void
 */
	protected function _protected() {
		return true;
	}

/**
 * redirect method overriding
 *
 * @return void
 */
	public function redirect() {
		echo 'this should not be accessible';
	}
}

/**
 * OtherPagesController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class OtherPagesController extends MyPluginAppController {

/**
 * name property
 *
 * @var string 'OtherPages'
 * @access public
 */
	public $name = 'OtherPages';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * display method
 *
 * @param mixed $page
 * @return void
 */
	public function display($page = null) {
		return $page;
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return true;
	}
}

/**
 * TestDispatchPagesController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class TestDispatchPagesController extends AppController {

/**
 * name property
 *
 * @var string 'TestDispatchPages'
 * @access public
 */
	public $name = 'TestDispatchPages';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		return true;
	}

/**
 * camelCased method
 *
 * @return void
 */
	public function camelCased() {
		return true;
	}
}

/**
 * ArticlesTestAppController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class ArticlesTestAppController extends AppController {
}

/**
 * ArticlesTestController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class ArticlesTestController extends ArticlesTestAppController {

/**
 * name property
 *
 * @var string 'ArticlesTest'
 * @access public
 */
	public $name = 'ArticlesTest';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		return true;
	}
/**
 * fake index method.
 *
 * @return void
 */
	function index() {
		return true;
	}
}

/**
 * SomePostsController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class SomePostsController extends AppController {

/**
 * name property
 *
 * @var string 'SomePosts'
 * @access public
 */
	public $name = 'SomePosts';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * autoRender property
 *
 * @var bool false
 * @access public
 */
	public $autoRender = false;

/**
 * beforeFilter method
 *
 * @return void
 */
	public function beforeFilter() {
		if ($this->params['action'] == 'index') {
			$this->params['action'] = 'view';
		} else {
			$this->params['action'] = 'change';
		}
		$this->params['pass'] = array('changed');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return true;
	}

/**
 * change method
 *
 * @return void
 */
	public function change() {
		return true;
	}
}

/**
 * TestCachedPagesController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class TestCachedPagesController extends AppController {

/**
 * name property
 *
 * @var string 'TestCachedPages'
 * @access public
 */
	public $name = 'TestCachedPages';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * helpers property
 *
 * @var array
 * @access public
 */
	public $helpers = array('Cache');

/**
 * cacheAction property
 *
 * @var array
 * @access public
 */
	public $cacheAction = array(
		'index' => '+2 sec',
		'test_nocache_tags' => '+2 sec',
		'view' => '+2 sec'
	);

/**
 * viewPath property
 *
 * @var string 'posts'
 * @access public
 */
	public $viewPath = 'posts';

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->render();
	}

/**
 * test_nocache_tags method
 *
 * @return void
 */
	public function test_nocache_tags() {
		$this->render();
	}

/**
 * view method
 *
 * @return void
 */
	public function view($id = null) {
		$this->render('index');
	}
/**
 * test cached forms / tests view object being registered
 *
 * @return void
 */
	function cache_form() {
		$this->cacheAction = 10;
		$this->helpers[] = 'Form';
	}
}

/**
 * TimesheetsController class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class TimesheetsController extends AppController {

/**
 * name property
 *
 * @var string 'Timesheets'
 * @access public
 */
	public $name = 'Timesheets';

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = array();

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return true;
	}
}

/**
 * DispatcherTest class
 *
 * @package       cake
 * @subpackage    cake.tests.cases
 */
class DispatcherTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function startTest() {
		$this->_get = $_GET;
		$_GET = array();
		$this->_post = $_POST;
		$this->_files = $_FILES;
		$this->_server = $_SERVER;

		$this->_app = Configure::read('App');
		Configure::write('App.base', false);
		Configure::write('App.baseUrl', false);
		Configure::write('App.dir', 'app');
		Configure::write('App.webroot', 'webroot');

		$this->_cache = Configure::read('Cache');
		Configure::write('Cache.disable', true);

		$this->_debug = Configure::read('debug');

		App::build(App::core());
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function endTest() {
		$_GET = $this->_get;
		$_POST = $this->_post;
		$_FILES = $this->_files;
		$_SERVER = $this->_server;
		App::build();
		Configure::write('App', $this->_app);
		Configure::write('Cache', $this->_cache);
		Configure::write('debug', $this->_debug);
	}

/**
 * testParseParamsWithoutZerosAndEmptyPost method
 *
 * @return void
 */
	public function testParseParamsWithoutZerosAndEmptyPost() {
		$Dispatcher = new Dispatcher();
		$test = $Dispatcher->parseParams(new CakeRequest("/testcontroller/testaction/params1/params2/params3"));
		$this->assertIdentical($test['controller'], 'testcontroller');
		$this->assertIdentical($test['action'], 'testaction');
		$this->assertIdentical($test['pass'][0], 'params1');
		$this->assertIdentical($test['pass'][1], 'params2');
		$this->assertIdentical($test['pass'][2], 'params3');
		$this->assertFalse(!empty($test['form']));
	}

/**
 * testParseParamsReturnsPostedData method
 *
 * @return void
 */
	public function testParseParamsReturnsPostedData() {
		$_POST['testdata'] = "My Posted Content";
		$Dispatcher = new Dispatcher();

		$test = $Dispatcher->parseParams(new CakeRequest("/"));
		$this->assertTrue(isset($test['form']), "Parsed URL not returning post data");
		$this->assertIdentical($test['form']['testdata'], "My Posted Content");
	}

/**
 * testParseParamsWithSingleZero method
 *
 * @return void
 */
	public function testParseParamsWithSingleZero() {
		$Dispatcher = new Dispatcher();
		$test = $Dispatcher->parseParams(new CakeRequest("/testcontroller/testaction/1/0/23"));
		$this->assertIdentical($test['controller'], 'testcontroller');
		$this->assertIdentical($test['action'], 'testaction');
		$this->assertIdentical($test['pass'][0], '1');
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][1]);
		$this->assertIdentical($test['pass'][2], '23');
	}

/**
 * testParseParamsWithManySingleZeros method
 *
 * @return void
 */
	public function testParseParamsWithManySingleZeros() {
		$Dispatcher = new Dispatcher();
		$test = $Dispatcher->parseParams(new CakeRequest("/testcontroller/testaction/0/0/0/0/0/0"));
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][0]);
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][1]);
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][2]);
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][3]);
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][4]);
		$this->assertPattern('/\\A(?:0)\\z/', $test['pass'][5]);
	}

/**
 * testParseParamsWithManyZerosInEachSectionOfUrl method
 *
 * @return void
 */
	public function testParseParamsWithManyZerosInEachSectionOfUrl() {
		$Dispatcher = new Dispatcher();
		$request = new CakeRequest("/testcontroller/testaction/000/0000/00000/000000/000000/0000000");
		$test = $Dispatcher->parseParams($request);
		$this->assertPattern('/\\A(?:000)\\z/', $test['pass'][0]);
		$this->assertPattern('/\\A(?:0000)\\z/', $test['pass'][1]);
		$this->assertPattern('/\\A(?:00000)\\z/', $test['pass'][2]);
		$this->assertPattern('/\\A(?:000000)\\z/', $test['pass'][3]);
		$this->assertPattern('/\\A(?:000000)\\z/', $test['pass'][4]);
		$this->assertPattern('/\\A(?:0000000)\\z/', $test['pass'][5]);
	}

/**
 * testParseParamsWithMixedOneToManyZerosInEachSectionOfUrl method
 *
 * @return void
 */
	public function testParseParamsWithMixedOneToManyZerosInEachSectionOfUrl() {
		$Dispatcher = new Dispatcher();
		$request = new CakeRequest("/testcontroller/testaction/01/0403/04010/000002/000030/0000400");
		$test = $Dispatcher->parseParams($request);
		$this->assertPattern('/\\A(?:01)\\z/', $test['pass'][0]);
		$this->assertPattern('/\\A(?:0403)\\z/', $test['pass'][1]);
		$this->assertPattern('/\\A(?:04010)\\z/', $test['pass'][2]);
		$this->assertPattern('/\\A(?:000002)\\z/', $test['pass'][3]);
		$this->assertPattern('/\\A(?:000030)\\z/', $test['pass'][4]);
		$this->assertPattern('/\\A(?:0000400)\\z/', $test['pass'][5]);
	}

/**
 * testQueryStringOnRoot method
 *
 * @return void
 */
	public function testQueryStringOnRoot() {
		Router::reload();
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

		$_GET = array('coffee' => 'life', 'sleep' => 'sissies');
		$Dispatcher = new Dispatcher();
		$uri = new CakeRequest('posts/home/?coffee=life&sleep=sissies');
		$result = $Dispatcher->parseParams($uri);
		$this->assertPattern('/posts/', $result['controller']);
		$this->assertPattern('/home/', $result['action']);
		$this->assertTrue(isset($result['url']['sleep']));
		$this->assertTrue(isset($result['url']['coffee']));

		$Dispatcher = new Dispatcher();
		$uri = new CakeRequest('/?coffee=life&sleep=sissy');
		$result = $Dispatcher->parseParams($uri);
		$this->assertPattern('/pages/', $result['controller']);
		$this->assertPattern('/display/', $result['action']);
		$this->assertTrue(isset($result['url']['sleep']));
		$this->assertTrue(isset($result['url']['coffee']));
		$this->assertEqual($result['url']['coffee'], 'life');
	}

/**
 * testPrivate method
 *
 * @return void
 */
	public function testPrivate() {
		$Dispatcher =& new TestDispatcher();
		Configure::write('App.baseUrl','/index.php');
		$url = 'some_pages/_protected/param:value/param2:value2';

		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$expected = array('privateAction', array(array(
			'className' => 'SomePagesController',
			'action' => '_protected',
			'webroot' => '/',
			'url' => 'some_pages/_protected/param:value/param2:value2',
			'base' => '/index.php'
		)));
		$this->assertEqual($controller, $expected);
	}

/**
 * testMissingAction method
 *
 * @return void
 */
	public function testMissingAction() {
		$Dispatcher =& new TestDispatcher();
		Configure::write('App.baseUrl', '/index.php');
		$url = 'some_pages/home/param:value/param2:value2';

		$controller = $Dispatcher->dispatch($url, array('return'=> 1));

		$expected = array('missingAction', array(array(
			'className' => 'SomePagesController',
			'action' => 'home',
			'webroot' => '/',
			'url' => '/index.php/some_pages/home/param:value/param2:value2',
			'base' => '/index.php'
		)));
		$this->assertEqual($expected, $controller);

		$Dispatcher =& new TestDispatcher();
		Configure::write('App.baseUrl','/index.php');
		$url = 'some_pages/redirect/param:value/param2:value2';

		$controller = $Dispatcher->dispatch($url, array('return'=> 1));

		$expected = array('missingAction', array(array(
			'className' => 'SomePagesController',
			'action' => 'redirect',
			'webroot' => '/',
			'url' => '/index.php/some_pages/redirect/param:value/param2:value2',
			'base' => '/index.php'
		)));
		$this->assertEqual($expected, $controller);
	}

/**
 * testDispatch method
 *
 * @return void
 */
	public function testDispatch() {
		$Dispatcher = new TestDispatcher();
		Configure::write('App.baseUrl','/index.php');
		$url = 'pages/home/param:value/param2:value2';

		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$expected = 'Pages';
		$this->assertEqual($expected, $controller->name);

		$expected = array('0' => 'home', 'param' => 'value', 'param2' => 'value2');
		$this->assertIdentical($expected, $controller->passedArgs);

		Configure::write('App.baseUrl','/pages/index.php');

		$url = 'pages/home';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$expected = 'Pages';
		$this->assertEqual($expected, $controller->name);

		$url = 'pages/home/';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertNull($controller->plugin);
		$this->assertNull($Dispatcher->params['plugin']);

		$expected = 'Pages';
		$this->assertEqual($expected, $controller->name);

		unset($Dispatcher);

		$Dispatcher = new TestDispatcher();
		Configure::write('App.baseUrl', '/timesheets/index.php');

		$url = 'timesheets';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$expected = 'Timesheets';
		$this->assertEqual($expected, $controller->name);

		$url = 'timesheets/';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$this->assertEqual('Timesheets', $controller->name);
		$this->assertEqual('/timesheets/index.php', $Dispatcher->request->base);


		$url = 'test_dispatch_pages/camelCased';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertEqual('TestDispatchPages', $controller->name);
	
		$url = 'test_dispatch_pages/camelCased/something. .';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertEqual($controller->params['pass'][0], 'something. .', 'Period was chopped off. %s');
	}

/**
 * testDispatchWithArray method
 *
 * @return void
 */
	public function testDispatchWithArray() {
		Router::reload();
		Configure::write('App.baseUrl', '/index.php');
		$Dispatcher = new TestDispatcher();

		$url = array('controller' => 'pages', 'action' => 'display');
		$controller = $Dispatcher->dispatch($url, array(
			'pass' => array('home'),
			'named' => array('param' => 'value', 'param2' => 'value2'),
			'return' => 1
		));
		$expected = 'Pages';
		$this->assertEqual($expected, $controller->name);

		$expected = array('0' => 'home', 'param' => 'value', 'param2' => 'value2');
		$this->assertIdentical($expected, $controller->passedArgs);
	}

/**
 * testAdminDispatch method
 *
 * @return void
 */
	public function testAdminDispatch() {
		$_POST = array();
		$Dispatcher = new TestDispatcher();
		Configure::write('Routing.prefixes', array('admin'));
		Configure::write('App.baseUrl','/cake/repo/branches/1.2.x.x/index.php');
		$url = 'admin/test_dispatch_pages/index/param:value/param2:value2';

		Router::reload();
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$this->assertEqual($controller->name, 'TestDispatchPages');

		$this->assertIdentical($controller->passedArgs, array('param' => 'value', 'param2' => 'value2'));
		$this->assertTrue($controller->params['admin']);

		$expected = '/cake/repo/branches/1.2.x.x/index.php/admin/test_dispatch_pages/index/param:value/param2:value2';
		$this->assertIdentical($expected, $controller->here);

		$expected = '/cake/repo/branches/1.2.x.x/index.php';
		$this->assertIdentical($expected, $controller->base);
	}

/**
 * testPluginDispatch method
 *
 * @return void
 */
	public function testPluginDispatch() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';

		Router::reload();
		$Dispatcher = new TestDispatcher();
		Router::connect(
			'/my_plugin/:controller/*', 
			array('plugin' => 'my_plugin', 'controller' => 'pages', 'action' => 'display')
		);

		$url = 'my_plugin/some_pages/home/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$result = $Dispatcher->parseParams(new CakeRequest($url));
		$expected = array(
			'pass' => array('home'),
			'named' => array('param'=> 'value', 'param2'=> 'value2'), 'plugin'=> 'my_plugin',
			'controller'=> 'some_pages', 'action'=> 'display', 'form'=> null,
			'url'=> array('url'=> 'my_plugin/some_pages/home/param:value/param2:value2'),
		);
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value, 'Value mismatch ' . $key . ' %');
		}

		$this->assertIdentical($controller->plugin, 'my_plugin');
		$this->assertIdentical($controller->name, 'SomePages');
		$this->assertIdentical($controller->params['controller'], 'some_pages');
		$this->assertIdentical($controller->passedArgs, array('0' => 'home', 'param'=>'value', 'param2'=>'value2'));

		$expected = '/cake/repo/branches/1.2.x.x/my_plugin/some_pages/home/param:value/param2:value2';
		$this->assertIdentical($expected, $controller->here);

		$expected = '/cake/repo/branches/1.2.x.x';
		$this->assertIdentical($expected, $controller->base);
	}

/**
 * testAutomaticPluginDispatch method
 *
 * @return void
 */
	public function testAutomaticPluginDispatch() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';

		Router::reload();
		$Dispatcher =& new TestDispatcher();
		Router::connect(
			'/my_plugin/:controller/:action/*',
			array('plugin' => 'my_plugin', 'controller' => 'pages', 'action' => 'display')
		);

		$Dispatcher->base = false;

		$url = 'my_plugin/other_pages/index/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$this->assertIdentical($controller->plugin, 'my_plugin');
		$this->assertIdentical($controller->name, 'OtherPages');
		$this->assertIdentical($controller->action, 'index');
		$this->assertIdentical($controller->passedArgs, array('param' => 'value', 'param2' => 'value2'));

		$expected = '/cake/repo/branches/1.2.x.x/my_plugin/other_pages/index/param:value/param2:value2';
		$this->assertIdentical($expected, $controller->here);

		$expected = '/cake/repo/branches/1.2.x.x';
		$this->assertIdentical($expected, $controller->base);
	}

/**
 * testAutomaticPluginControllerDispatch method
 *
 * @return void
 */
	public function testAutomaticPluginControllerDispatch() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';

		$plugins = App::objects('plugin');
		$plugins[] = 'MyPlugin';
		$plugins[] = 'ArticlesTest';

		App::setObjects('plugin', $plugins);

		Router::reload();
		$Dispatcher = new TestDispatcher();
		$Dispatcher->base = false;

		$url = 'my_plugin/my_plugin/add/param:value/param2:value2';

		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$this->assertIdentical($controller->plugin, 'my_plugin');
		$this->assertIdentical($controller->name, 'MyPlugin');
		$this->assertIdentical($controller->action, 'add');
		$this->assertEqual($controller->params['named'], array('param' => 'value', 'param2' => 'value2'));


		Router::reload();
		$Dispatcher = new TestDispatcher();
		$Dispatcher->base = false;

		// Simulates the Route for a real plugin, installed in APP/plugins
		Router::connect('/my_plugin/:controller/:action/*', array('plugin' => 'my_plugin'));

		$plugin = 'MyPlugin';
		$pluginUrl = Inflector::underscore($plugin);

		$url = $pluginUrl;
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertIdentical($controller->plugin, 'my_plugin');
		$this->assertIdentical($controller->name, 'MyPlugin');
		$this->assertIdentical($controller->action, 'index');

		$expected = $pluginUrl;
		$this->assertEqual($controller->params['controller'], $expected);


		Configure::write('Routing.prefixes', array('admin'));

		Router::reload();
		$Dispatcher = new TestDispatcher();

		$url = 'admin/my_plugin/my_plugin/add/5/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$this->assertEqual($controller->params['plugin'], 'my_plugin');
		$this->assertEqual($controller->params['controller'], 'my_plugin');
		$this->assertEqual($controller->params['action'], 'admin_add');
		$this->assertEqual($controller->params['pass'], array(5));
		$this->assertEqual($controller->params['named'], array('param' => 'value', 'param2' => 'value2'));
		$this->assertIdentical($controller->plugin, 'my_plugin');
		$this->assertIdentical($controller->name, 'MyPlugin');
		$this->assertIdentical($controller->action, 'admin_add');

		$expected = array(0 => 5, 'param'=>'value', 'param2'=>'value2');
		$this->assertEqual($controller->passedArgs, $expected);

		Configure::write('Routing.prefixes', array('admin'));
		Router::reload();

		$Dispatcher = new TestDispatcher();

		$controller = $Dispatcher->dispatch('admin/articles_test', array('return' => 1));
		$this->assertIdentical($controller->plugin, 'articles_test');
		$this->assertIdentical($controller->name, 'ArticlesTest');
		$this->assertIdentical($controller->action, 'admin_index');

		$expected = array(
			'pass'=> array(),
			'named' => array(),
			'controller' => 'articles_test',
			'plugin' => 'articles_test',
			'action' => 'admin_index',
			'prefix' => 'admin',
			'admin' =>  true,
			'form' => array(), 
			'url' => array('url' => 'admin/articles_test'),
			'return' => 1
		);
		foreach ($expected as $key => $value) {
			$this->assertEqual($controller->params[$key], $expected[$key], 'Value mismatch ' . $key . ' %s');
		}
	}

/**
 * test Plugin dispatching without controller name and using
 * plugin short form instead.
 *
 * @return void
 */
	public function testAutomaticPluginDispatchWithShortAccess() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';
		$plugins = App::objects('plugin');
		$plugins[] = 'MyPlugin';

		App::setObjects('plugin', $plugins);
		Router::reload();

		$Dispatcher =& new TestDispatcher();
		$Dispatcher->base = false;

		$url = 'my_plugin/';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertEqual($controller->params['controller'], 'my_plugin');
		$this->assertEqual($controller->params['plugin'], 'my_plugin');
		$this->assertEqual($controller->params['action'], 'index');
		$this->assertFalse(isset($controller->params['pass'][0]));
	}

/**
 * test plugin shortcut urls with controllers that need to be loaded,
 * the above test uses a controller that has already been included.
 *
 * @return void
 */
	function testPluginShortCutUrlsWithControllerThatNeedsToBeLoaded() {
		$loaded = class_exists('TestPluginController', false);
		if ($this->skipIf($loaded, 'TestPluginController already loaded, this test will always pass, skipping %s')) {
			return true;
		}
		Router::reload();
		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS)
		), true);
		App::objects('plugin', null, false);

		$Dispatcher =& new TestDispatcher();
		$Dispatcher->base = false;

		$url = 'test_plugin/';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertEqual($controller->params['controller'], 'test_plugin');
		$this->assertEqual($controller->params['plugin'], 'test_plugin');
		$this->assertEqual($controller->params['action'], 'index');
		$this->assertFalse(isset($controller->params['pass'][0]));

		$url = '/test_plugin/tests/index';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertEqual($controller->params['controller'], 'tests');
		$this->assertEqual($controller->params['plugin'], 'test_plugin');
		$this->assertEqual($controller->params['action'], 'index');
		$this->assertFalse(isset($controller->params['pass'][0]));

		$url = '/test_plugin/tests/index/some_param';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertEqual($controller->params['controller'], 'tests');
		$this->assertEqual($controller->params['plugin'], 'test_plugin');
		$this->assertEqual($controller->params['action'], 'index');
		$this->assertEqual($controller->params['pass'][0], 'some_param');

		App::build();
	}

/**
 * testAutomaticPluginControllerMissingActionDispatch method
 *
 * @return void
 */
	public function testAutomaticPluginControllerMissingActionDispatch() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';

		Router::reload();
		$Dispatcher =& new TestDispatcher();
		$Dispatcher->base = false;

		$url = 'my_plugin/not_here/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return'=> 1));

		$expected = array('missingAction', array(array(
			'className' => 'MyPluginController',
			'action' => 'not_here',
			'webroot' => '/cake/repo/branches/1.2.x.x/',
			'url' => '/cake/repo/branches/1.2.x.x/my_plugin/not_here/param:value/param2:value2',
			'base' => '/cake/repo/branches/1.2.x.x'
		)));
		$this->assertIdentical($expected, $controller);

		Router::reload();
		$Dispatcher =& new TestDispatcher();
		$Dispatcher->base = false;

		$url = 'my_plugin/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return'=> 1));

		$expected = array('missingAction', array(array(
			'className' => 'MyPluginController',
			'action' => 'param:value',
			'webroot' => '/cake/repo/branches/1.2.x.x/',
			'url' => '/cake/repo/branches/1.2.x.x/my_plugin/param:value/param2:value2',
			'base' => '/cake/repo/branches/1.2.x.x'
		)));
		$this->assertIdentical($expected, $controller);
	}

/**
 * testPrefixProtection method
 *
 * @return void
 */
	public function testPrefixProtection() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';

		Router::reload();
		Router::connect('/admin/:controller/:action/*', array('prefix'=>'admin'), array('controller', 'action'));

		$Dispatcher = new TestDispatcher();

		$url = 'test_dispatch_pages/admin_index/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$expected = array('privateAction', array(array(
			'className' => 'TestDispatchPagesController',
			'action' => 'admin_index',
			'webroot' => '/cake/repo/branches/1.2.x.x/',
			'url' => 'test_dispatch_pages/admin_index/param:value/param2:value2',
			'base' => '/cake/repo/branches/1.2.x.x'
		)));
		$this->assertIdentical($expected, $controller);
	}

/**
 * Test dispatching into the TestPlugin in the test_app
 *
 * @return void
 */
	public function testTestPluginDispatch() {
		$Dispatcher = new TestDispatcher();
		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS)
		));
		App::objects('plugin', null, false);
		Router::reload();
		Router::parse('/');

		$url = '/test_plugin/tests/index';
		$result = $Dispatcher->dispatch($url, array('return' => 1));
		$this->assertTrue(class_exists('TestsController'));
		$this->assertTrue(class_exists('TestPluginAppController'));
		$this->assertTrue(class_exists('OtherComponentComponent'));
		$this->assertTrue(class_exists('PluginsComponentComponent'));

		$this->assertEqual($result->params['controller'], 'tests');
		$this->assertEqual($result->params['plugin'], 'test_plugin');
		$this->assertEqual($result->params['action'], 'index');

		App::build();
	}

/**
 * testChangingParamsFromBeforeFilter method
 *
 * @return void
 */
	public function testChangingParamsFromBeforeFilter() {
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';
		$Dispatcher =& new TestDispatcher();
		$url = 'some_posts/index/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$expected = array('missingAction', array(array(
			'className' => 'SomePostsController',
			'action' => 'view',
			'webroot' => '/cake/repo/branches/1.2.x.x/',
			'url' => '/cake/repo/branches/1.2.x.x/some_posts/index/param:value/param2:value2',
			'base' => '/cake/repo/branches/1.2.x.x'
		)));
		$this->assertEqual($expected, $controller);

		$url = 'some_posts/something_else/param:value/param2:value2';
		$controller = $Dispatcher->dispatch($url, array('return' => 1));

		$expected = 'SomePosts';
		$this->assertEqual($expected, $controller->name);

		$expected = 'change';
		$this->assertEqual($expected, $controller->action);

		$expected = array('changed');
		$this->assertIdentical($expected, $controller->params['pass']);
	}

/**
 * testStaticAssets method
 *
 * @return void
 */
	public function testAssets() {
		Router::reload();
		$Configure = Configure::getInstance();
		$Configure->__objects = null;

		App::build(array(
			'plugins' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS),
			'vendors' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'vendors'. DS),
			'views' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS)
		));

		$Dispatcher = new TestDispatcher();
		$debug = Configure::read('debug');
		//Configure::write('debug', 0);

		ob_start();
		$Dispatcher->dispatch('theme/test_theme/../webroot/css/test_asset.css');
		$result = ob_get_clean();
		$this->assertFalse($result);

		ob_start();
		$Dispatcher->dispatch('theme/test_theme/pdfs');
		$result = ob_get_clean();
		$this->assertFalse($result);

		ob_start();
		$Dispatcher->dispatch('theme/test_theme/flash/theme_test.swf');
		$result = ob_get_clean();

		$file = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'themed' . DS . 'test_theme' . DS . 'webroot' . DS . 'flash' . DS . 'theme_test.swf');
		$this->assertEqual($file, $result);
		$this->assertEqual('this is just a test to load swf file from the theme.', $result);

		ob_start();
		$Dispatcher->dispatch('theme/test_theme/pdfs/theme_test.pdf');
		$result = ob_get_clean();
		$file = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'themed' . DS . 'test_theme' . DS . 'webroot' . DS . 'pdfs' . DS . 'theme_test.pdf');
		$this->assertEqual($file, $result);
		$this->assertEqual('this is just a test to load pdf file from the theme.', $result);

		ob_start();
		$Dispatcher->dispatch('theme/test_theme/img/test.jpg');
		$result = ob_get_clean();
		$file = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS . 'themed' . DS . 'test_theme' . DS . 'webroot' . DS . 'img' . DS . 'test.jpg');
		$this->assertEqual($file, $result);

		ob_start();
		$Dispatcher->asset('theme/test_theme/css/test_asset.css');
		$result = ob_get_clean();
		$this->assertEqual('this is the test asset css file', $result);

		ob_start();
		$Dispatcher->asset('theme/test_theme/js/theme.js');
		$result = ob_get_clean();
		$this->assertEqual('root theme js file', $result);

		ob_start();
		$Dispatcher->asset('theme/test_theme/js/one/theme_one.js');
		$result = ob_get_clean();
		$this->assertEqual('nested theme js file', $result);

		ob_start();
		$Dispatcher->asset('test_plugin/root.js');
		$result = ob_get_clean();
		$expected = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'test_plugin' . DS . 'webroot' . DS . 'root.js');
		$this->assertEqual($result, $expected);

		ob_start();
		$Dispatcher->dispatch('test_plugin/flash/plugin_test.swf');
		$result = ob_get_clean();
		$file = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'test_plugin' . DS . 'webroot' . DS . 'flash' . DS . 'plugin_test.swf');
		$this->assertEqual($file, $result);
		$this->assertEqual('this is just a test to load swf file from the plugin.', $result);

		ob_start();
		$Dispatcher->dispatch('test_plugin/pdfs/plugin_test.pdf');
		$result = ob_get_clean();
		$file = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'test_plugin' . DS . 'webroot' . DS . 'pdfs' . DS . 'plugin_test.pdf');
		$this->assertEqual($file, $result);
		 $this->assertEqual('this is just a test to load pdf file from the plugin.', $result);

		ob_start();
		$Dispatcher->asset('test_plugin/js/test_plugin/test.js');
		$result = ob_get_clean();
		$this->assertEqual('alert("Test App");', $result);

		ob_start();
		$Dispatcher->asset('test_plugin/js/test_plugin/test.js');
		$result = ob_get_clean();
		$this->assertEqual('alert("Test App");', $result);

		ob_start();
		$Dispatcher->asset('test_plugin/css/test_plugin_asset.css');
		$result = ob_get_clean();
		$this->assertEqual('this is the test plugin asset css file', $result);

		ob_start();
		$Dispatcher->asset('test_plugin/img/cake.icon.gif');
		$result = ob_get_clean();
		$file = file_get_contents(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'plugins' . DS . 'test_plugin' .DS . 'webroot' . DS . 'img' . DS . 'cake.icon.gif');
		$this->assertEqual($file, $result);

		ob_start();
		$Dispatcher->asset('plugin_js/js/plugin_js.js');
		$result = ob_get_clean();
		$expected = "alert('win sauce');";
		$this->assertEqual($result, $expected);

		ob_start();
		$Dispatcher->asset('plugin_js/js/one/plugin_one.js');
		$result = ob_get_clean();
		$expected = "alert('plugin one nested js file');";
		$this->assertEqual($result, $expected);
		Configure::write('debug', $debug);
		//reset the header content-type without page can render as plain text.
		header('Content-type: text/html');

		ob_start();
		$Dispatcher->asset('test_plugin/css/unknown.extension');
		$result = ob_get_clean();
		$this->assertEqual('Testing a file with unknown extension to mime mapping.', $result);
		header('Content-type: text/html');

		ob_start();
		$Dispatcher->asset('test_plugin/css/theme_one.htc');
		$result = ob_get_clean();
		$this->assertEqual('htc file', $result);
		header('Content-type: text/html');
	}

/**
 * test that missing asset processors trigger a 404 with no response body.
 *
 * @return void
 */
	function testMissingAssetProcessor404() {
		$Dispatcher =& new TestDispatcher();
		Configure::write('Asset.filter', array(
			'js' => '',
			'css' => null
		));
		$this->assertNoErrors();

		ob_start();
		$Dispatcher->asset('ccss/cake.generic.css');
		$result = ob_get_clean();
		$this->assertTrue($Dispatcher->stopped);

		header('HTTP/1.1 200 Ok');
	}

/**
 * test that asset filters work for theme and plugin assets	
 *
 * @return void
 */
	function testAssetFilterForThemeAndPlugins() {
		$Dispatcher =& new TestDispatcher();
		Configure::write('Asset.filter', array(
			'js' => '',
			'css' => ''
		));
		$Dispatcher->asset('theme/test_theme/ccss/cake.generic.css');
		$this->assertTrue($Dispatcher->stopped);

		$Dispatcher->stopped = false;
		$Dispatcher->asset('theme/test_theme/cjs/debug_kit.js');
		$this->assertTrue($Dispatcher->stopped);

		$Dispatcher->stopped = false;
		$Dispatcher->asset('test_plugin/ccss/cake.generic.css');
		$this->assertTrue($Dispatcher->stopped);

		$Dispatcher->stopped = false;
		$Dispatcher->asset('test_plugin/cjs/debug_kit.js');
		$this->assertTrue($Dispatcher->stopped);

		$Dispatcher->stopped = false;
		$Dispatcher->asset('css/ccss/debug_kit.css');
		$this->assertFalse($Dispatcher->stopped);
		
		$Dispatcher->stopped = false;
		$Dispatcher->asset('js/cjs/debug_kit.js');
		$this->assertFalse($Dispatcher->stopped);
	}
/**
 * testFullPageCachingDispatch method
 *
 * @return void
 */
	public function testFullPageCachingDispatch() {
		Configure::write('Cache.disable', false);
		Configure::write('Cache.check', true);
		Configure::write('debug', 2);

		$_POST = array();
		$_SERVER['PHP_SELF'] = '/';

		Router::reload();
		Router::connect('/', array('controller' => 'test_cached_pages', 'action' => 'index'));

		App::build(array(
			'views' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views' . DS),
		), true);

		$dispatcher = new TestDispatcher();

		$url = '/';

		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);

		$filename = $this->__cachePath($dispatcher->here);
		unlink($filename);

		$url = 'test_cached_pages/index';
		$_POST = array(
			'slasher' => "Up in your's grill \ '"
		);

		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);
		$filename = $this->__cachePath($dispatcher->here);
		unlink($filename);

		$url = 'TestCachedPages/index';

		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);
		$filename = $this->__cachePath($dispatcher->here);
		unlink($filename);

		$url = 'TestCachedPages/test_nocache_tags';

		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);
		$filename = $this->__cachePath($dispatcher->here);
		unlink($filename);

		$url = 'test_cached_pages/view/param/param';

		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);
		$filename = $this->__cachePath($dispatcher->here);
		unlink($filename);

		$url = 'test_cached_pages/view/foo:bar/value:goo';

		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);
		$filename = $this->__cachePath($dispatcher->here);
		$this->assertTrue(file_exists($filename));

		unlink($filename);
	}

/**
 * test that cached() registers a view and un-registers it.  Tests
 * that helpers using ClassRegistry::getObject('view'); don't fail
 *
 * @return void
 */
	function testCachedRegisteringViewObject() {
		Configure::write('Cache.disable', false);
		Configure::write('Cache.check', true);
		Configure::write('debug', 2);

		$_POST = array();
		$_SERVER['PHP_SELF'] = '/';

		Router::reload();
		App::build(array(
			'views' => array(TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS)
		));

		$dispatcher =& new TestDispatcher();
		$dispatcher->base = false;

		$url = 'test_cached_pages/cache_form';
		ob_start();
		$dispatcher->dispatch($url);
		$out = ob_get_clean();

		ClassRegistry::flush();

		ob_start();
		$dispatcher->cached($url);
		$cached = ob_get_clean();

		$result = str_replace(array("\t", "\r\n", "\n"), "", $out);
		$cached = preg_replace('/<!--+[^<>]+-->/', '', $cached);
		$expected =  str_replace(array("\t", "\r\n", "\n"), "", $cached);

		$this->assertEqual($result, $expected);
		$filename = $this->__cachePath($dispatcher->here);
		@unlink($filename);
		ClassRegistry::flush();
	}

/**
 * testHttpMethodOverrides method
 *
 * @return void
 */
	public function testHttpMethodOverrides() {
		Router::reload();
		Router::mapResources('Posts');

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$dispatcher = new Dispatcher();

		$result = $dispatcher->parseParams(new CakeRequest('/posts'));
		$expected = array('pass' => array(), 'named' => array(), 'plugin' => null, 'controller' => 'posts', 'action' => 'add', '[method]' => 'POST', 'form' => array());
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value, 'Value mismatch for ' . $key . ' %s');
		}

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';

		$result = $dispatcher->parseParams(new CakeRequest('/posts/5'));
		$expected = array('pass' => array('5'), 'named' => array(), 'id' => '5', 'plugin' => null, 'controller' => 'posts', 'action' => 'edit', '[method]' => 'PUT', 'form' => array());
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value, 'Value mismatch for ' . $key . ' %s');
		}

		unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$result = $dispatcher->parseParams(new CakeRequest('/posts/5'));
		$expected = array('pass' => array('5'), 'named' => array(), 'id' => '5', 'plugin' => null, 'controller' => 'posts', 'action' => 'view', '[method]' => 'GET', 'form' => array());
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value, 'Value mismatch for ' . $key . ' %s');
		}

		$_POST['_method'] = 'PUT';

		$result = $dispatcher->parseParams(new CakeRequest('/posts/5'));
		$expected = array('pass' => array('5'), 'named' => array(), 'id' => '5', 'plugin' => null, 'controller' => 'posts', 'action' => 'edit', '[method]' => 'PUT', 'form' => array());
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value, 'Value mismatch for ' . $key . ' %s');
		}

		$_POST['_method'] = 'POST';
		$_POST['data'] = array('Post' => array('title' => 'New Post'));
		$_POST['extra'] = 'data';
		$_SERVER = array();

		$result = $dispatcher->parseParams(new CakeRequest('/posts'));
		$expected = array(
			'pass' => array(), 'named' => array(), 'plugin' => null, 'controller' => 'posts', 'action' => 'add',
			'[method]' => 'POST', 'form' => array('extra' => 'data'), 'data' => array('Post' => array('title' => 'New Post')),
		);
		foreach ($expected as $key => $value) {
			$this->assertEqual($result[$key], $value, 'Value mismatch for ' . $key . ' %s');
		}

		unset($_POST['_method']);
	}

/**
 * Tests that the Dispatcher does not return an empty action
 *
 * @return void
 */
	public function testTrailingSlash() {
		$_POST = array();
		$_SERVER['PHP_SELF'] = '/cake/repo/branches/1.2.x.x/index.php';

		Router::reload();
		$Dispatcher = new TestDispatcher();
		Router::connect('/myalias/:action/*', array('controller' => 'my_controller', 'action' => null));

		$Dispatcher->base = false;
		$url = 'myalias/'; //Fails
		$result = $Dispatcher->parseParams(new CakeRequest($url));
		$this->assertEqual('index', $result['action']);

		$url = 'myalias'; //Passes
		$result = $Dispatcher->parseParams(new CakeRequest($url));
		$this->assertEqual('index', $result['action']);
	}

/**
 * backupEnvironment method
 *
 * @return void
 * @access private
 */
	function __backupEnvironment() {
		return array(
			'App'	=> Configure::read('App'),
			'GET'	=> $_GET,
			'POST'	=> $_POST,
			'SERVER'=> $_SERVER
		);
	}

/**
 * reloadEnvironment method
 *
 * @return void
 * @access private
 */
	function __reloadEnvironment() {
		foreach ($_GET as $key => $val) {
			unset($_GET[$key]);
		}
		foreach ($_POST as $key => $val) {
			unset($_POST[$key]);
		}
		foreach ($_SERVER as $key => $val) {
			unset($_SERVER[$key]);
		}
		Configure::write('App', array());
	}

/**
 * loadEnvironment method
 *
 * @param mixed $env
 * @return void
 * @access private
 */
	function __loadEnvironment($env) {
		if ($env['reload']) {
			$this->__reloadEnvironment();
		}

		if (isset($env['App'])) {
			Configure::write('App', $env['App']);
		}

		if (isset($env['GET'])) {
			foreach ($env['GET'] as $key => $val) {
				$_GET[$key] = $val;
			}
		}

		if (isset($env['POST'])) {
			foreach ($env['POST'] as $key => $val) {
				$_POST[$key] = $val;
			}
		}

		if (isset($env['SERVER'])) {
			foreach ($env['SERVER'] as $key => $val) {
				$_SERVER[$key] = $val;
			}
		}
	}

/**
 * cachePath method
 *
 * @param mixed $her
 * @return string
 * @access private
 */
	function __cachePath($here) {
		$path = $here;
		if ($here == '/') {
			$path = 'home';
		}
		$path = strtolower(Inflector::slug($path));

		$filename = CACHE . 'views' . DS . $path . '.php';

		if (!file_exists($filename)) {
			$filename = CACHE . 'views' . DS . $path . '_index.php';
		}
		return $filename;
	}
}
