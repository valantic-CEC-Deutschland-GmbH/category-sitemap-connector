<?php

declare(strict_types = 1);

namespace ValanticSprykerTest\Zed\CategorySitemapConnector\Business;

use Codeception\Test\Unit;
use DOMDocument;
use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\SitemapFileTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use InvalidArgumentException;
use League\Uri\Uri;
use Orm\Zed\Url\Persistence\Map\SpyUrlTableMap;
use Orm\Zed\Url\Persistence\SpyUrlQuery;
use Orm\Zed\UrlStorage\Persistence\Map\SpyUrlStorageTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use ValanticSprykerTest\Zed\CategorySitemapConnector\CategorySitemapConnectorTester;

/**
 * Auto-generated group annotations
 *
 * @group ValanticSprykerTest
 * @group Zed
 * @group CategorySitemapConnector
 * @group Business
 * @group Facade
 * @group CategorySitemapConnectorFacadeTest
 * Add your own group annotations below this line
 */
class CategorySitemapConnectorFacadeTest extends Unit
{
    public CategorySitemapConnectorTester $tester;

    /**
     * @return void
     */
    public function testFacadeRendersCorrectAmountOfUrlsWithCorrectStructure(): void
    {
        // Arrange
        $this->tester->mockConfigMethod('getSitemapUrlLimit', 1_000, 'CategorySitemapConnector');
        $categoryConnectorFacade = $this->tester->getLocator()->categorySitemapConnector()->facade();
        $storeFacade = $this->tester->getLocator()->store()->facade();

        /** @var array<\Orm\Zed\Url\Persistence\SpyUrl> $validEntries */
        $validEntries = SpyUrlQuery::create()
            ->filterByFkResourceCategorynode(null, Criteria::ISNOTNULL)
            ->filterByFkResourceRedirect(null, Criteria::ISNULL)
            ->useSpyCategoryNodeQuery()
                  ->useCategoryQuery()
                    ->useSpyCategoryStoreQuery()
                        ->filterByFkStore($storeFacade->getCurrentStore()->getIdStore())
                  ->endUse()
                ->endUse()
            ->endUse()
            ->addJoin(SpyUrlTableMap::COL_ID_URL, SpyUrlStorageTableMap::COL_FK_URL, Criteria::INNER_JOIN)
            ->select(SpyUrlTableMap::COL_URL)
            ->find()
            ->getData();

        $validEntries = array_map(static function (string $url): string {
            return Uri::createFromString($url)->toString();
        }, $validEntries);

        $validEntryCount = count($validEntries);

        // Act
        /** @var array<\Generated\Shared\Transfer\SitemapFileTransfer> $resultsXmlFile */
        $resultsXmlFile = $categoryConnectorFacade->createSitemapXml();

        $totalCount = 0;

        // Assert
        foreach ($resultsXmlFile as $result) {
            self::assertInstanceOf(SitemapFileTransfer::class, $result);
            self::assertNotEmpty($result->getContent());
            self::assertNotEmpty($result->getStoreName());
            self::assertNotFalse(parse_url($result->getYvesBaseUrl()));

            $domDoc = new DOMDocument();
            $domDoc->loadXML($result->getContent());
            $urls = $domDoc->getElementsByTagName('url');

            foreach ($urls as $url) {
                $loc = $url->getElementsByTagName('loc')->item(0)->textContent;
                $lastMod = $url->getElementsByTagName('lastmod')->item(0)->textContent;

                self::assertContains(rtrim(parse_url($loc)['path']), $validEntries);

                self::assertNotFalse(parse_url($loc));
                self::assertNotEmpty($lastMod);
            }

            $totalCount += $urls->count();
        }

        self::assertSame($validEntryCount, $totalCount);
    }

    /**
     * @return void
     */
    public function testOnlyLinksVisibleToStoreAreRendered(): void
    {
        // ARRANGE
        $allStores = $this->tester->getLocator()->store()->facade()->getAllStores();
        $currentStore = $this->tester->getLocator()->store()->facade()->getCurrentStore();
        $otherStore = $this->getDifferentCurrentStore($allStores, $currentStore);

        $locale1 = $this->tester->getLocator()->locale()->facade()->getLocale($currentStore->getDefaultLocaleIsoCode());
        $locale2 = $this->tester->getLocator()->locale()->facade()->getLocale($otherStore->getDefaultLocaleIsoCode());

        $category1 = $this->tester->haveCategory([
            CategoryTransfer::URL => '/test-url1',
        ]);
        $category2 = $this->tester->haveCategory([
            CategoryTransfer::URL => '/test-url2',
        ]);

        $this->tester->haveUrl([
            UrlTransfer::FK_RESOURCE_CATEGORYNODE => $category1->getCategoryNode()->getIdCategoryNode(),
            UrlTransfer::URL => $category1->getUrl(),
            UrlTransfer::FK_LOCALE => $locale1->getIdLocale(),
        ]);

        $this->tester->haveUrl([
            UrlTransfer::FK_RESOURCE_CATEGORYNODE => $category2->getCategoryNode()->getIdCategoryNode(),
            UrlTransfer::URL => $category2->getUrl(),
            UrlTransfer::FK_LOCALE => $locale2->getIdLocale(),
        ]);

        $this->tester->haveCategoryStoreRelation($category1->getIdCategory(), $currentStore->getIdStore());
        $this->tester->haveCategoryStoreRelation($category2->getIdCategory(), $otherStore->getIdStore());

        $url = SpyUrlQuery::create()
            ->findOneByFkResourceCategorynode($category1->getCategoryNode()->getIdCategoryNode());

        $url2 = SpyUrlQuery::create()
            ->findOneByFkResourceCategorynode($category2->getCategoryNode()->getIdCategoryNode());

        $this->tester->getLocator()->urlStorage()->facade()->publishUrl(
            [
                $url->getIdUrl(),
                $url2->getIdUrl(),
            ],
        );

        // ACT
        $sitemapXml = $this->tester->getLocator()->categorySitemapConnector()->facade()->createSitemapXml();

        // ASSERT
        self::assertTrue($this->containsUrlInSitemap($sitemapXml, $url->getUrl()));
        self::assertFalse($this->containsUrlInSitemap($sitemapXml, $url2->getUrl()));
    }

    /**
     * @param array<\Generated\Shared\Transfer\StoreTransfer> $allStores
     * @param \Generated\Shared\Transfer\StoreTransfer $currentStore
     *
     * @throws \InvalidArgumentException
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    private function getDifferentCurrentStore(array $allStores, StoreTransfer $currentStore): StoreTransfer
    {
        foreach ($allStores as $store) {
            if ($store->getIdStore() !== $currentStore->getIdStore()) {
                return $store;
            }
        }

        throw new InvalidArgumentException();
    }

    /**
     * @param array<\Generated\Shared\Transfer\SitemapFileTransfer> $sitemapXml
     * @param string $needle
     *
     * @return bool
     */
    private function containsUrlInSitemap(array $sitemapXml, string $needle): bool
    {
        foreach ($sitemapXml as $item) {
            if (strpos($item->getContent(), $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
