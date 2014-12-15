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
                        </div>
                    </div>
                {/if}
            {/foreach}
            {if (!$npscomments_too_early AND ($npscomments_logged OR $npscomments_allow_guests))}
                <a id="new_comment_tab_btn" class="btn btn-default button button-medium" href="#new_seller_comment_form">{l s='Comment shop' mod='npscomments'} <i class="icon-plus"></i></a>
            {/if}
        {else}
            {if (!$npscomments_too_early AND ($npscomments_logged OR $npscomments_allow_guests))}
                 <a id="new_comment_tab_btn" class="btn btn-default button button-medium" href="#new_seller_comment_form">{l s='Comment shop' mod='npscomments'} <i class="icon-plus"></i></a>
            {else}
                <p class="alert alert-info"><span class="alert-content">
                    {l s='No reviews for the moment.' mod='npscomments'}
                    </span>
                </p>
            {/if}
        {/if}
    </div>

    {if isset($seller) && $seller}
        <!-- Fancybox -->
        <div style="display:none">
            <div id="new_seller_comment_form">
                <h2 class="page-subheading">
                    {l s='Write your review' mod='npscomments'}
                </h2>
                <form id="id_new_seller_comment_form" class="row" action="#">
                    <div class="clearfix col-xs-12 col-sm-6 seller">
                        {if isset($npscomments_cover_image) && !empty($npscomments_cover_image)}
                            <img src="{$npscomments_cover_image}" height="{$npscomments_mediumSize.height}" width="{$npscomments_mediumSize.width}" alt="{$seller->company_name[{$current_id_lang}]|escape:html:'UTF-8'}"/>
                        {else}
                            <img src="{$img_prod_dir}{$lang_iso}-default-medium_default.jpg" height="{$npscomments_mediumSize.height}" width="{$npscomments_mediumSize.width}"/>
                        {/if}
                        <strong>{$seller->company_name}</strong><br />
                        {$seller->company_description[{$current_id_lang}]}
                    </div>
                    <div class="clearfix col-xs-12 col-sm-6 seller">
                        <div id="new_seller_comment_form_error" class="alert alert-error" style="display:none;padding:15px 25px">
                            <ul></ul>
                        </div>
                        {if $npscomments_criterions|@count > 0}
                            <ul id="npscomments_criterions_list">
                                {foreach from=$npscomments_criterions item='criterion'}
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
                            <label for="comment_title">{l s='Title for your review' mod='npscomments'}<sup class="required">*</sup></label>
                            <input id="comment_title" class="is_required validate form-control" data-validate="isGenericName" name="title" type="text" value=""/>
                        </div>
                        <div class="form-group">
                            <label for="content">{l s='Your review' mod='npscomments'}
                                <sup class="required">*</sup></label>
                            <textarea id="content" class="is_required validate form-control" data-validate="isMessage" name="content"></textarea>
                        </div>
                        {if $npscomments_allow_guests == true && !$npscomments_logged}
                            <div class="form-group">
                                <label>{l s='Your name' mod='npscomments'}<sup class="required">*</sup></label>
                                <input id="commentCustomerName" name="customer_name" type="text" value=""/>
                            </div>
                        {/if}
                        <p class="txt_required">
                            <sup class="required">*</sup> {l s='Required fields' mod='npscomments'}
                        </p>
                    </div>
                </form>
                <p class="submit">
                    <input class="button ccl" type="button" value="{l s='Cancel' mod='npscomments'}" onclick="$.fancybox.close();"/>
                    <input id="submitSellerNewMessage" class="button" name="submitSellerMessage" type="submit" value="{l s='Send' mod='npscomments'}"/>
                </p>
            </div>
        </div>
        <!-- End fancybox -->
    {/if}
</div>
