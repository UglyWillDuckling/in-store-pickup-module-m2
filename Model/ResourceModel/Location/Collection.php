<?php

    namespace GaussDev\InStore\Model\ResourceModel\Location;


    class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    {

        public function _construct()
        {
            $this->_init('GaussDev\InStore\Model\Location', 'GaussDev\InStore\Model\ResourceModel\Location');
        }
    }