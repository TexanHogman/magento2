<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Scans source code for references to classes and see if they indeed exist
 */
namespace Magento\Test\Legacy;

class ClassesTest extends \PHPUnit_Framework_TestCase
{
    public function testPhpCode()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             */
            function ($file) {
                $classes = \Magento\Framework\Test\Utility\Classes::collectPhpCodeClasses(file_get_contents($file));
                $this->_assertNonFactoryName($classes, $file);
            },
            \Magento\Framework\Test\Utility\Files::init()->getPhpFiles()
        );
    }

    public function testConfiguration()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $path
             */
            function ($path) {
                $xml = simplexml_load_file($path);

                $classes = \Magento\Framework\Test\Utility\Classes::collectClassesInConfig($xml);
                $this->_assertNonFactoryName($classes, $path);

                $modules = \Magento\Framework\Test\Utility\Classes::getXmlAttributeValues($xml, '//@module', 'module');
                $this->_assertNonFactoryName(array_unique($modules), $path, false, true);
            },
            \Magento\Framework\Test\Utility\Files::init()->getConfigFiles()
        );
    }

    public function testLayouts()
    {
        $invoker = new \Magento\Framework\Test\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $path
             */
            function ($path) {
                $xml = simplexml_load_file($path);
                $classes = \Magento\Framework\Test\Utility\Classes::collectLayoutClasses($xml);
                foreach (\Magento\Framework\Test\Utility\Classes::getXmlAttributeValues(
                    $xml,
                    '/layout//@helper',
                    'helper'
                ) as $class) {
                    $classes[] = \Magento\Framework\Test\Utility\Classes::getCallbackClass($class);
                }
                $classes = array_merge(
                    $classes,
                    \Magento\Framework\Test\Utility\Classes::getXmlAttributeValues($xml, '/layout//@module', 'module')
                );
                $this->_assertNonFactoryName(array_unique($classes), $path);

                $tabs = \Magento\Framework\Test\Utility\Classes::getXmlNodeValues(
                    $xml,
                    '/layout//action[@method="addTab"]/block'
                );
                $this->_assertNonFactoryName(array_unique($tabs), $path, true);
            },
            \Magento\Framework\Test\Utility\Files::init()->getLayoutFiles()
        );
    }

    /**
     * Check whether specified classes or module names correspond to a file according PSR-1 Standard.
     *
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $names
     * @param bool $softComparison
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertNonFactoryName($names, $file, $softComparison = false, $moduleBlock = false)
    {
        if (!$names) {
            return;
        }
        $factoryNames = [];
        foreach ($names as $name) {
            try {
                if ($softComparison) {
                    $this->assertNotRegExp('/\//', $name);
                } elseif ($moduleBlock) {
                    $this->assertFalse(false === strpos($name, '_'));
                    $this->assertRegExp('/^([A-Z][A-Za-z\d_]+)+$/', $name);
                } else {
                    if (strpos($name, 'Magento') === false) {
                        continue;
                    }
                    $this->assertFalse(false === strpos($name, '\\'));
                    $this->assertRegExp('/^([A-Z\\\\][A-Za-z\d\\\\]+)+$/', $name);
                }
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                $factoryNames[] = $name;
            }
        }
        if ($factoryNames) {
            $this->fail("Obsolete factory name(s) detected in {$file}:" . "\n" . implode("\n", $factoryNames));
        }
    }
}
