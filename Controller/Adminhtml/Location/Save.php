<?php

namespace GaussDev\InStore\Controller\Adminhtml\Location;

use GaussDev\InStore\Controller\Adminhtml\Location;
use Magento\Framework\Exception\LocalizedException;

class Save extends Location
{
    /**
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();

        if ($originalRequestData) {
            try {

                $locationId = $this->getRequest()->getParam('location_id');
                $name       = $this->getRequest()->getParam('name');
                $street     = $this->getRequest()->getParam('street');
                $postcode   = $this->getRequest()->getParam('postcode');
                $city       = $this->getRequest()->getParam('city');


                $locationModel = $this->createLocationModel();

                if ($locationId) {
                    $locationModel->load($locationId);
                    if ($locationId != $locationModel->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong location is specified.'));
                    }
                }

                $locationModel->setName($name);

                $locationModel->setStreet($street);
                $locationModel->setData("postcode", $postcode);
                $locationModel->setCity($city);
                $locationModel->save();

                $this->messageManager->addSuccess(__('You saved the Location.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->_addSessionErrorMessages($exception->getMessage());
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException($exception, __('Something went wrong while saving the Location.'));
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($locationId) {
                $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $locationId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    '*/*/edit',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }
}
