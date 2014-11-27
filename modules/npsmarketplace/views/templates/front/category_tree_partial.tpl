{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<li class="category_{$node.id}{if isset($last) && $last == 'true'} last{/if}">
    <p class="checkbox">
        <input type="checkbox" name="category[]" id="category_{$node.id}" value="{$node.id}" {if (isset($smarty.post.category) && in_array($node.id, $smarty.post.category)) || in_array($node.id, $product['categories'])}checked=""{/if} title="{$node.desc|strip_tags|trim|truncate:255:'...'|escape:'html':'UTF-8'}"/>
        <label for="category_{$node.id}">{$node.name|escape:'html':'UTF-8'}</label>
     </p>
	{if $node.children|@count > 0}
		<ul>
        {foreach from=$node.children item=child name=categories_tree}
                    {if $smarty.foreach.categories_tree.last}
                        {include file="$category_partial_tpl_path" node=$child last='true'}
                    {else}
                        {include file="$category_partial_tpl_path" node=$child}
                    {/if}
                {/foreach}
		</ul>
	{/if}
</li>
