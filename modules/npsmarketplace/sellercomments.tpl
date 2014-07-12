<script type="text/javascript">
var sellercomments_controller_url = '{$sellercomments_controller_url}';
var confirm_report_message = '{l s='Are you sure that you want to report this comment?' mod='sellercomments' js=1}';
var secure_key = '{$secure_key}';
var sellercomments_url_rewrite = '{$sellercomments_url_rewriting_activated}';
var sellercomment_added = '{l s='Your comment has been added!' mod='sellercomments' js=1}';
var sellercomment_added_moderation = '{l s='Your comment has been submitted and will be available once approved by a moderator.' mod='sellercomments' js=1}';
var sellercomment_title = '{l s='New comment' mod='sellercomments' js=1}';
var sellercomment_ok = '{l s='OK' mod='sellercomments' js=1}';
var moderation_active = {$moderation_active};
</script>

<div id="seller_comments_tab">
	<div id="seller_comments_block_tab">
	{if $comments}
		{foreach from=$comments item=comment}
			{if $comment.content}
			<div class="comment clearfix">
				<div class="comment_author">
					<span>{l s='Grade' mod='sellercomments'}&nbsp</span>
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
						<strong>{$comment.customer_name|escape:'html':'UTF-8'}</strong><br/>
						<em>{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</em>
					</div>
				</div>
				<div class="comment_details">
					<h4 class="title_block">{$comment.title}</h4>
					<p>{$comment.content|escape:'html':'UTF-8'|nl2br}</p>
					<ul>
						{if $comment.total_advice > 0}
							<li>{l s='%1$d out of %2$d people found this review useful.' sprintf=[$comment.total_useful,$comment.total_advice] mod='sellercomments'}</li>
						{/if}
						{if $logged}
							{if !$comment.customer_advice}
							<li>{l s='Was this comment useful to you?' mod='sellercomments'}<button class="usefulness_btn" data-is-usefull="1" data-id-seller-comment="{$comment.id_seller_comment}">{l s='yes' mod='sellercomments'}</button><button class="usefulness_btn" data-is-usefull="0" data-id-seller-comment="{$comment.id_seller_comment}">{l s='no' mod='sellercomments'}</button></li>
							{/if}
							{if !$comment.customer_report}
							<li><span class="report_btn" data-id-seller-comment="{$comment.id_seller_comment}">{l s='Report abuse' mod='sellercomments'}</span></li>
							{/if}
						{/if}
					</ul>
				</div>
			</div>
			{/if}
		{/foreach}
        {if (!$too_early AND ($logged OR $allow_guests))}
		<p class="align_center">
			<a id="new_comment_tab_btn" class="open-comment-form" href="#new_seller_comment_form">{l s='Write your review' mod='sellercomments'} !</a>
		</p>
        {/if}
	{else}
		{if (!$too_early AND ($logged OR $allow_guests))}
		<p class="align_center">
			<a id="new_comment_tab_btn" class="open-comment-form" href="#new_seller_comment_form">{l s='Be the first to write your review' mod='sellercomments'} !</a>
		</p>
		{else}
		<p class="align_center">{l s='No customer reviews for the moment.' mod='sellercomments'}</p>
		{/if}
	{/if}	
	</div>
</div>

{if isset($seller) && $seller}
<!-- Fancybox -->
<div style="display:none">
	<div id="new_seller_comment_form">
		<form id="id_new_seller_comment_form" action="#">
			<h2 class="title">{l s='Write your review' mod='sellercomments'}</h2>
			{if isset($seller) && $seller}
			<div class="seller clearfix">
				<img src="{$sellercomment_cover_image}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$seller->name|escape:html:'UTF-8'}" />
				<div class="seller_desc">
					<p class="seller_name"><strong>{$seller->name}</strong></p>
					{$seller->description_short}
				</div>
			</div>
			{/if}
			<div class="new_seller_comment_form_content">
				<h2>{l s='Write your review' mod='sellercomments'}</h2>
				<div id="new_seller_comment_form_error" class="error" style="display:none;padding:15px 25px">
					<ul></ul>
				</div>
				{if $criterions|@count > 0}
					<ul id="criterions_list">
					{foreach from=$criterions item='criterion'}
						<li>
							<label>{$criterion.name|escape:'html':'UTF-8'}</label>
							<div class="star_content">
								<input class="star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="1" />
								<input class="star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="2" />
								<input class="star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="3" />
								<input class="star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="4" />
								<input class="star" type="radio" name="criterion[{$criterion.id_seller_comment_criterion|round}]" value="5" checked="checked" />
							</div>
							<div class="clearfix"></div>
						</li>
					{/foreach}
					</ul>
				{/if}
				<label for="comment_title">{l s='Title for your review' mod='sellercomments'}<sup class="required">*</sup></label>
				<input id="comment_title" name="title" type="text" value=""/>

				<label for="content">{l s='Your review' mod='sellercomments'}<sup class="required">*</sup></label>
				<textarea id="content" name="content"></textarea>

				{if $allow_guests == true && !$logged}
				<label>{l s='Your name' mod='sellercomments'}<sup class="required">*</sup></label>
				<input id="commentCustomerName" name="customer_name" type="text" value=""/>
				{/if}

				<div id="new_seller_comment_form_footer">
					<input id="id_seller_comment_send" name="id_seller" type="hidden" value='{$id_seller_comment_form}' />
					<p class="fl required"><sup>*</sup> {l s='Required fields' mod='sellercomments'}</p>
					<p class="fr">
						<button id="submitNewMessage" name="submitMessage" type="submit">{l s='Send' mod='sellercomments'}</button>&nbsp;
						{l s='or' mod='sellercomments'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='sellercomments'}</a>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</form><!-- /end new_seller_comment_form_content -->
	</div>
</div>
<!-- End fancybox -->
{/if}
