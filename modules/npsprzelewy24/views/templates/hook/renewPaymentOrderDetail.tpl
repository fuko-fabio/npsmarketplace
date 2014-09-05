<!-- Created by michalz on 25.03.14 -->
<div class="box" style="overflow: auto;">
	<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/></a>&nbsp;{l s='Pay with Przelewy24' mod='przelewy24'}</h2>
	<p>{l s='Your payment was not confirmed by Przelewy24. If you want to retry press button "Retry".' mod='przelewy24'}</p>
	<p class="cart_navigation">
		<a class="exclusive_large" href="{$p24_retryPaymentUrl}">
			<span id="proceedPaymentLink">
				{l s='Retry' mod='przelewy24'}
			</span>
		</a>
	</p>
</div>