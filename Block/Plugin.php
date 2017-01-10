<?php


namespace GaussDev\InStore\Block;

use Magento\Framework\Webapi\Exception;

use Magento\Framework\Locale\Resolver;
use Magento\Checkout\Model\Session as checkoutSession;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use GaussDev\Locations\Model\ResourceModel\Location\Collection;

class Plugin
{
    public $session, $locationCollection, $storeManager, $storeInfo, $locale;


    public function __construct(
        checkoutSession $session,
        StoreManagerInterface $storeManage,
        Information $storeInfo,
        Resolver $resolver,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $locationCollectionName //get this from di.xml
    )
    {
        $this->session = $session;
        $this->storeManager = $storeManage;
        $this->storeInfo = $storeInfo;
        $this->locale = $resolver->getLocale();

        $this->locationCollection = $objectManager->create($locationCollectionName);//($locationCollectionName);
    }


    public function aftergetCheckoutConfig($subject, $result)
    {
        $id = $this->session->getData('storeId', false);//get the previously selected store id from session
        $availableStores = $this->locationCollection->load();



        $stores = [];
        foreach ($availableStores as $store){
            $stores[] = [
                'id' => $store->getId(),
                'city' =>       $store->getCity(),
                'name' =>       $store->getName(),
                'street' =>     $store->getStreet(),
                'postcode' =>   $store->getPostcode(),
                'country_id' => $this->getLocale()
            ];
        }

        $result['stores'] =       $stores;
        $result['storeId'] =      $id ?: $stores[0]['id'];
        $result['saveStoreUrl'] = $this->storeManager->getStore()->getBaseUrl() . "rest/V1/saveStoreId/id/";

        return $result;
    }

    protected function getLocale()
    {
        return substr($this->locale, strpos($this->locale, "_") + 1);
    }
}