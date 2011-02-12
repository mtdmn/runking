<?php
/**
 * TestRunner for CakePHP Test suite.
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
 * @package       cake.tests.libs
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
define('CORE_TEST_CASES', TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'cases');
define('APP_TEST_CASES', TESTS . 'cases');

require 'PHPUnit/TextUI/Command.php';

require_once CAKE_TESTS_LIB . 'cake_test_runner.php';
require_once CAKE_TESTS_LIB . 'cake_test_loader.php';
require_once CAKE_TESTS_LIB . 'cake_test_suite.php';
require_once CAKE_TESTS_LIB . 'cake_test_case.php';
require_once CAKE_TESTS_LIB . 'controller_test_case.php';


PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'DEFAULT');

/**
 * Class to customize loading of test suites from CLI
 *
 * @package       cake.tests.lib
 */
class CakeTestSuiteCommand extends PHPUnit_TextUI_Command {

/**
 * Construct method
 *
 * @param array $params list of options to be used for this run
 */
	public function __construct($loader, $params = array()) {
		$this->arguments['loader'] = $loader;
		$this->arguments['test'] = $params['case'];
		$this->arguments['testFile'] = $params;
		$this->_params = $params;
	}

	/**
     * @param array   $argv
     * @param boolean $exit
     */
    public function run(array $argv, $exit = TRUE)
    {
        $this->handleArguments($argv);

        $runner = new CakeTestRunner($this->arguments['loader']);

        if (is_object($this->arguments['test']) &&
            $this->arguments['test'] instanceof PHPUnit_Framework_Test) {
            $suite = $this->arguments['test'];
        } else {
            $suite = $runner->getTest(
              $this->arguments['test'],
              $this->arguments['testFile'],
              $this->arguments['syntaxCheck']
            );
        }

        if (count($suite) == 0) {
            $skeleton = new PHPUnit_Util_Skeleton_Test(
              $suite->getName(),
              $this->arguments['testFile']
            );

            $result = $skeleton->generate(TRUE);

            if (!$result['incomplete']) {
                eval(str_replace(array('<?php', '?>'), '', $result['code']));
                $suite = new PHPUnit_Framework_TestSuite(
                  $this->arguments['test'] . 'Test'
                );
            }
        }

        if ($this->arguments['listGroups']) {
            PHPUnit_TextUI_TestRunner::printVersionString();

            print "Available test group(s):\n";

            $groups = $suite->getGroups();
            sort($groups);

            foreach ($groups as $group) {
                print " - $group\n";
            }

            exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
        }

        unset($this->arguments['test']);
        unset($this->arguments['testFile']);

        try {
            $result = $runner->doRun($suite, $this->arguments);
        }

        catch (PHPUnit_Framework_Exception $e) {
            print $e->getMessage() . "\n";
        }

        if ($exit) {
            if (isset($result) && $result->wasSuccessful()) {
                exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
            }

            else if (!isset($result) || $result->errorCount() > 0) {
                exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
            }

            else {
                exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
            }
        }
    }

/**
 * Sets the proper test suite to use and loads the test file in it.
 * this method gets called as a callback from the parent class
 *
 * @return void
 */
	protected function handleCustomTestSuite() {
		
	}
}