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
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Category form input image element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Image extends Varien_Data_Form_Element_Abstract
{

    /**
     * Enter description here...
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        $this->setType('file');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . $url;
            }

            $html = '<a href="'.$url.'" onclick="imagePreview(\''.$this->getHtmlId().'_image\'); return false;"><img src="'.$url.'" id="'.$this->getHtmlId().'_image" title="'.$this->getValue().'" alt="'.$this->getValue().'" height="22" width="22" class="small-image-preview v-middle" /></a> ';
        }
        $this->setClass('input-file');
        $html.= parent::getElementHtml();
        $html.= $this->_getDeleteCheckbox();

        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('Mage_Core_Helper_Data')->__('Delete Image');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> '.$label.'</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }

}
