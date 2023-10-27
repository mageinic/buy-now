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
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\LocalizedToNormalized;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;

/**
 * Class Of Buy Now Button
 */
class BuyNowCart extends Add implements HttpPostActionInterface
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var Session
     */
    protected Session $checkoutSession;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Validator
     */
    protected Validator $formKeyValidator;

    /**
     * @var RequestQuantityProcessor|null
     */
    protected ?RequestQuantityProcessor $quantityProcessor;

    /**
     * @var Cart
     */
    private Cart $cartHelper;

    /**
     * @var ResolverInterface
     */
    private ResolverInterface $resolver;

    /**
     * @var Escaper
     */
    private Escaper $escaper;

    /**
     * @var Data
     */
    private Data $buyNowHelper;

    /**
     * Buy Now Cart Constructor
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param Cart $cartHelper
     * @param Escaper $escaper
     * @param ResolverInterface $resolver
     * @param Data $buyNowHelper
     * @param RequestQuantityProcessor $quantityProcessor
     */
    public function __construct(
        Context                    $context,
        ScopeConfigInterface       $scopeConfig,
        Session                    $checkoutSession,
        StoreManagerInterface      $storeManager,
        Validator                  $formKeyValidator,
        CustomerCart               $cart,
        ProductRepositoryInterface $productRepository,
        Cart                       $cartHelper,
        Escaper                    $escaper,
        ResolverInterface          $resolver,
        Data                       $buyNowHelper,
        RequestQuantityProcessor  $quantityProcessor
    ) {
        $this->buyNowHelper = $buyNowHelper;
        $this->cartHelper = $cartHelper;
        $this->escaper = $escaper;
        $this->resolver = $resolver;
        $this->quantityProcessor = $quantityProcessor;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository,
            $quantityProcessor
        );
    }

    /**
     * Add product to shopping cart action
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new LocalizedToNormalized(['locale' => $this->resolver->getLocale()]);
                $params['qty'] = $this->quantityProcessor->prepareQuantity($params['qty']);
                $params['qty'] = $filter->filter($params['qty']);
            }
            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }
            if (!$this->buyNowHelper->keepCartProducts()) {
                $this->cart->truncate();
            }
            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }
            $this->cart->save();
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                [
                    'product' => $product,
                    'request' => $this->getRequest(),
                    'response' => $this->getResponse()
                ]
            );
            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
                return $this->goBack($baseUrl . 'checkout/', $product);
            }
        } catch (LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage($this->escaper->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($message));
                }
            }
            $url = $this->_checkoutSession->getRedirectUrl(true);
            if (!$url) {
                $cartUrl = $this->cartHelper->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }
            return $this->goBack($url);
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add this item to your shopping cart right now.')
            );
        }
        return $this->goBack();
    }
}
