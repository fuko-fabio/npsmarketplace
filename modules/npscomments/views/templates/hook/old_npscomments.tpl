<div class="tab-pane fade" id="seller_comments_tab">
    <script type="text/javascript">
        var npscomments_controller_url = '{$npscomments_controller_url}';
        var npscomments_confirm_report_message = '{l s='Are you sure that you want to report this comment?' mod='npscomments' js=1}';
        var npscomments_secure_key = '{$npscomments_secure_key}';
        var npscomments_added = '{l s='Your comment has been added!' mod='npscomments' js=1}';
        var npscomments_added_moderation = '{l s='Your comment has been submitted and will be available once approved by a moderator.' mod='npscomments' js=1}';
        var npscomments_title = '{l s='New comment' mod='npscomments' js=1}';
        var npscomments_ok = '{l s='OK' mod='npscomments' js=1}';
        var npscomments_moderation_active = {$npscomments_moderation_active};
    </script>
    <div id="seller_comments_block_tab">
        {if $npscomments}
        {foreach from=$npscomments item=comment}
        {if $comment.content}
        <div class="comment clearfix">
            <div class="comment_author">
                <span>{l s='Grade' mod='npscomments'}&nbsp</span>
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
                <ul>
                    {if $comment.total_advice > 0}
                    <li>
                        {l s='%1$d out of %2$d people found this review useful.' sprintf=[$comment.total_useful,$comment.total_advice] mod='npscomments'}
                    </li>
                    {/if}
                    {if $npscomments_logged}
                    {if !$comment.customer_advice}
                    <li>
                        {l s='Was this comment useful to you?' mod='npscomments'}
                        <button class="nps_usefulness_btn" data-is-usefull="1" data-id-seller-comment="{$comment.id_seller_comment}">
                            {l s='yes' mod='npscomments'}
                        </button>
                        <button class="nps_usefulness_btn" data-is-usefull="0" data-id-seller-comment="{$comment.id_seller_comment}">
                            {l s='no' mod='npscomments'}
                        </button>
                    </li>
                    {/if}
                    {if !$comment.customer_report}
                    <li>
                        <span class="nps_report_btn" data-id-seller-comment="{$comment.id_seller_comment}">{l s='Report abuse' mod='npscomments'}</span>
                    </li>
                    {/if}
                    {/if}
                </ul>
            </div>
        </div>
        {/if}
        {/foreach}
        {if (!$npscomments_too_early AND ($npscomments_logged OR $npscomments_allow_guests))}
        <p class="align_center">
            <a id="new_comment_tab_btn" class="open-seller-comment-form" href="#new_seller_comment_form">{l s='Write your review' mod='npscomments'} !</a>
        </p>
        {/if}
        {else}
        {if (!$npscomments_too_early AND ($npscomments_logged OR $npscomments_allow_guests))}
        <p class="align_center">
            <a id="new_comment_tab_btn" class="open-seller-comment-form" href="#new_seller_comment_form">{l s='Be the first to write your review' mod='npscomments'} !</a>
        </p>
        {else}
        <p class="align_center">
            {l s='No customer reviews for the moment.' mod='npscomments'}
        </p>
        {/if}
        {/if}
    </div>
    
    {if isset($seller) && $seller}
    <!-- Fancybox -->
    <div style="display:none">
        <div id="new_seller_comment_form">
            <form id="id_new_seller_comment_form" action="#">
                <h2 class="title">{l s='Write your review' mod='npscomments'}</h2>
                {if isset($seller) && $seller}
                <div class="seller clearfix">
                    <img src="{$npscomments_cover_image}" height="{$npscomments_mediumSize.height}" width="{$npscomments_mediumSize.width}" alt="{$seller->company_name[{$current_id_lang}]|escape:html:'UTF-8'}" />
                    <div class="seller_desc">
                        <p class="seller_name">
                            <strong>{$seller->company_name[{$current_id_lang}]}</strong>
                        </p>
                        {$seller->company_description[{$current_id_lang}]}
                    </div>
                </div>
                {/if}
                <div class="new_seller_comment_form_content">
                    <h2>{l s='Write your review' mod='npscomments'}</h2>
                    <div id="new_seller_comment_form_error" class="error" style="display:none;padding:15px 25px">
                        <ul></ul>
                    </div>
                    {if $npscomments_criterions|@count > 0}
                    <ul id="npscomments_criterions_list">
                        {foreach from=$npscomments_criterions item='criterion'}
                        <li>
                            <label>{$criterion.name|escape:'html':'UTF-8'}</label>
                            <div class="star_content">
                                <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="1" />
                                <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="2" />
                                <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="3" />
                                <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="4" />
                                <input class="seller-star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="5" checked="checked" />
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        {/foreach}
                    </ul>
                    {/if}
                    <label for="comment_title">{l s='Title for your review' mod='npscomments'}<sup class="required">*</sup></label>
                    <input id="comment_title" name="title" type="text" value=""/>
    
                    <label for="content">{l s='Your review' mod='npscomments'}<sup class="required">*</sup></label>
                    <textarea id="content" name="content"></textarea>
                    {if $npscomments_allow_guests == true && !$npscomments_logged}
                    <label>{l s='Your name' mod='npscomments'}<sup class="required">*</sup></label>
                    <input id="commentCustomerName" name="customer_name" type="text" value=""/>
                    {/if}
    
                    <div id="new_seller_comment_form_footer">
                        <input id="id_seller_comment_send" name="id_seller" type="hidden" value='{$id_npscomments_form}' />
                        <p class="fl required">
                            <sup>*</sup> {l s='Required fields' mod='npscomments'}
                        </p>
                        <p class="fr">
                            <button id="submitSellerNewMessage" name="submitSellerMessage" type="submit">
                                {l s='Send' mod='npscomments'}
                            </button>
                            &nbsp;
                            {l s='or' mod='npscomments'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='npscomments'}</a>
                        </p>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </form><!-- /end new_seller_comment_form_content -->
        </div>
    </div>
    <!-- End fancybox -->
    {/if}
</div>
