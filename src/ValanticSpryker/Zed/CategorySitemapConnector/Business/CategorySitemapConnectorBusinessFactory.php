<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use ValanticSpryker\Service\Sitemap\SitemapServiceInterface;
use ValanticSpryker\Zed\CategorySitemapConnector\Business\Model\Creator\CategorySitemapCreator;
use ValanticSpryker\Zed\CategorySitemapConnector\Business\Model\Creator\CategorySitemapCreatorInterface;
use ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorDependencyProvider;

/**
 * @method \ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig getConfig()
 * @method \ValanticSpryker\Zed\CategorySitemapConnector\Persistence\CategorySitemapConnectorRepositoryInterface getRepository()
 */
class CategorySitemapConnectorBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \ValanticSpryker\Zed\CategorySitemapConnector\Business\Model\Creator\CategorySitemapCreatorInterface
     */
    public function createCategorySitemapCreator(): CategorySitemapCreatorInterface
    {
        return new CategorySitemapCreator(
            $this->getSitemapService(),
            $this->getStoreFacade(),
            $this->getConfig(),
            $this->getRepository(),
        );
    }

    /**
     * @return \ValanticSpryker\Service\Sitemap\SitemapServiceInterface
     */
    private function getSitemapService(): SitemapServiceInterface
    {
        return $this->getProvidedDependency(CategorySitemapConnectorDependencyProvider::SERVICE_SITEMAP);
    }

    /**
     * @return \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    private function getStoreFacade(): StoreFacadeInterface
    {
        return $this->getProvidedDependency(CategorySitemapConnectorDependencyProvider::FACADE_STORE);
    }
}
