<?php

namespace GaussDev\InStore\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use GaussDev\InStore\Model\LocationFactory;

abstract class Location extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var LocationFactory
     */
    private $locationFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LocationFactory $locationFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->locationFactory = $locationFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('GaussDev_InStore::location');
    }

    protected function createLocationModel()
    {
        return $this->locationFactory->create();
    }
}