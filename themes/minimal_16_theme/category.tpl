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
{include file="$tpl_dir./errors.tpl"}
{if isset($category)}
    {if $category->id AND $category->active}

    {if $scenes || $category->description || $category->id_image}
        <div class="content_scene_cat">
                 {if $scenes}
                    <div class="content_scene">
                        <!-- Scenes -->
                        {include file="$tpl_dir./scenes.tpl" scenes=$scenes}
                        {if $category->description}
                            <div class="cat_desc rte">
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short">{$description_short}</div>
                                <div id="category_description_full" class="unvisible">{$category->description}</div>
                                <a href="{$link->getCategoryLink($category->id_category, $category.link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a>
                            {else}
                                <div>{$category->description}</div>
                            {/if}
                            </div>
                        {/if}
                        </div>
                    {else}
                    <!-- Category image -->
                    {*
                    <div class="content_scene_cat_bg" {if $category->id_image}style="background:url({$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}) 0 top no-repeat; background-size:contain; min-height:{$categorySize.height}px;" {/if}>
                    *}
                    <div class="content_scene_cat_bg">
                        {if $category->description}
                            <div class="cat_desc">
                            <span class="category-name">
                                {strip}
                                    {$category->name|escape:'html':'UTF-8'}
                                    {if isset($categoryNameComplement)}
                                        {$categoryNameComplement|escape:'html':'UTF-8'}
                                    {/if}
                                {/strip}
                            </span>
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short" class="rte">{$description_short}</div>
                                <div id="category_description_full" class="unvisible rte">{$category->description}</div>
                                <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a>
                            {else}
                                <div class="rte">{$category->description}</div>
                            {/if}
                            </div>
                        {/if}
                     
                     </div>
                  {/if}
            </div>
        {/if}

        {if isset($subcategories)}
        <!-- Subcategories -->
        <div id="subcategories">
            {assign 'subcount' count($subcategories) - 1}
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="category-box">
                                <a href="{$link->getCategoryLink($subcategories[0].id_category, $subcategories[0].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[0].name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategories[0].id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcategories[0].link_rewrite, $subcategories[0].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                                {/if}
                                </a>
                                <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[0].id_category, $subcategories[0].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[0].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                            </div>
                        </div>
                    </div>
                    {if $subcount >= 4}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="category-box">
                                <a href="{$link->getCategoryLink($subcategories[4].id_category, $subcategories[4].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[4].name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategories[4].id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcategories[4].link_rewrite, $subcategories[4].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                                {/if}
                                </a>
                                <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[4].id_category, $subcategories[4].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[4].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                            </div>
                        </div>
                        {if $subcount >= 5}
                        <div class="col-sm-6">
                            <div class="category-box">
                                <a href="{$link->getCategoryLink($subcategories[5].id_category, $subcategories[5].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[5].name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategories[5].id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcategories[5].link_rewrite, $subcategories[5].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                                {/if}
                                </a>
                                <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[5].id_category, $subcategories[5].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[5].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                            </div>
                        </div>
                        {/if}
                    </div>
                    {/if}
                </div>
                {if $subcount >= 1}
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="category-box">
                                <a href="{$link->getCategoryLink($subcategories[1].id_category, $subcategories[1].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[1].name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategories[1].id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcategories[1].link_rewrite, $subcategories[1].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                                {/if}
                                </a>
                                <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[1].id_category, $subcategories[1].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[1].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                            </div>
                        </div>
                        {if $subcount >= 2}
                        <div class="col-sm-6">
                            <div class="category-box">
                                <a href="{$link->getCategoryLink($subcategories[2].id_category, $subcategories[2].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[2].name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategories[2].id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcategories[2].link_rewrite, $subcategories[2].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                                {/if}
                                </a>
                                <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[2].id_category, $subcategories[2].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[2].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                            </div>
                        </div>
                        {/if}
                    </div>
                    {if $subcount >= 3}
                     <div class="row">
                        <div class="col-sm-12">
                            <div class="category-box">
                                <a href="{$link->getCategoryLink($subcategories[3].id_category, $subcategories[3].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[3].name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategories[3].id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcategories[3].link_rewrite, $subcategories[3].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                                {/if}
                                </a>
                                <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[3].id_category, $subcategories[3].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[3].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                            </div>
                        </div>
                    </div>
                    {/if}
                </div>
                {/if}
            </div>
            {if $subcount >= 6}
            <div class="row">
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[6].id_category, $subcategories[6].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[6].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[6].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[6].link_rewrite, $subcategories[6].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[6].id_category, $subcategories[6].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[6].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {if $subcount >= 7}
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[7].id_category, $subcategories[7].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[7].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[7].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[7].link_rewrite, $subcategories[7].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[7].id_category, $subcategories[7].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[7].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
                {if $subcount >= 8}
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[8].id_category, $subcategories[8].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[8].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[8].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[8].link_rewrite, $subcategories[8].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[8].id_category, $subcategories[8].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[8].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
                {if $subcount >= 9}
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[9].id_category, $subcategories[9].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[9].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[9].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[9].link_rewrite, $subcategories[9].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[9].id_category, $subcategories[9].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[9].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
            </div>
            {/if}
            {if $subcount >= 10}
            <div class="row">
                <div class="col-sm-6">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[10].id_category, $subcategories[10].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[10].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[10].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[10].link_rewrite, $subcategories[10].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[10].id_category, $subcategories[10].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[10].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                
                {if $subcount >= 11}
                <div class="col-sm-6">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[11].id_category, $subcategories[11].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[11].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[11].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[11].link_rewrite, $subcategories[11].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[11].id_category, $subcategories[11].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[11].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
            </div>
            {/if}
            {if $subcount >= 12}
            <div class="row">
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[12].id_category, $subcategories[12].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[12].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[12].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[12].link_rewrite, $subcategories[12].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[12].id_category, $subcategories[12].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[12].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                   
                {if $subcount >= 13}
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[13].id_category, $subcategories[13].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[13].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[13].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[13].link_rewrite, $subcategories[13].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[13].id_category, $subcategories[13].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[13].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
                {if $subcount >= 14}
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[14].id_category, $subcategories[14].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[14].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[14].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[14].link_rewrite, $subcategories[14].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[14].id_category, $subcategories[14].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[14].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
                {if $subcount >= 15}
                <div class="col-sm-3">
                    <div class="category-box">
                        <a href="{$link->getCategoryLink($subcategories[15].id_category, $subcategories[15].link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategories[15].name|escape:'html':'UTF-8'}" class="img">
                        {if $subcategories[15].id_image}
                            <img class="replace-2x" src="{$link->getCatImageLink($subcategories[15].link_rewrite, $subcategories[15].id_image, 'large_default')|escape:'html':'UTF-8'}" alt="" />
                        {else}
                            <img class="replace-2x" src="{$img_cat_dir}default-large_default.jpg" alt="" />
                        {/if}
                        </a>
                        <a class="subcategory-name" href="{$link->getCategoryLink($subcategories[15].id_category, $subcategories[15].link_rewrite)|escape:'html':'UTF-8'}">{$subcategories[15].name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                    </div>
                </div>
                {/if}
            </div>
            {/if}
		</div>
		{/if}
		{if $products}
			<div class="content_sortPagiBar clearfix">
            	<div class="sortPagiBar clearfix">
            		{include file="./product-sort.tpl"}
                	{include file="./nbr-product-page.tpl"}
				</div>
                <div class="top-pagination-content clearfix">
                	{include file="./product-compare.tpl"}
					{include file="$tpl_dir./pagination.tpl"}
                </div>
			</div>
			{include file="./product-list.tpl" products=$products}
			<div class="content_sortPagiBar">
				<div class="bottom-pagination-content clearfix">
					{include file="./product-compare.tpl" paginationId='bottom'}
                    {include file="./pagination.tpl" paginationId='bottom'}
				</div>
			</div>
		{/if}
	{elseif $category->id}
		<p class="alert alert-warning"><span class="alert-content">{l s='This category is currently unavailable.'}</span></p>
	{/if}
{/if}