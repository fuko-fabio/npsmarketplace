<script type="text/javascript">
	$(document).ready(function() {
		$('<p id="left_payment_parts"  ><a target="_blank" style="text-align:right; font-size:large;" id="fensybox_link" class="iframe" href="https://secure.przelewy24.pl/kalkulator_raty/index.html?ammount={$product_ammount}"> {$part_count} rat x ~{$part_cost} zł </a></p>').insertAfter('#our_price_display');
		$("a#fensybox_link").fancybox({
			'width'	 	: 	680,
			'height'    :   520
		});
	});
</script>