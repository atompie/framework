<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Session;

@session_start();

/**
 * Session test case.
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $_SESSION = array();
        Session::destroyInstance();
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SESSION = array();
        Session::destroyInstance();
        parent::tearDown();
    }

    public function testSession_MustBeSingleton()
    {
        $oSession1 = Session::getInstance();
        $oStdClass = new \stdClass();
        $oStdClass->a = 1;
        $oSession1->set('var1', $oStdClass);
        $oSession2 = Session::getInstance();
        $oSession2->set('var2', 'y');

        $this->assertTrue($oSession2->count() == 2);

        $this->assertTrue($oSession1->has('var2'));
        $this->assertTrue($oSession1->get('var2') == 'y');

        $this->assertTrue(isset($_SESSION['var2']));
        $this->assertTrue(isset($_SESSION['var1']));

        $oSession1->remove('var1');
        $this->assertTrue(!$oSession2->has('var1'));
        $this->assertTrue(!isset($_SESSION['var1']));


    }

    public function testSession_Accessing()
    {

        $oSession1 = Session::getInstance();
        $oStdClass = new \stdClass();
        $oStdClass->a = 1;
        $oSession1->set('var1', $oStdClass);
        $this->assertTrue($oSession1->get('var1')->a == 1);
        $this->assertTrue($_SESSION['var1']->a == 1);

        $_SESSION = array();

        $oSession1 = Session::getInstance();
        $oSession1->set('var1', array('a' => 1));
        $aValue = $oSession1->get('var1');
        $this->assertTrue($aValue['a'] == 1);
        $this->assertTrue($_SESSION['var1']['a'] == 1);

    }

    public function testSession_InstanceStart()
    {
        $oSession1 = Session::getInstance();
        \PHPUnit_Framework_Assert::assertAttributeEquals(false, 'bIsSessionStarted', $oSession1);
        $oSession1->count();
        \PHPUnit_Framework_Assert::assertAttributeEquals(true, 'bIsSessionStarted', $oSession1);

        $aValues = $oSession1->getAll();
        $this->assertTrue(empty($aValues));
        $oSession1->mergeKeyValue('a', 1);
        $this->assertTrue($oSession1->count() == 1);
        Session::destroyInstance();
        $oSession1 = Session::getInstance();
        \PHPUnit_Framework_Assert::assertAttributeEquals(false, 'bIsSessionStarted', $oSession1);
        $oSession1->count();
        \PHPUnit_Framework_Assert::assertAttributeEquals(true, 'bIsSessionStarted', $oSession1);
    }

}