<?php
namespace GaussDev\InStore\Model;

use \GaussDev\InStore\Api\MyStoreInterface;


class MyStore implements MyStoreInterface
{
    protected $session;

    public function __construct(
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function saveStoreId($id) {
        $this->session->setData("storeId", $id);
    }
}