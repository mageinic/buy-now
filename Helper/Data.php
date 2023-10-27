<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_BuyNow
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\BuyNow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Helper Class Data For Buy Now Button
 */
class Data extends AbstractHelper
{
    /**#@+
     * Constants For Module Config Value Path.
     */
    public const IS_ENABLE = 'buy_now_button/general/enable';
    public const BUTTON_TITLE = 'buy_now_button/general/button_title';
    public const SHOW_ON_LISTING = 'buy_now_button/general/show_on_listing_page';
    public const SHOW_ON_PRODUCT_PAGE = 'buy_now_button/general/show_on_product_detail_page';
    public const SHOW_ON_RELATED = 'buy_now_button/general/show_on_related_product';
    public const SHOW_ON_UPSELL = 'buy_now_button/general/show_on_upsell_product';
    public const SHOW_ON_WISHLIST = 'buy_now_button/general/show_on_wishlist_product';
    public const SHOW_ON_COMPARE = 'buy_now_button/general/show_on_compare_list_product';
    public const SHOW_ON_CROSS = 'buy_now_button/general/show_on_cross_sell_product';
    public const KEEP_CART_PRODUCTS = 'buy_now_button/general/keep_cart_products';
    public const SHOW_ON_WIDGET_PRODUCT = 'buy_now_button/general/show_on_widget_product';
    /**#@-*/

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var int
     */
    private int $storeId;

    /**
     * Data constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeId = $this->storeManager->getStore()->getId();
    }

    /**
     * Get Module Enable
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->scopeConfig->getValue(
            self::IS_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get Button Title
     *
     * @return string
     */
    public function getButtonTitle(): string
    {
        return $this->scopeConfig->getValue(
            self::BUTTON_TITLE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Category List Page
     *
     * @return bool
     */
    public function isEnableListingPage(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_LISTING,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Product Page
     *
     * @return bool
     */
    public function isEnableProductPage(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_PRODUCT_PAGE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Related Product
     *
     * @return bool
     */
    public function isEnableRelatedProduct(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_RELATED,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Upsell Product
     *
     * @return bool
     */
    public function isEnableUpsellProduct(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_UPSELL,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Cross-Sell Product
     *
     * @return bool
     */
    public function isEnableCrossSellProduct(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_CROSS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Wishlist Product
     *
     * @return bool
     */
    public function isEnableWishlistProduct(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_WISHLIST,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Compare Product
     *
     * @return bool
     */
    public function isEnableCompareProduct(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_COMPARE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Enable On Widget Product
     *
     * @return bool
     */
    public function isEnableWidgetProduct(): bool
    {
        return $this->scopeConfig->getValue(
            self::SHOW_ON_WIDGET_PRODUCT,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Keep Products On Cart Product
     *
     * @return bool
     */
    public function keepCartProducts(): bool
    {
        return $this->scopeConfig->getValue(
            self::KEEP_CART_PRODUCTS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
