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

namespace MageINIC\BuyNow\ViewModel;

use Magento\Catalog\Helper\Output;
use Magento\Catalog\Helper\Product\Compare;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use MageINIC\BuyNow\Helper\Data;
use MageINIC\BuyNow\Helper\WishlistData;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 *  Class Buy Now For View Model
 */
class BuyNow implements ArgumentInterface
{
    /**
     * @var Output
     */
    protected Output $outputHelper;

    /**
     * @var Compare
     */
    protected Compare $compareHelper;

    /**
     * @var WishlistHelper
     */
    protected WishlistHelper $wishlistHelper;

    /**
     * @var Data
     */
    protected Data $buyNowHelper;

    /**
     * @var WishlistData
     */
    protected WishlistData $buyNowWishlistHelper;

    /**
     * @var PostHelper
     */
    protected PostHelper $postHelper;

    /**
     * Buy Now View Model Constructor
     *
     * @param Output $outputHelper
     * @param Compare $compareHelper
     * @param WishlistHelper $wishlistHelper
     * @param PostHelper $postHelper
     * @param Data $buyNowHelper
     * @param WishlistData $buyNowWishlistHelper
     */
    public function __construct(
        Output            $outputHelper,
        Compare           $compareHelper,
        WishlistHelper    $wishlistHelper,
        PostHelper        $postHelper,
        Data              $buyNowHelper,
        WishlistData      $buyNowWishlistHelper
    ) {
        $this->outputHelper = $outputHelper;
        $this->compareHelper = $compareHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->postHelper = $postHelper;
        $this->buyNowHelper = $buyNowHelper;
        $this->buyNowWishlistHelper = $buyNowWishlistHelper;
    }

    /**
     * Get Output Helper
     *
     * @return Output
     */
    public function getOutputHelper(): Output
    {
        return $this->outputHelper;
    }

    /**
     * Get Compare Helper
     *
     * @return Compare
     */
    public function getCompareHelper(): Compare
    {
        return $this->compareHelper;
    }

    /**
     * Get Wishlist Helper
     *
     * @return WishlistHelper
     */
    public function getWishlistHelper(): WishlistHelper
    {
        return $this->wishlistHelper;
    }

    /**
     * Get Post Helper
     *
     * @return PostHelper
     */
    public function getPostHelper(): PostHelper
    {
        return $this->postHelper;
    }

    /**
     * Get Buy Now Helper Data
     *
     * @return Data
     */
    public function getBuyNowHelper(): Data
    {
        return $this->buyNowHelper;
    }

    /**
     * Get Wishlist Helper Data
     *
     * @return WishlistData
     */
    public function getBuyNowWishlistHelper(): WishlistData
    {
        return $this->buyNowWishlistHelper;
    }
}
