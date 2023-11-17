<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Persistence\Mapper;

use Generated\Shared\Transfer\SitemapUrlTransfer;
use Orm\Zed\Url\Persistence\SpyUrl;
use Propel\Runtime\Collection\ObjectCollection;
use ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig;

class CategorySitemapConnectorMapper implements CategorySitemapConnectorMapperInterface
{
    /**
     * @var string
     */
    private const URL_FORMAT = '%s%s/';

    /**
     * @var \ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig
     */
    private CategorySitemapConnectorConfig $config;

    /**
     * @param \ValanticSpryker\Zed\CategorySitemapConnector\CategorySitemapConnectorConfig $config
     */
    public function __construct(CategorySitemapConnectorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function mapUrlEntitiesToSitemapUrlTransfers(ObjectCollection $urlEntities): array
    {
        $transfers = [];

        foreach ($urlEntities as $urlEntity) {
            $transfers[] = $this->createSitemapUrlTransfer($urlEntity);
        }

        return $transfers;
    }

    /**
     * @param \Orm\Zed\Url\Persistence\SpyUrl $urlEntity
     *
     * @return \Generated\Shared\Transfer\SitemapUrlTransfer
     */
    private function createSitemapUrlTransfer(SpyUrl $urlEntity): SitemapUrlTransfer
    {
        return (new SitemapUrlTransfer())
            ->setUrl($this->formatUrl($urlEntity))
            ->setUpdatedAt($urlEntity->getVirtualColumn('updated_at'))
            ->setResourceId($urlEntity->getFkResourcePage())
            ->setResourceType(CategorySitemapConnectorConfig::RESOURCE_TYPE);
    }

    /**
     * @param \Orm\Zed\Url\Persistence\SpyUrl $urlEntity
     *
     * @return string
     */
    private function formatUrl(SpyUrl $urlEntity): string
    {
        return sprintf(
            self::URL_FORMAT,
            $this->config->getYvesBaseUrl(),
            rtrim($urlEntity->getUrl(), '/'),
        );
    }
}
