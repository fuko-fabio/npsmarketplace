{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !$isCustomerFavoriteProduct AND $isLogged}
<li id="npsfavorite_block_extra_add" class="add">
    <a href="javascript:void(0);" class="btn btn-default button button-small">
        <i class="icon-heart-empty"></i> {l s='Add this product to my list of favorites.' mod='npsfavorite'}
    </a>
</li>
{/if}
{if $isCustomerFavoriteProduct AND $isLogged}
<li id="npsfavorite_block_extra_remove">
    <a href="javascript:void(0);" class="btn btn-default button button-small">
        <i class="icon-heart-empty"></i> {l s='Remove this product from my favorite\'s list. ' mod='npsfavorite'}
    </a>
</li>
{/if}

<li id="npsfavorite_block_extra_added">
    <a href="javascript:void(0);" class="btn btn-default button button-small">
        <i class="icon-heart-empty"></i> {l s='Remove this product from my favorite\'s list. ' mod='npsfavorite'}
    </a>
</li>
<li id="npsfavorite_block_extra_removed">
    <a href="javascript:void(0);" class="btn btn-default button button-small">
        <i class="icon-heart"></i> {l s='Add this product to my list of favorites.' mod='npsfavorite'}
    </a>
</li>