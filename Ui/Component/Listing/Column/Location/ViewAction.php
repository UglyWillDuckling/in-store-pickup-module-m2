<?php

namespace GaussDev\InStore\Ui\Component\Listing\Column\Location;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ViewAction extends Column
{
    /** Url path */
    const LOCATION_URL_PATH_EDIT = 'gaussdev_instore/location/edit';
    const LOCATION_URL_PATH_DELETE = 'gaussdev_instore/location/delete';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;
    private $deleteUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::LOCATION_URL_PATH_EDIT,
        $deleteUrl = self::LOCATION_URL_PATH_DELETE
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->deleteUrl = $deleteUrl;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['location_id'])) {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlBuilder->getUrl($this->editUrl, ['id' => $item['location_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href'  => $this->urlBuilder->getUrl($this->deleteUrl, ['id' => $item['location_id']]),
                        'label' => __('Delete')
                    ];
                }
            }
        }

        return $dataSource;
    }
}