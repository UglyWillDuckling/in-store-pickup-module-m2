<?php
    namespace GaussDev\InStore\Model\Quote;

    //TODO use plugin instead of preference


    use Magento\Quote\Model\QuoteAddressValidator;
    use Psr\Log\LoggerInterface as Logger;

    class ShippingAddressManagement extends \Magento\Quote\Model\ShippingAddressManagement
    {
        private $minimumAmountErrorMessage;
        public $session;


        public function __construct(
            \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
            QuoteAddressValidator $addressValidator,
            Logger $logger,
            \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
            \Magento\Checkout\Model\Session $session
        )
        {
            parent::__construct($quoteRepository, $addressValidator, $logger, $addressRepository, $scopeConfig, $totalsCollector);

            $this->session = $session;
        }


        /**
         * {@inheritDoc}
         * @SuppressWarnings(PHPMD.NPathComplexity)
         */
        public function assign($cartId, \Magento\Quote\Api\Data\AddressInterface $address)
        {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            if ($quote->isVirtual()) {
                throw new NoSuchEntityException(
                    __('Cart contains virtual product(s) only. Shipping address is not applicable.')
                );
            }

            $saveInAddressBook = $address->getSaveInAddressBook() ? 1 : 0;
            $sameAsBilling = $address->getSameAsBilling() ? 1 : 0;
            $customerAddressId = $address->getCustomerAddressId();
            $this->addressValidator->validate($address);
            $quote->setShippingAddress($address);
            $address = $quote->getShippingAddress();

            if ($customerAddressId === null) {
                $address->setCustomerAddressId(null);
            }

            if ($customerAddressId && $this->session->getData("shippingMethod") != "instore")
            {
                $addressData = $this->addressRepository->getById($customerAddressId);
                $address = $quote->getShippingAddress()->importCustomerAddressData($addressData);
            } elseif ($quote->getCustomerId()) {
                $address->setEmail($quote->getCustomerEmail());
            }
            $address->setSameAsBilling($sameAsBilling);
            $address->setSaveInAddressBook($saveInAddressBook);
            $address->setCollectShippingRates(true);

            if (!$quote->validateMinimumAmount($quote->getIsMultiShipping())) {
                throw new InputException($this->getMinimumAmountErrorMessage()->getMessage());
            }

            try {
                $address->save();
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new InputException(__('Unable to save address. Please check input data.'));
            }
            return $quote->getShippingAddress()->getId();
        }


        /**
         * @return \Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage
         * @deprecated
         */
        private function getMinimumAmountErrorMessage()
        {
            if ($this->minimumAmountErrorMessage === null) {
                $objectManager = ObjectManager::getInstance();
                $this->minimumAmountErrorMessage = $objectManager->get(
                    \Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage::class
                );
            }
            return $this->minimumAmountErrorMessage;
        }
    }