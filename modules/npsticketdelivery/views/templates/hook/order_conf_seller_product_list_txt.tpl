{foreach $list as $item}
{l s='Seller'}: {$item.seller->name}
{$item.address_txt}
{$item.customer->email}
{if isset($item.seller->nip) && !empty($item.seller->nip)}{l s='NIP' mod='npsticketdelivery'}: {$item.seller->nip}{/if}
{if isset($item.seller->regon) && !empty($item.seller->regon)}{l s='REGON' mod='npsticketdelivery'}: {$item.seller->regon}{/if}
{if isset($item.seller->krs) && !empty($item.seller->krs)}{l s='KRS' mod='npsticketdelivery'}: {$item.seller->krs}{/if}
{if isset($item.seller->krs_reg) && !empty($item.seller->krs_reg)}{l s='KRS registered by' mod='npsticketdelivery'}: {$item.seller->krs_reg}{/if}

{l s='INDEX' mod='npsticketdelivery'}
{l s='EVENT' mod='npsticketdelivery'}
{l s='UNIT PRICE' mod='npsticketdelivery'}
{l s='QTY' mod='npsticketdelivery'}
{l s='TOTAL PRICE' mod='npsticketdelivery'}
{$item.products_txt} 
{/foreach}