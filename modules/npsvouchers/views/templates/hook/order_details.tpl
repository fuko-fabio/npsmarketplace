{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*}
{if $cart_rules}
<h1 class="page-heading">{l s='Vouchers' mod='npsvouchers'}</h1>
<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <tr>
        <td>{l s='ID' mod='npsvouchers'}</td>
        <td>{l s='Name' mod='npsvouchers'}</td>
        <td>{l s='Discount' mod='npsvouchers'}</td>
      </tr>
    </thead>
    <tbody>
      {foreach from=$cart_rules item=cart_rule}
      <tr>
        <td>{$cart_rule.id_cart_rule}</td>
        <td>{$cart_rule.name}</td>
        <td>-{displayPrice price=$cart_rule.value currency=$currency->id}</td>
      </tr>
      {/foreach}
    </tbody>
  </table>
</div>
{/if}
