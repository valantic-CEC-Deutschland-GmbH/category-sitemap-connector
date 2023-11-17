<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Communication\Plugin;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use ValanticSpryker\Zed\Sitemap\Dependency\Plugin\SitemapCreatorPluginInterface;

/**
 * @method \ValanticSpryker\Zed\CategorySitemapConnector\Business\CategorySitemapConnectorFacadeInterface getFacade()
 */
class CategorySitemapCreatorPlugin extends AbstractPlugin implements SitemapCreatorPluginInterface
{
 /**
  * @return array<\Generated\Shared\Transfer\SitemapFileTransfer>
  */
    public function createSitemapXml(): array
    {
        return $this->getFacade()
            ->createSitemapXml();
    }
}
