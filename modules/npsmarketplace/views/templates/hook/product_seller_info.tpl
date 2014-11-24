{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{addJsDefL name=npsAjaxUrl}{$nps_ajax_url}{/addJsDefL}
<script type="text/javascript">
{literal}
$('document').ready(function(){
    $('#send_seller_button').fancybox({
        'hideOnContentClick': false
    });
    $('#send_seller_form_error').hide();

    $('#sendQuestionEmail').click(function(){
        $('#send_seller_form_error').hide();
        var event_question = $('#event_question').val();
        var email = $('#customer_email').val();
        var id_product = '{/literal}{$sts_product_id}{literal}';
        if (event_question && email && !isNaN(id_product))
        {
            $.fancybox.showLoading();
            $.ajax({
                url: npsAjaxUrl,
                type: "POST",
                headers: {"cache-control": "no-cache"},
                data: {action: 'sendToSeller', secure_key: '{/literal}{$sts_secure_key}{literal}', question: event_question, email: email, id_product: id_product},{/literal}{literal}
                dataType: "json",
                success: function(result) {
                    $.fancybox.hideLoading();
                    $.fancybox.close();
                    var msg = result ? "{/literal}{l s='Your e-mail has been sent successfully' mod='npsmarketplace'}{literal}" : "{/literal}{l s='Your e-mail could not be sent. Please check the e-mail address and try again.' mod='npsmarketplace'}{literal}";
                    var title = "{/literal}{l s='Ask seller' mod='npsmarketplace'}{literal}";
                    fancyMsgBox(msg, title);
                },
                error: function () {
                    $.fancybox.hideLoading();
                    $('#send_friend_form_error').show();
                }
            });
        }
        else
            $('#send_seller_form_error').show();
    });
});
{/literal}
</script>
<ul id="seller_info_block_extra">
    <li class="seller-name"><a href="{$seller_shop_url}">{$seller_name}</a><br/></li>
    <li class="seller-ask"><a id="send_seller_button" href="#send_seller_form">{l s='Ask seller' mod='npsmarketplace'}</a></li>
</ul>
<div style="display: none;">
    <div id="send_seller_form">
            <h2 class="title">{l s='Ask seller' mod='npsmarketplace'}</h2>

            <div class="send_seller_form_content" id="send_seller_form_content">
                <p id="send_seller_form_error" class="alert alert-error">
                    {l s='You did not fill required fields' mod='npsmarketplace'}
                </p>

                <div class="form_container">
                    <div class="form-group">
                        <label for="customer_email">{l s='Your e-mail address' mod='npsmarketplace'} <sup class="required">*</sup></label>
                        <input id="customer_email" class="validate form-control" data-validate="isEmail" name="customer_email" type="text" value="{$cookie->email}"/>
                    </div>
                    <div class="form-group">
                        <label for="event_question">{l s='Question' mod='npsmarketplace'} <sup class="required">*</sup></label>
                        <textarea id="event_question" class="validate form-control" data-validate="isMessage"></textarea>
                    </div>
                    <p class="txt_required"><sup class="required">*</sup> {l s='Required fields' mod='npsmarketplace'}</p>
                </div>
                <p class="submit">
                    <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
                    <input id="sendQuestionEmail" class="button" type="button" value="{l s='Send' mod='npsmarketplace'}" />
                </p>
            </div>
    </div>
</div>

