{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{addJsDefL name=p24PaymentUrl}{$p24_payment_url}{/addJsDefL}

<div class="row">
    <div class="form-group col-sm-12">
    <a class="pull-right" rel="nofollow" href="http://www.przelewy24.pl/" target="_blank"><img src="{$img_dir}logo_przelewy24.png"/></a>
    <label>{l s='Terms of Przelewy 24 service'}</label>
    <p class="checkbox">
        <input type="checkbox" name="cp24" id="cp24" value="1"/>
        <label for="cp24">{l s='I accept the' mod='npsprzelewy24'}</label>
        <a class="iframe" rel="nofollow" href="{$p24_agreement_url}" target="_blank">{l s='Regulations of Przelewy24.' mod='npsprzelewy24'}</a>
    </p>
    </div>
</div>
<div class="cart_navigation">
    <button class="button btn btn-default button-medium pull-right" type="button" onclick="payWithP24()">
        <span>{l s='Pay with Przelewy24' mod='npsprzelewy24'} <i class="icon-chevron-right right"></i></span>
    </button>
</div>