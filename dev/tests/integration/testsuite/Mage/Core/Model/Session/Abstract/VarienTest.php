<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test class for Mage_Core_Model_Session_Abstract_Varien
 *
 */
class Mage_Core_Model_Session_Abstract_VarienTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider sessionSaveMethodDataProvider
     */
    public function testSessionSaveMethod($saveMethod, $iniValue)
    {
        $this->markTestIncomplete('Bug MAGE-5487');

        // depending on configuration some values cannot be set as default save session handlers.
        $origSessionHandler = ini_set('session.save_handler', $iniValue);
        if ($iniValue && (ini_get($iniValue) != $iniValue)) {
            $this->markTestSkipped("Can't  set '$iniValue' as session save handler");
        }
        ini_set('session.save_handler', $origSessionHandler);

        Mage::getConfig()->setNode(Mage_Core_Model_Session_Abstract::XML_NODE_SESSION_SAVE, $saveMethod);
        /**
         * @var Mage_Core_Model_Session_Abstract_Varien
         */
        $model = new Mage_Core_Model_Session_Abstract();
        $model->start();
        if ($iniValue) {
            $this->assertEquals(ini_get('session.save_handler'), $iniValue);
        }
    }

    public function sessionSaveMethodDataProvider()
    {
        $testCases = array(
            array('db', 'user'),
            array('memcache', 'memcache'),
            array('memcached', 'memcached'),
            array('eaccelerator', 'eaccelerator'),
            array('', ''),
            array('dummy', ''),
        );

        return $testCases;
    }
}
