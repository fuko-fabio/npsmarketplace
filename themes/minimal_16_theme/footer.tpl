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
{if !$content_only}
					</div><!-- #center_column -->
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
				</div><!-- #columns -->
			</div><!-- .columns-container -->
			<!-- Footer -->
			<div class="footer-container">
				<footer id="footer"  class="container">
					<div class="row">
					    {$HOOK_FOOTER}
					<section class="bottom-eu">
                        <img class="pull-left" src="{$img_dir}kapital_ludzki.png"/>
                        <a class="text-center" href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
                            <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if $logo_image_width} width="{$logo_image_width}"{/if}{if $logo_image_height} height="{$logo_image_height}"{/if}/>
                        </a>
                        <img class="pull-right" src="{$img_dir}unia.png"/>
                        <p class="clearfix">{l s="Projekt współfinansowany przez Unię Europejską w ramach Europejskiego Funduszu Społecznego"}</p>
                    </section>
                        <section class="bottom-footer">
                            <div>
                                Copyright &copy; 2014 <a href="http://labsintown.com" title="LabsInTown">Labs in Town</a>
                            </div>
                            <div class="pull-right">
                                <a target="_blank" href="http://npsoftware.pl" title="nps software"><span class="cname">nps</span><span class="csoftware"> software</span></a>
                            </div>
                        </section>
					</div>
				</footer>
			</div><!-- #footer -->
		</div><!-- #page -->
{/if}
{include file="$tpl_dir./global.tpl"}
	</body>
</html>