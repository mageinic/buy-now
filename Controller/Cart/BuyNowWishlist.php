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

namespace MageINIC\BuyNow\Controller\Cart;

use Exception;
use MageINIC\BuyNow\Helper\Data;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Cart as checkoutCart;
use Magento\Framework\App\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Wishlist\Controller\Index\Cart as wishlistCart;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data as wishlistHelper;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\ResourceModel\Item\Option\Collection;

/**
 *  Class Of Buy Now Button For Wishlist
 */
class BuyNowWishlist extends wishlistCart implements HttpPostActionInterface
{
    /**
     * @var Data
     */
    protected Data $buyNowHelper;

    /**
     * @var OptionFactory
     */
    protected OptionFactory $optionFactory;

    /**
     * Buy Now Wishlist Constructor
     *
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param LocaleQuantityProcessor $quantityProcessor
     * @param ItemFactory $itemFactory
     * @param checkoutCart $cart
     * @param OptionFactory $optionFactory
     * @param Product $productHelper
     * @param Escaper $escaper
     * @param wishlistHelper $helper
     * @param Cart $cartHelper
     * @param Validator $formKeyValidator
     * @param Data $buyNowHelper
     */
    public function __construct(
        Action\Context            $context,
        WishlistProviderInterface $wishlistProvider,
        LocaleQuantityProcessor   $quantityProcessor,
        ItemFactory               $itemFactory,
        checkoutCart              $cart,
        OptionFactory             $optionFactory,
        Product                   $productHelper,
        Escaper                   $escaper,
        wishlistHelper            $helper,
        Cart                      $cartHelper,
        Validator                 $formKeyValidator,
        Data                      $buyNowHelper
    ) {
        $this->optionFactory = $optionFactory;
        $this->buyNowHelper = $buyNowHelper;
        parent::__construct(
            $context,
            $wishlistProvider,
            $quantityProcessor,
            $itemFactory,
            $cart,
            $optionFactory,
            $productHelper,
            $escaper,
            $helper,
            $cartHelper,
            $formKeyValidator
        );
        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        $this->itemFactory = $itemFactory;
        $this->cart = $cart;
        $this->productHelper = $productHelper;
        $this->escaper = $escaper;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * @return Json|ResultInterface|Redirect
     */
    public function execute(): Json|ResultInterface|Redirect
    {
        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('wishlist/index/');
        }

        $itemId = (int)$this->getRequest()->getParam('item');
        $item = $this->itemFactory->create()->load($itemId);
        if (!$item->getId()) {
            $resultRedirect->setPath('wishlist/index');
            return $resultRedirect;
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $resultRedirect->setPath('wishlist/index');
            return $resultRedirect;
        }

        $qty = $this->getRequest()->getParam('qty');
        $postQty = $this->getRequest()->getPostValue('qty');
        if ($postQty !== null && $qty !== $postQty) {
            $qty = $postQty;
        }
        if (is_array($qty)) {
            $qty = $qty[$itemId] ?? 1;
        }
        $qty = $this->quantityProcessor->process($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        $redirectUrl = $this->_url->getUrl('wishlist/index');
        $configureUrl = $this->_url->getUrl(
            'wishlist/index/configure/',
            [
                'id' => $item->getId(),
                'product_id' => $item->getProductId(),
            ]
        );
        try {
            /**
             * @var Collection $options
             */
            $options = $this->optionFactory->create()
                ->getCollection()->addItemFilter([$itemId]);
            $item->setOptions($options->getOptionsByItem($itemId));
            $buyRequest = $this->productHelper->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                ['current_config' => $item->getBuyRequest()]
            );

            if (!$this->buyNowHelper->keepCartProducts()) {
                $this->cart->truncate();
            }
            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($this->cart, true);
            $this->cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            $refererUrl = $this->_url->getUrl(
                'checkout',
                ['_secure' => true]
            );
            if ($refererUrl && $refererUrl != $configureUrl) {
                $redirectUrl = $refererUrl;
            }
        } catch (ProductException $e) {
            $this->messageManager->addErrorMessage(
                __('This product(s) is out of stock.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addNoticeMessage($e->getMessage());
            $redirectUrl = $configureUrl;
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add the item to the cart right now.')
            );
        }
        $this->helper->calculate();
        if ($this->getRequest()->isAjax()) {
            /**
             * @var Json $resultJson
             */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $redirectUrl]);
            return $resultJson;
        }
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
