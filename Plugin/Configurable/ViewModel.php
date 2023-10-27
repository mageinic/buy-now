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
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_BuyNow
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\BuyNow\Plugin\Configurable;

use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Configuration View Model
 */
class ViewModel
{
    /**
     * Key `view_model`
     */
    private const VIEW_MODEL = 'view_model';

    /**
     * @var ArgumentInterface
     */
    private ArgumentInterface $viewModel;

    /**
     * View Model Construct
     *
     * @param ArgumentInterface $viewModel
     */
    public function __construct(ArgumentInterface $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    /**
     * Sets The View Model Before Rendering To Html
     *
     * @param DataObject $block
     * @return DataObject|null
     */
    public function beforeToHtml(DataObject $block): ?DataObject
    {
        if (!$block->hasData(self::VIEW_MODEL)) {
            $block->setData(self::VIEW_MODEL, $this->viewModel);
        }
        return null;
    }

    /**
     * After Plugin For Get Template
     *
     * @param ProductsList $subject
     * @param ProductsList $result
     * @return string
     */
    public function afterGetTemplate(ProductsList $subject, $result)
    {
        return 'MageINIC_BuyNow::catalog/product/widget/grid.phtml';
    }
}
