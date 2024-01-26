<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Persistence\Mapper;

use Propel\Runtime\Collection\ObjectCollection;

interface CategorySitemapConnectorMapperInterface
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Url\Persistence\SpyUrl> $urlEntities
     *
     * @return array<\Generated\Shared\Transfer\SitemapUrlNodeTransfer>
     */
    public function mapUrlEntitiesToSitemapUrlNodeTransfers(ObjectCollection $urlEntities): array;
}
