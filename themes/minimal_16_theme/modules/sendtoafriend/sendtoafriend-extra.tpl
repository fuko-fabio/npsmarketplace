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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<li class="sendtofriend">
    <a id="send_friend_button" href="#send_friend_form" class="btn btn-default button button-small">
        <i class="icon-envelope"></i> {l s='Send to a friend' mod='sendtoafriend'}
    </a>

    <div style="display: none;">
        <div id="send_friend_form">
            <h2 class="page-subheading">
                {l s='Send to a friend' mod='sendtoafriend'}
            </h2>

            <div class="row">
                <div class="product clearfix col-xs-12 col-sm-6">
                    <img src="{$link->getImageLink($stf_product->link_rewrite, $stf_product_cover, 'home_default')|escape:'html':'UTF-8'}"
                         height="{$homeSize.height}" width="{$homeSize.width}"
                         alt="{$stf_product->name|escape:'html':'UTF-8'}"/>

                    <div class="product_desc">
                        <p class="product_name">
                            <strong>{$stf_product->name}</strong>
                        </p>
                        {$stf_product->description_short}
                    </div>
                </div>
                <!-- .product -->
                <div class="send_friend_form_content col-xs-12 col-sm-6" id="send_friend_form_content">
                    <p id="send_friend_form_error" class="alert alert-error"></p>
                    <div class="form_container">
                        <p class="intro_form">
                            {l s='Recipient' mod='sendtoafriend'}
                        </p>

                        <div class="form-group">
                            <label for="friend_name">
                                {l s='Name of your friend' mod='sendtoafriend'} <sup class="required">*</sup>
                            </label>
                            <input id="friend_name" class="is_required validate form-control" data-validate="isGenericName" name="friend_name" type="text" value=""/>
                        </div>

                        <div class="form-group">
                            <label for="friend_email">
                                {l s='E-mail address of your friend' mod='sendtoafriend'} <sup class="required">*</sup>
                            </label>
                            <input id="friend_email" class="is_required validate form-control" data-validate="isEmail" name="friend_email" type="text" value=""/>
                        </div>

                        <p class="txt_required">
                            <sup class="required">*</sup> {l s='Required fields' mod='sendtoafriend'}
                        </p>
                    </div>
                </div>
                <!-- .send_friend_form_content -->
            </div>
            <p class="submit">
                <input class="button ccl" type="button" value="{l s='Cancel' mod='sendtoafriend'}" onclick="$.fancybox.close();"/>
                <input id="sendEmail" class="button" type="button" value="{l s='Send' mod='sendtoafriend'}" />
            </p>
        </div>
    </div>
</li>
{addJsDef stf_secure_key=$stf_secure_key}
{addJsDefL name=stf_msg_success}{l s='Your e-mail has been sent successfully' mod='sendtoafriend' js=1}{/addJsDefL}
{addJsDefL name=stf_msg_error}{l s='Your e-mail could not be sent. Please check the e-mail address and try again.' mod='sendtoafriend' js=1}{/addJsDefL}
{addJsDefL name=stf_msg_title}{l s='Send to a friend' mod='sendtoafriend' js=1}{/addJsDefL}
{addJsDefL name=stf_msg_required}{l s='You did not fill required fields' mod='sendtoafriend' js=1}{/addJsDefL}
