<?php

namespace GaussDev\InStore\Block\Adminhtml\Location;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);

        $this->addButton(
            'delete',
            [
                'label' => __('Delete'),
                'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                    . ','
                    . json_encode($this->getDeleteUrl()
                    )
                    . ')',
                'class' => 'scalable delete',
                'level' => -1
            ]
        );
    }

    /**
     * Retrieve text for header element depending on loaded location
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('location')->getId()) {
            return __("Edit Location '%1'", $this->escapeHtml($this->_coreRegistry->registry('location')->getName()));
        } else {
            return __('New Location');
        }
    }

    /**
     * Initialize location edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'location_id';
        $this->_blockGroup = 'GaussDev_InStore';
        $this->_controller = 'adminhtml_location';

        parent::_construct();
    }

    public function getDeleteUrl(array $args = [])
    {
        return $this->getUrl('gaussdev_instore/location/delete', [
            'id' => $this->getRequest()->getParam('id')
        ]);
    }
}