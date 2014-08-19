<!-- Created by michalz on 13.03.14 -->

{capture name=path}{l s='Pay with Przelewy24' mod='przelewy24'}{/capture}

{if $p24_status == 'success'}
	<div class="box" style="overflow: auto;">
		<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/></a>&nbsp;{l s='Congratulation!' mod='przelewy24'}</h2>
		<p>{l s='Thank you for your purchase. Your payment was confirmed by Przelewy24. You can track your order in history of orders.' mod='przelewy24'}</p>
		<p class="cart_navigation">
			<a href="{$base_dir_ssl}index.php" class="button_large">
				<i class="icon-chevron-left"></i>{l s='Return to shop' mod='przelewy24'}
			</a>
			<a class="exclusive_large" href="{$base_url_ssl}index.php?controller=history">
				{l s="Show order history" mod="przelewy24"}
			</a>
		</p>
	</div>
{else}
	<div class="box" style="overflow: auto;">
		<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/></a>&nbsp;{l s='Payment failed!' mod='przelewy24'}</h2>
		<p>{l s='Your payment was not confirmed by Przelewy24. Contact with your seller for more information.' mod='przelewy24'}</p>
		<p class="cart_navigation">
			<a href="{$base_dir_ssl}index.php" class="button_large">
				<i class="icon-chevron-left"></i>{l s='Return to shop' mod='przelewy24'}
			</a>
			<a class="exclusive_large" href="{$base_url_ssl}index.php?controller=history">
				{l s="Show order history" mod="przelewy24"}
			</a>
		</p>
	</div>
{/if}
