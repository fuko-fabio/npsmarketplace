<!-- Created by michalz on 13.03.14 -->

<script type="text/javascript">
	function proceedPayment() {
		var form = document.getElementById('przelewy24Form');

		{if $p24_validationRequired}
			var link = document.getElementById('proceedPaymentLink');
			var output = document.getElementById('ajaxOutput');
			var orderDescription = document.getElementById('orderDescription');
			link.children[0].style.display = 'inline';

			var xmlHttp = new XMLHttpRequest();

			xmlHttp.onreadystatechange = function () {
				if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
					var response = xmlHttp.responseText;
					if (response.substr(0, 2) == 'OK') {
						output.innerHTML = '{l s='' mod='przelewy24'}';
						orderDescription.value = 'Zamówienie ' + response.substr(2);
						form.submit();
					} else if (response == 'INVALID_SESSION_ID') {
						output.innerHTML = '{l s='Error. Invalid data.' mod='przelewy24'}';
					} else {
						output.innerHTML = response;
					}
					link.children[0].style.display = 'none';
				} else if (xmlHttp.status == 404) {
					link.children[0].style.display = 'none';
					output.innerHTML = '{l s='Error. File not found.' mod='przelewy24'}';
				}
			};

			var postData = 'p24_session_id={$p24_session_id}';

			xmlHttp.open('POST', '{$modules_dir}npsprzelewy24/ajax/proceedPayment.php');
			xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlHttp.send(postData);
		{else}
			form.submit();
		{/if}
	}
</script>

{capture name=path}{l s='Pay with Przelewy24' mod='przelewy24'}{/capture}

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="box" style="overflow: auto;">
	<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Payment confirmation' mod='przelewy24'}"/></a>&nbsp;{l s="Pay with Przelewy24" mod="przelewy24"}</h2>
	<hr/>
	{if isset($productsNumber) && $productsNumber <= 0}
		<p style="font-size: 16px; line-height: 20px;">{l s='Your shopping cart is empty.' mod='przelewy24'}</p>
	{else}
		<p style="font-size: 16px; line-height: 20px;">
			{l s='Press "Confirm" to confirm your order and be redirected to przelewy24.pl, where you can complete the payment process.' mod='przelewy24'}
		<form action="https://{$p24_url}" method="post" id="przelewy24Form" name="przelewy24Form"
		      accept-charset="utf-8">
			<input type="hidden" name="p24_session_id" value="{$p24_session_id}"/>
			<input type="hidden" name="p24_id_sprzedawcy" value="{$p24_id_sprzedawcy}"/>
			<input type="hidden" name="p24_kwota" value="{$p24_kwota}"/>
			<input type="hidden" name="p24_opis" value="{$p24_opis}" id="orderDescription"/>
			<input type="hidden" name="p24_klient" value="{$p24_klient}"/>
			<input type="hidden" name="p24_adres" value="{$p24_adres}"/>
			<input type="hidden" name="p24_kod" value="{$p24_kod}"/>
			<input type="hidden" name="p24_miasto" value="{$p24_miasto}"/>
			<input type="hidden" name="p24_kraj" value="{$p24_kraj}"/>
			<input type="hidden" name="p24_email" value="{$p24_email}"/>
			<input type="hidden" name="p24_language" value="{$p24_language}"/>
			{if $p24_metoda != ''}
				<input type="hidden" name="p24_metoda" value="{$p24_metoda}"/>
			{/if}
			<input type="hidden" name="p24_return_url_ok" value="{$p24_return_url_ok}"/>
			<input type="hidden" name="p24_return_url_error" value="{$p24_return_url_error}"/>

			<p style="color: red;" id="ajaxOutput"></p>
		</form>
	{/if}
</div>

<p class="cart_navigation">
	{if isset($productsNumber) && $productsNumber <= 0}
		<a href="{$base_dir_ssl}index.php" class="button_large">
			<i class="icon-chevron-left"></i>{l s='Return to shop' mod='przelewy24'}
		</a>
	{else}
		<a href="{$base_dir_ssl}index.php?controller=order?step=3"
		   class="{if $ps_version >= 1.6 }button-exclusive btn btn-default{else}button_large{/if}">
			<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='przelewy24'}
		</a>
		<a class="exclusive_large" href="javascript:proceedPayment()">
			<span id="proceedPaymentLink">
				{l s='Confirm' mod='przelewy24'}&nbsp;<img src="{$modules_dir}przelewy24/img/ajax.gif" style="display: none; width: 10px; height: 10px;"/>
			</span>
		</a>
	{/if}
</p>