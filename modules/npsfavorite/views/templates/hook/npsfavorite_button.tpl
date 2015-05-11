{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{if $isLogged}
<a href="javascript:void(0);" class="nps-favourite-add" value="{l s='Favourite' mod='npsfavourite'}" onclick="addToFavourite({$product.id_product});"><i class="icon-heart"></i></a>
{/if}
