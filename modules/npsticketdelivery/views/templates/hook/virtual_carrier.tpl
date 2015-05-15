{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div class="row">
    <div class="form-group col-md-6">
        <label for="product_code">{l s='Delivery email address' mod='npsticketdelivery'}</label>
        <input class="is_required validate form-control" data-validate="isEmail" type="text" name="ticket_destination" value="{if isset($ticket_destination)}{$ticket_destination}{else}{$cookie->email}{/if}"/>
        <span class="form_info">{l s='You can send the tickets to a different address. Suitable for gifts. Tickets also will be sent to your e-mail address.' mod='npsticketdelivery'} <a target="_blank" href="{$send_tickets_info_url}">{l s='See more informations' mod='npsticketdelivery'}</a></span>
    </div>
</div>
<label>{l s='Tickets personal data and questions' mod='npsticketdelivery'}</label>

<div class="tickets-container">
    {foreach $cart->getProducts() as $product}
        {for $x=1 to $product.quantity}
            <div class="item">
              <span class="name">{$product.name}{if isset($product.attributes)} <span class="attributes">({$product.attributes})</span>{/if}</span>
              <br />
              <div class="row">
                  <div class="form-group col-md-6">
                      <label class="required">{l s='Person' mod='npsticketdelivery'}</label>
                      <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="ticket_person[{$product.id_product}][{$x}]" value="{if isset($ticket_person[{$product.id_product}][{$x}])}{$ticket_person[{$product.id_product}][{$x}]}{else}{$cookie->customer_firstname} {$cookie->customer_lastname}{/if}"/>
                      <span class="form_info">{l s='Visible on ticket.' mod='npsticketdelivery'} <a class="link" target="_blank" href="{$send_tickets_info_url}">{l s='See more informations' mod='npsticketdelivery'}</a></span>
                  </div>
              </div>
              {if isset($ticket_questions[$product.id_product]) && !empty($ticket_questions[$product.id_product])}
                <label>{l s='Questions from seller' mod='npsticketdelivery'}</label>
                <div class="questions-container">
                  {foreach $ticket_questions[$product.id_product] as $key => $question}
                  <div class="form-group">
                      <label {if $question.required}class="required"{/if}>{$question.question}</label>
                      <input class="{if $question.required}is_required{/if} validate form-control" data-validate="isMessage" type="text" name="ticket_question[{$product.id_product}][{$x}][{$question.id_question}]"
                        value="{if isset($ticket_question[{$product.id_product}][{$x}][{$question.id_question}])}{$ticket_question[{$product.id_product}][{$x}][{$question.id_question}]}{/if}"/>
                  </div>
                  {/foreach}
                  </div>
              {/if}
            </div>
        {/for}
    {/foreach}
</div>
