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

define([
    'jquery',
    'mage/translate',
    'underscore',
    'Magento_Catalog/js/product/view/product-ids-resolver'
], function ($, $t, _, idsResolver) {
    'use strict';
    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {

            ajaxSubmit: function (form) {
                var self = this,
                    productIds = idsResolver(form),
                    formData;

                var formAction = form.attr('action');

                if (!formAction.includes("buynow")) {
                    $(self.options.minicartSelector).trigger('contentLoading');
                    self.disableAddToCartButton(form);
                }

                formData = new FormData(form[0]);

                $.ajax({
                    url: formAction,
                    data: formData,
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,

                    beforeSend: function () {
                        if (formAction.includes("buynow")) {
                            $("body").trigger('processStart');
                        }
                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStart);
                        }
                    },

                    success: function (res) {
                        var eventData, parameters;

                        $(document).trigger('ajax:addToCart', {
                            'sku': form.data().productSku,
                            'productIds': productIds,
                            'form': form,
                            'response': res
                        });

                        if (formAction.includes("buynow")) {
                            $("body").trigger('processStop');
                        }

                        if (self.isLoaderEnabled()) {
                            $('body').trigger(self.options.processStop);
                        }

                        if (res.backUrl) {

                            if (!formAction.includes("buynow")) {
                                eventData = {
                                    'form': form,
                                    'redirectParameters': []
                                };

                                $('body').trigger('catalogCategoryAddToCartRedirect', eventData);

                                if (eventData.redirectParameters.length > 0) {
                                    parameters = res.backUrl.split('#');
                                    parameters.push(eventData.redirectParameters.join('&'));
                                    res.backUrl = parameters.join('#');
                                }
                            }

                            self._redirect(res.backUrl);

                            return;
                        }

                        if (res.messages) {
                            $(self.options.messagesSelector).html(res.messages);
                        }

                        if (res.minicart) {
                            $(self.options.minicartSelector).replaceWith(res.minicart);
                            $(self.options.minicartSelector).trigger('contentUpdated');
                        }

                        if (res.product && res.product.statusText) {
                            $(self.options.productStatusSelector)
                                .removeClass('available')
                                .addClass('unavailable')
                                .find('span')
                                .html(res.product.statusText);
                        }
                        self.enableAddToCartButton(form);
                    },

                    error: function (res) {
                        $(document).trigger('ajax:addToCart:error', {
                            'sku': form.data().productSku,
                            'productIds': productIds,
                            'form': form,
                            'response': res
                        });
                    },

                    complete: function (res) {
                        if (res.state() === 'rejected') {
                            location.reload();
                        }
                    }
                });
            }
        });

        return $.mage.catalogAddToCart;
    }
});
