<?php
/**
 * Index Controller Test
 *
 * @package SellBooksDirect_UnitTest
 * @subpackage Controller
 * @version $Id: 6e0768699e8b1cfb93c464abb35eabd6cbea3e39 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Unit testing bootstrap
 */
require_once '../../bootstrap.php';

/**
 * Index Controller Test
 *
 * @package SellBooksDirect_UnitTest
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    /**
     * Setup application as per our config
     *
     * @see Zend_Test_PHPUnit_ControllerTestCase::setUp()
     */
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    /**
     * Call to root should pull index action
     */
    public function testCallToRootShouldPullIndex()
    {
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
    }

}
