<?php

/**
 * Fastly CDN for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Fastly CDN for Magento End User License Agreement
 * that is bundled with this package in the file LICENSE_FASTLY_CDN.txt.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fastly CDN to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fastly
 * @package     Fastly_Cdn
 * @copyright   Copyright (c) 2016 Fastly, Inc. (http://www.fastly.com)
 * @license     BSD, see LICENSE_FASTLY_CDN.txt
 */
namespace Fastly\Cdn\Block\System\Config\Form\Field;

use \Fastly\Cdn\Model\Config;

/**
 * Backend system config array field renderer
 */
class EnableAuth extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    protected $_api;

    protected $config;

    /**
     * Currently necessary for work around a missing feature in the M2 core.
     *
     * @var string
     */
    protected $_template = 'Fastly_Cdn::system/config/form/field/enableAuth.phtml';


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Fastly\Cdn\Model\Api $api,
        Config $config,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_api = $api;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('backend_name', ['label' => __('User')]);
        $this->_addAfter = false;
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'store_id' && isset($this->_columns[$columnName])) {
            $options = $this->getOptions(__('-- Select Store --'));
            $element = $this->_elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * Get list of store views.
     *
     * @param bool|false $label
     *
     * @return array
     */
    protected function getOptions($label = false)
    {
        $options = [];
        foreach ($this->_storeManager->getStores() as $store)
        {
            $options[] = ['value' => $store->getId(), 'label' => $store->getName()];
        }
        if ($label) {
            array_unshift($options, ['value' => '', 'label' => $label]);
        }
        return $options;
    }

    /**
     * Check if Basic Authentication is enabled
     * @return int
     */
    public function isAuthEnabled()
    {
        return $this->config->getBasicAuthenticationStatus();
    }
}
