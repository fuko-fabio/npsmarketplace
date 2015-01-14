{foreach $list as $item}
    <table class="table" style="width:100%">
      <tr>
        <td>
          <font size="2" face="Open-sans, sans-serif" color="#555454">
            <p data-html-only="1" style="border-bottom:1px solid #ffaa00;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:14px;padding-bottom:10px">
              {l s='Seller' mod='npsticketdelivery'}: {$item.seller->name}
            </p>
          </font>
        </td>
      </tr>
    </table>

    <table class="table" style="width:100%">
      <tr>
        <td style="padding:7px 0">
          <font size="2" face="Open-sans, sans-serif" color="#555454">
            <span data-html-only="1" style="color:#777"> {$item.address_html}</span><br />
            <span style="color:#777">
                {$item.customer->email}
                <br />
                {if isset($item.seller->nip) && !empty($item.seller->nip)}
                  <strong>{l s='NIP' mod='npsticketdelivery'}:</span> {$item.seller->nip}
                  <br />
                {/if}
                {if isset($item.seller->regon) && !empty($item.seller->regon)}
                  <strong>{l s='REGON' mod='npsticketdelivery'}:</strong> {$item.seller->regon}
                  <br />
                {/if}
                {if isset($item.seller->krs) && !empty($item.seller->krs)}
                  <strong>{l s='KRS' mod='npsticketdelivery'}:</strong> {$item.seller->krs}
                  <br />
                {/if}
                {if isset($item.seller->krs_reg) && !empty($item.seller->krs_reg)}
                  <strong>{l s='KRS registered by' mod='npsticketdelivery'}:</strong> {$item.seller->krs_reg}
                  <br />
                {/if}
            </span>
            <br />
          </font>
        </td>
      </tr>
    </table>
    
    <table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse">
      <!-- Title -->
      <thead>
        <tr>
          <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">{l s='Index' mod='npsticketdelivery'}</th>
          <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">{l s='Event' mod='npsticketdelivery'}</th>
          <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">{l s='Price' mod='npsticketdelivery'}</th>
          <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">{l s='Qty' mod='npsticketdelivery'}</th>
          <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">{l s='Total price' mod='npsticketdelivery'}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="5" style="border:1px solid #D6D4D4;color:#777;padding:7px 0"> &nbsp;&nbsp;{$item.products_html} </td>
        </tr>
      </tbody>
    </table>
    <br />
    <br />
{/foreach}
