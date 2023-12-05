<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Persistence;

use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\Url\Persistence\Map\SpyUrlTableMap;
use Orm\Zed\UrlStorage\Persistence\Map\SpyUrlStorageTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \ValanticSpryker\Zed\CategorySitemapConnector\Persistence\CategorySitemapConnectorPersistenceFactory getFactory()
 */
class CategorySitemapConnectorRepository extends AbstractRepository implements CategorySitemapConnectorRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findActiveCategories(StoreTransfer $currentStore, int $page, int $pageLimit): array
    {
        $query = $this->getFactory()
           ->createSpyUrlQuery()
           ->filterByFkResourceCategorynode(null, Criteria::ISNOTNULL)
           ->filterByFkResourceRedirect(null, Criteria::ISNULL)
           ->useSpyLocaleQuery()
               ->useLocaleStoreQuery()
                   ->filterByFkStore($currentStore->getIdStore())
               ->endUse()
           ->endUse()
           ->useSpyCategoryNodeQuery()
               ->useCategoryQuery()
                    ->useSpyCategoryStoreQuery()
                        ->filterByFkStore($currentStore->getIdStore())
                    ->endUse()
               ->endUse()
           ->endUse()
           ->addJoin(SpyUrlTableMap::COL_ID_URL, SpyUrlStorageTableMap::COL_FK_URL, Criteria::INNER_JOIN)
           ->withColumn(SpyUrlStorageTableMap::COL_UPDATED_AT, 'updated_at')
           ->setOffset($this->calculateOffsetByPage($page, $pageLimit))
           ->setLimit($pageLimit);

        return $this->getFactory()
            ->createCategoryPageSitemapMapper()
            ->mapUrlEntitiesToSitemapUrlTransfers(
                $query->find(),
            );
    }

    /**
     * @param int $page
     * @param int $pageLimit
     *
     * @return int
     */
    private function calculateOffsetByPage(int $page, int $pageLimit): int
    {
        return ($page - 1) * $pageLimit;
    }
}
