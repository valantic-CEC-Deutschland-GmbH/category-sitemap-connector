<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Business\Model\Creator;

use Spryker\Zed\Store\Business\StoreFacadeInterface;
use ValanticSpryker\Service\Sitemap\SitemapServiceInterface;
use ValanticSpryker\Shared\CategorySitemapConnector\CategorySitemapConnectorConstants;
use ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig;
use ValanticSpryker\Zed\CategorySitemapConnector\Persistence\CategorySitemapConnectorRepositoryInterface;

class CategorySitemapCreator implements CategorySitemapCreatorInterface
{
    /**
     * @var \ValanticSpryker\Service\Sitemap\SitemapServiceInterface
     */
    private SitemapServiceInterface $sitemapService;

    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    private StoreFacadeInterface $storeFacade;

    /**
     * @var \ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig
     */
    private CategorySitemapConnectorConfig $config;

    /**
     * @var \ValanticSpryker\Zed\CategorySitemapConnector\Persistence\CategorySitemapConnectorRepositoryInterface
     */
    private CategorySitemapConnectorRepositoryInterface $repository;

    /**
     * @param \ValanticSpryker\Service\Sitemap\SitemapServiceInterface $sitemapService
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     * @param \ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig $config
     * @param \ValanticSpryker\Zed\CategorySitemapConnector\Persistence\CategorySitemapConnectorRepositoryInterface $repository
     */
    public function __construct(
        SitemapServiceInterface $sitemapService,
        StoreFacadeInterface $storeFacade,
        CategorySitemapConnectorConfig $config,
        CategorySitemapConnectorRepositoryInterface $repository
    ) {
        $this->sitemapService = $sitemapService;
        $this->storeFacade = $storeFacade;
        $this->config = $config;
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function createCategorySitemapXml(): array
    {
        $pageLimit = $this->config->getSitemapUrlLimit();
        $sitemapList = [];
        $currentStoreTransfer = $this->storeFacade->getCurrentStore();
        $page = 1;

        do {
            $urlList = $this->repository->findActiveCategories(
                $currentStoreTransfer,
                $page,
                $pageLimit,
            );

            $sitemapTransfer = $this->sitemapService->createSitemapXmlFileTransfer(
                $urlList,
                $page,
                $currentStoreTransfer->getName(),
                CategorySitemapConnectorConstants::RESOURCE_TYPE,
            );

            if ($sitemapTransfer !== null) {
                $sitemapList[] = $sitemapTransfer;
            }

            $page++;
        } while ($sitemapTransfer !== null);

        return $sitemapList;
    }
}
