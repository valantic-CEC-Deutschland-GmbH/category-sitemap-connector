# Content Pages Sitemap Connector

# Description

This module is used alongside `valantic-spryker/sitemap` Sitemap module to extend the sitemap with category URLs.

# Usage

1. `composer require valantic-spryker/category-sitemap-connector`
2. Since this is under ValanticSpryker namespace, make sure that in `config_default`:
   1. `$config[KernelConstants::CORE_NAMESPACES]` has the namespace
   2. `$config[KernelConstants::PROJECT_NAMESPACES]` has the namespace
5. Add `CategorySitemapCreatorPlugin` to `\ValanticSpryker\Zed\Sitemap\SitemapDependencyProvider::getSitemapCreatorPluginStack`
6. Add `\ValanticSpryker\Shared\CategorySitemapConnector\CategorySitemapConnectorConstants::RESOURCE_TYPE` to `\ValanticSpryker\Yves\Sitemap\SitemapDependencyProvider::getAvailableSitemapRouteResources`
7. Now the Sitemap will include **published** URLs of categories.

# Testing

Tests do not work without Spryker environment, since Spryker helpers are used. To run tests, execute following command in root Spryker directory:

`codecept run -c vendor/valantic-spryker/category-sitemap-connector/tests/ValanticSprykerTest/Zed/CategorySitemapConnector`
