<?php

namespace GaussDev\InStore\Block\Adminhtml\Location\Edit;

use GaussDev\InStore\Model\Location;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('location_form');
        $this->setTitle(__('Location Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var Location $model */
        $model = $this->_coreRegistry->registry('location');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('location_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        $this->_addElementTypes($fieldset);

        if ($model->getLocationId()) {
            $fieldset->addField('location_id', 'hidden', ['name' => 'location_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'street',
            'text',
            [
                'name'     => 'street',
                'label'    => __('Street'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name'     => 'city',
                'label'    => __('City'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name'     => 'postcode',
                'label'    => __('Postcode'),
                'required' => true
            ]
        );


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();

        return $this;
    }
}