<?php

    namespace GaussDev\InStore\Model\ResourceModel;



    class Location extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
    {
        public function _construct()
        {
            $this->_init("gaussdev_instore_location", "entity_id");
        }
    }