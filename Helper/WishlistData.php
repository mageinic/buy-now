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

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;

/**
 *  Helper Class For Wishlist
 */
class WishlistData extends Data
{
    /**
     * Get Add To Cart Params
     *
     * @param Item $item
     * @param bool $addReferer
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAddToCartParams($item, $addReferer = false): string
    {
        $params = $this->_getCartUrlParameters($item);
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] = '';
        if ($addReferer) {
            $params = $this->addRefererToParams($params);
        }
        return $this->_postDataHelper->getPostData(
            $this->_getUrlStore($item)->getUrl('buynow/cart/buynowwishlist'),
            $params
        );
    }
}
