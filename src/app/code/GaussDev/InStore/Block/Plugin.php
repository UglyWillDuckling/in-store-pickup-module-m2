<?php


namespace GaussDev\InStore\Block;

use GaussDev\BBM\Model\ResourceModel\Availability\CollectionFactory;
use Magento\Framework\Webapi\Exception;
use GaussDev\BBM\Model\ResourceModel\Location\Collection;


class Plugin
{
    protected $session, $availabilityCollectionFactory;


    public function __construct(
        \Magento\Checkout\Model\Session $session,
        CollectionFactory $availabilityCollectionFactory,
        Collection $locationCollection
    )
    {
        $this->session = $session;
        $this->collectionFactory = $availabilityCollectionFactory;
        $this->locationCollection = $locationCollection;
    }


    public function aftergetCheckoutConfig($subject, $result)
    {
        $id = $this->session->getData('storeId', false);//get the selected store id from session


        $availableStores = $this->locationCollection->load();


        $stores = [];
        foreach ($availableStores as $store){


            $stores[] = [
                'id' => $store->getBbm_stock_id(),
                'street' => $store->getStreet(),
                'country_id' => "HR",
                'postcode' => $store->getPostcode(),
                'city' => $store->getCity(),
                //Missing city and postcode in bbm storage
            ];
        }

        $result['stores'] =$stores;
        $result['storeId'] = $id ?: $stores[0]['id'];

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
}