<div>
  <div style="line-height: 1pt">
    &nbsp;
  </div>

  <table style="width: 100%">
    <tr><td style="width: 100%"><b>{$name}</b></td></tr>
    {if isset($date) && $date != '0' && $date != '0000-00-00 00:00:00'}
    <tr><td style="width: 100%">{date_format(date_create($date), 'Y-m-d H:i')}</td></tr>
    {/if}
  </table>

  <div style="line-height: 1pt">
    &nbsp;
  </div>

  <table style="width: 100%; font-size: 8pt; ">
    <tr style="background-color: #4D4D4D; color: #FFF; line-height:6px;">
      <td style="text-align: left; font-weight: bold; width: 20%">{l s='Person' mod='npsticketdelivery'}</td>
      <td style="text-align: left; font-weight: bold; width: 10%">{l s='Type' mod='npsticketdelivery'}</td>
      <td style="text-align: left; font-weight: bold; width: 10%">{l s='Price' mod='npsticketdelivery'}</td>
      <td style="text-align: left; font-weight: bold; width: 18%">{l s='Ticket nr' mod='npsticketdelivery'}</td>
      <td style="text-align: left; font-weight: bold; width: 17%">{l s='Date' mod='npsticketdelivery'}</td>
      <td style="text-align: left; font-weight: bold; width: 20%">{l s='Email(buyer)' mod='npsticketdelivery'}</td>
      <td style="text-align: left; font-weight: bold; width: 5%">{l s='Notes' mod='npsticketdelivery'}</td>
    </tr>
    {foreach $participants as $participant}
    {cycle values='#FFF,#DDD' assign=bgcolor}
    <tr style="line-height:8px;background-color:{$bgcolor};">
      <td style="text-align: left; width: 20%">{$participant.person|escape:'html':'UTF-8'}</td>
      <td style="text-align: left; width: 10%">{$participant.combination_name|escape:'html':'UTF-8'}</td>
      <td style="text-align: left; width: 10%">{round($participant.price, 2)} {$currency->iso_code}</td>
      <td style="text-align: left; width: 18%">{$participant.code}</td>
      <td style="text-align: left; width: 17%">
          {if $participant.date == '0000-00-00 00:00:00'}
          {l s='Carnet' mod='npsticketdelivery'}
          {else}
          {date_format(date_create($participant.date), 'Y-m-d H:i')}
          {/if}
      </td>
      <td style="text-align: left; width: 20%">{$participant.email|escape:'html':'UTF-8'}</td>
      <td style="text-align: left; width: 5%"></td>
    </tr>
    {if isset($participant.questions) && !empty($participant.questions)}
    <tr style="line-height:4px;background-color:{$bgcolor};">
        <td style="text-align: left; width: 100%">{foreach from=$participant.questions item=q}<span style="font-size: 7pt">{$q.question|escape:'html':'UTF-8'}</span><br />{$q.answer|escape:'html':'UTF-8'}<br />{/foreach}</td>
    </tr>
    {/if}
    {/foreach}
  </table>

  {if isset($HOOK_DISPLAY_PDF)}
  <div style="line-height: 1pt">
    &nbsp;
  </div>
  <table style="width: 100%">
    <tr>
      <td style="width: 100%">{$HOOK_DISPLAY_PDF}</td>
    </tr>
  </table>
  {/if}

</div>
