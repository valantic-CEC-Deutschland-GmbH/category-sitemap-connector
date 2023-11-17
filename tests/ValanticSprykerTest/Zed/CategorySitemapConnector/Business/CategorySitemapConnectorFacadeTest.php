<?php

declare(strict_types = 1);

namespace Business;

use Codeception\Test\Unit;
use DOMDocument;
use Generated\Shared\Transfer\SitemapFileTransfer;
use Orm\Zed\Url\Persistence\Map\SpyUrlTableMap;
use Orm\Zed\Url\Persistence\SpyUrlQuery;
use Orm\Zed\UrlStorage\Persistence\Map\SpyUrlStorageTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use ValanticSprykerTest\Zed\CategorySitemapConnector\CategorySitemapConnectorTester;

class CategorySitemapConnectorFacadeTest extends Unit
{
    public CategorySitemapConnectorTester $tester;

    /**
     * @return void
     */
    public function testFacadeRendersCorrectAmountOfUrlsWithCorrectStructure(): void
    {
        // Arrange
        $this->tester->mockConfigMethod('getSitemapUrlLimit', 200, 'CategorySitemapConnector');
        $contentPageConnectorFacade = $this->tester->getLocator()->categorySitemapConnector()->facade();
        $storeFacade = $this->tester->getLocator()->store()->facade();

        /** @var \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Url\Persistence\SpyUrl> $validEntries */
        $validEntries = SpyUrlQuery::create()
            ->filterByFkResourceCategorynode(null, Criteria::ISNOTNULL)
            ->filterByFkResourceRedirect(null, Criteria::ISNULL)
            ->useSpyLocaleQuery()
                ->useLocaleStoreQuery()
                    ->filterByFkStore($storeFacade->getCurrentStore()->getIdStore())
                ->endUse()
            ->endUse()
            ->addJoin(SpyUrlTableMap::COL_ID_URL, SpyUrlStorageTableMap::COL_FK_URL, Criteria::INNER_JOIN)
            ->select(SpyUrlTableMap::COL_URL)
            ->find()
            ->getData();

        $validEntryCount = count($validEntries);

        // Act
        /** @var array<\Generated\Shared\Transfer\SitemapFileTransfer> $resultsXmlFile */
        $resultsXmlFile = $contentPageConnectorFacade->createSitemapXml();

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

                $trimmedUrl = rtrim(parse_url($loc)['path'], '/');
                self::assertContains($trimmedUrl, $validEntries);

                self::assertNotFalse(parse_url($loc));
                self::assertNotEmpty($lastMod);
            }

            $totalCount += $urls->count();
        }

        self::assertSame($validEntryCount, $totalCount);
    }
}
