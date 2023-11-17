<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Persistence;

use Orm\Zed\Url\Persistence\SpyUrlQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use ValanticSpryker\Zed\CategorySitemapConnector\Persistence\Mapper\CategorySitemapConnectorMapper;
use ValanticSpryker\Zed\CategorySitemapConnector\Persistence\Mapper\CategorySitemapConnectorMapperInterface;

/**
 * @method \ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig getConfig()
 */
class CategorySitemapConnectorPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Url\Persistence\SpyUrlQuery
     */
    public function createSpyUrlQuery(): SpyUrlQuery
    {
        return SpyUrlQuery::create();
    }

    /**
     * @return \ValanticSpryker\Zed\CategorySitemapConnector\Persistence\Mapper\CategorySitemapConnectorMapperInterface
     */
    public function createCategoryPageSitemapMapper(): CategorySitemapConnectorMapperInterface
    {
        return new CategorySitemapConnectorMapper(
            $this->getConfig(),
        );
    }
}
