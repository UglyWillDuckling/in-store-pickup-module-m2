<?php

namespace GaussDev\InStore\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_locationCollection;


    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        //first get the class name for location class
        $className ='GaussDev\InStore\Model\ResourceModel\Location\Collection';


        //then store the location collection object
        $this->_locationCollection = $objectManager->create($className);
    }

    public function geLocations()
    {
        //return all the stores
        return $this->_locationCollection->load();
    }
}