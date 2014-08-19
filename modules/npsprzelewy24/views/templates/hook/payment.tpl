<!-- Created by michalz on 13.03.14 -->

<p class="payment_module">
	<a title="{l s='Pay with Przelewy24' mod='przelewy24'}" href="{$p24_url_payment}">
		<img src="{$modules_dir}przelewy24/img/logo_small.png"
		     alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/>
		{l s='Pay with Przelewy24' mod='przelewy24'}
	</a>
</p>
{if intval($p24_installment_show) >= 1 }
	<p class="payment_module">
		<a title="{l s='Installment with Przelewy24' mod='przelewy24'}" href="{$p24_url_installment}">
			<img src="{$modules_dir}przelewy24/img/logo_small.png"
			     alt="{l s='Installment with Przelewy24' mod='przelewy24'}"/>
			{l s='Installment with Przelewy24' mod='przelewy24'}
		</a>
	</p>
{/if}