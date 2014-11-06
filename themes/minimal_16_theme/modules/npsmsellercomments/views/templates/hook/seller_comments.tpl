<div class="tab-pane fade" id="seller_comments_tab">
    <script type="text/javascript">
        var sellercomments_controller_url = '{$sellercomments_controller_url}';
        var sellercomments_confirm_report_message = '{l s='Are you sure that you want to report this comment?' mod='npsmsellercomments' js=1}';
        var sellercomments_secure_key = '{$sellercomments_secure_key}';
        var sellercomment_added = '{l s='Your comment has been added!' mod='npsmsellercomments' js=1}';
        var sellercomment_added_moderation = '{l s='Your comment has been submitted and will be available once approved by a moderator.' mod='npsmsellercomments' js=1}';
        var sellercomment_title = '{l s='New comment' mod='npsmsellercomments' js=1}';
        var sellercomment_ok = '{l s='OK' mod='npsmsellercomments' js=1}';
        var sellercomments_moderation_active = {$sellercomments_moderation_active};
        var sellercomments_id_seller = '{$seller->id}'
    </script>
    <div id="seller_comments_block_tab">
        {if $sellercomments}
            {foreach from=$sellercomments item=comment}
                {if $comment.content}
                    <div class="comment clearfix">
                        <div class="comment_author">
                            <span>{l s='Grade' mod='npsmsellercomments'}&nbsp</span>

                            <div class="star_content clearfix">
                                {section name="i" start=0 loop=5 step=1}
                                    {if $comment.grade le $smarty.section.i.index}
                                        <div class="star"></div>
                                    {else}
                                        <div class="star star_on"></div>
                                    {/if}
                                {/section}
                            </div>
                            <div class="comment_author_infos">
                                <strong>{$comment.customer_name|escape:'html':'UTF-8'}</strong>
                                <br/>
                                <em>{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</em>
                            </div>
                        </div>
                        <div class="comment_details">
                            <h4 class="title_block">{$comment.title}</h4>

                            <p>
                                {$comment.content|escape:'html':'UTF-8'|nl2br}
                            </p>
                        </div>
                    </div>
                {/if}
            {/foreach}
            {if (!$sellercomments_too_early AND ($sellercomments_logged OR $sellercomments_allow_guests))}
                <a id="new_comment_tab_btn" class="btn btn-default button button-medium" href="#new_seller_comment_form">{l s='Comment' mod='npsmsellercomments'} <i class="icon-plus"></i></a>
            {/if}
        {else}
            {if (!$sellercomments_too_early AND ($sellercomments_logged OR $sellercomments_allow_guests))}
                 <a id="new_comment_tab_btn" class="btn btn-default button button-medium" href="#new_seller_comment_form">{l s='Comment' mod='npsmsellercomments'} <i class="icon-plus"></i></a>
            {else}
                <p class="alert alert-info">
                    {l s='No reviews for the moment.' mod='npsmsellercomments'}
                </p>
            {/if}
        {/if}
    </div>

    {if isset($seller) && $seller}
        <!-- Fancybox -->
        <div style="display:none">
            <div id="new_seller_comment_form">
                <h2 class="page-subheading">
                    {l s='Write your review' mod='npsmsellercomments'}
                </h2>
                <form id="id_new_seller_comment_form" class="row" action="#">
                    <div class="clearfix col-xs-12 col-sm-6 seller">
                            <img src="{$sellercomments_cover_image}" height="{$sellercomments_mediumSize.height}" width="{$sellercomments_mediumSize.width}" alt="{$seller->company_name[{$current_id_lang}]|escape:html:'UTF-8'}"/>
                            <strong>{$seller->company_name}</strong><br />
                            {$seller->company_description[{$current_id_lang}]}
                    </div>
                    <div class="clearfix col-xs-12 col-sm-6 seller">
                        <div id="new_seller_comment_form_error" class="alert alert-error" style="display:none;padding:15px 25px">
                            <ul></ul>
                        </div>
                        {if $sellercomments_criterions|@count > 0}
                            <ul id="sellercomments_criterions_list">
                                {foreach from=$sellercomments_criterions item='criterion'}
                                    <li>
                                        <label>{$criterion.name|escape:'html':'UTF-8'}</label>

                                        <div class="star_content">
                                            <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="1"/>
                                            <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="2"/>
                                            <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="3"/>
                                            <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="4"/>
                                            <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="5" checked="checked"/>
                                        </div>
                                        <div class="clearfix"></div>
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}

                        <div class="form-group">
                            <label for="comment_title">{l s='Title for your review' mod='npsmsellercomments'}<sup class="required">*</sup></label>
                            <input id="comment_title" class="is_required validate form-control" data-validate="isGenericName" name="title" type="text" value=""/>
                        </div>
                        <div class="form-group">
                            <label for="content">{l s='Your review' mod='npsmsellercomments'}
                                <sup class="required">*</sup></label>
                            <textarea id="content" class="is_required validate form-control" data-validate="isMessage" name="content"></textarea>
                        </div>
                        {if $sellercomments_allow_guests == true && !$sellercomments_logged}
                            <div class="form-group">
                                <label>{l s='Your name' mod='npsmsellercomments'}<sup class="required">*</sup></label>
                                <input id="commentCustomerName" name="customer_name" type="text" value=""/>
                            </div>
                        {/if}
                        <p class="txt_required">
                            <sup class="required">*</sup> {l s='Required fields' mod='sendtoafriend'}
                        </p>
                    </div>
                </form>
                <p class="submit">
                    <input class="button ccl" type="button" value="{l s='Cancel' mod='sendtoafriend'}" onclick="$.fancybox.close();"/>
                    <input id="submitSellerNewMessage" class="button" name="submitSellerMessage" type="submit" value="{l s='Send' mod='npsmsellercomments'}"/>
                </p>
            </div>
        </div>
        <!-- End fancybox -->
    {/if}
</div>
