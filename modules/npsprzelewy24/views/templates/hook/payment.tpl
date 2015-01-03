{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div class="row">
    <div class="col-sm-12">
    <a href="http://www.przelewy24.pl/" target="_blank"><img src="{$img_dir}logo_przelewy24.png"/></a> <strong>{l s='By clicking "Pay with Przelewy24" I accept the' mod='npsmarketplace'} <a href="{$p24_agreement_url}" target="_blank">{l s='Regulations of Przelewy24.' mod='npsmarketplace'}</a></strong>
    </div>
</div>
<div class="cart_navigation">
    <a class="button btn btn-default button-medium pull-right" href="{$p24_payment_url}"  title="{l s='Pay with Przelewy24' mod='npsprzelewy24'}" onclick="$.fancybox.showLoading();">
        <span>{l s='Pay with Przelewy24' mod='npsprzelewy24'} <i class="icon-chevron-right right"></i></span>
    </a>
</div>