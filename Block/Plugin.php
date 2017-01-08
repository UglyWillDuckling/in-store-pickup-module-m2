<?php


namespace GaussDev\InStore\Block;

use Magento\Framework\Webapi\Exception;

use GaussDev\InStore\Model\ResourceModel\Location\Collection;
use Magento\Framework\Locale\Resolver;


class Plugin
{
    protected $session, $availabilityCollectionFactory;


    public function __construct(
        \Magento\Checkout\Model\Session $session,
        Collection $locationCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManage,
        \Magento\Store\Model\Information $storeInfo,
        Resolver $resolver
    )
    {
        $this->session = $session;
        $this->locationCollection = $locationCollection;
        $this->storeManager = $storeManage;
        $this->storeInfo = $storeInfo;
        $this->locale = $resolver->getLocale();
    }


    public function aftergetCheckoutConfig($subject, $result)
    {
        $id = $this->session->getData('storeId', false);//get the previously selected store id from session
        $availableStores = $this->locationCollection->load();



        $stores = [];
        foreach ($availableStores as $store){
            $stores[] = [
                'city' =>       $store->getCity(),
                'name' =>       $store->getName(),
                'street' =>     $store->getStreet(),
                'postcode' =>   $store->getPostcode(),
                'country_id' => $this->getLocale()
            ];
        }

        $this->storeManager;

        $result['stores'] =       $stores;
        $result['storeId'] =      $id ?: $stores[0]['id'];
        $result['saveStoreUrl'] = $this->storeManager->getStore()->getBaseUrl() . "rest/V1/saveStoreId/id/";

        return $result;
    }

    public function beforesaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $id = $addressInformation->getData('shipping_address')->getData('id');

        $this->session->setData('storeId', $id);
    }


    protected function getLocale()
    {
        return substr($this->locale, strpos($this->locale, "_") + 1);
    }
}