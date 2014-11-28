{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{addJsDefL name=npsAjaxUrl}{$nps_ajax_url}{/addJsDefL}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsmarketplace'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Marketing' mod='npsmarketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-marketing-view">
    <h1 class="page-heading bottom-indent">{l s='Marketing' mod='npsmarketplace'}</h1>
    <p class="alert alert-error code-error" style="display: none"><span class="alert-content">{l s='An error occurred while generating code. Try again or please contact customer service..' mod='npsmarketplace'}</span></p>
    <p class="alert alert-info"><span class="alert-content">{l s='Here you can generate code that you can paste on any page. Thanks to this code your events from our store will be visible to other pages.' mod='npsmarketplace'}</span></p>
    <form class="marketing-code">
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="in_row">{l s='Max events in row' mod='npsmarketplace'}</label>
                <select class="form-control" id="in_row" name="in_row">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option> 
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="max_events">{l s='Total max events' mod='npsmarketplace'}</label>
                <select class="form-control" id="max_events" name="max_events">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="6">6</option>
                    <option value="8">8</option> 
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="12">12</option> 
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="town">{l s='Events from town' mod='npsmarketplace'}</label>
                <select class="form-control" id="town" name="id_town">
                    <option value="0" selected="">{l s='All' mod='npsmarketplace'}</option> 
                    {foreach from=$towns item=town}
                        <option value="{$town.id_town}">{$town.name}</option> 
                    {/foreach}
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="language">{l s='Language' mod='npsmarketplace'}</label>
                <select class="form-control" id="language" name="id_lang">
                    {foreach from=$languages item=lang}
                        <option value="{$lang.id_lang}">{$lang.name}</option> 
                    {/foreach}
                </select>
            </div>
        </div>
         <div class="row">
            <div class="form-group col-md-6">
                <label for="width">{l s='Frame width(px)' mod='npsmarketplace'}</label>
                <input class="validate form-control" data-validate="isNumber" type="number" id="width" name="width" value="600"/>
                <span class="form_info">{l s='Recomended minimal width 300px' mod='npsmarketplace'}</span>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="form-group col-md-6">
            <button type="button" class="btn btn-default button button-medium" onclick="getTheCode();">{l s='Get the code' mod='npsmarketplace'}</button>
        </div>
    </div>
    <div class="form-group">
        <label for="iframe_code">{l s='Your code' mod='npsmarketplace'}</label>
        <textarea readonly="" rows="5" class="form-control textarea-autosize" id="iframe_code" name="iframe_code"></textarea>
    </div>
    <label>{l s='Preview' mod='npsmarketplace'}</label>
    <div id="iframe_code_preview"></div>
</div>