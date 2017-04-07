<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 26.01.17.
 * Time: 19:26
 */
namespace GaussDev\InStore\Plugin\Model;

class ShippingInformationManagementPlugin
{
    protected $session;

    public function __construct(\Magento\Checkout\Model\Session $session)
    {
        $this->session = $session;
    }

    public function beforeSaveAddressInformation(\GaussDev\Fixes\Model\ShippingInformationManagement $subject, $cartId,
                                                 \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation)
    {
        $this->session->setData("shippingMethod", $addressInformation->getShippingMethodCode());
    }
}