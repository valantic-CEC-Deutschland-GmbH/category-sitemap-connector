<?php

declare(strict_types = 1);

namespace ValanticSpryker\Zed\CategorySitemapConnector\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \ValanticSpryker\Zed\CategorySitemapConnector\Business\CategorySitemapConnectorBusinessFactory getFactory()
 */
class CategorySitemapConnectorFacade extends AbstractFacade implements CategorySitemapConnectorFacadeInterface
{
    /**
     * @inheritDoc
     */
    public function createSitemapXml(): array
    {
        return $this->getFactory()
            ->createCategorySitemapCreator()
            ->createCategorySitemapXml();
    }
}
