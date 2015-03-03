{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

<script type="text/javascript">
    var calendarApiUrl = '{$calendar_api_url}';
    var calendarPageUrl = '{$calendar_page_url}';
</script>
{foreach from=$css_urls item=css}
<link rel="stylesheet" href="{$css}">
{/foreach}
{foreach from=$js_urls item=js}
<script type="text/javascript" src="{$js}"></script>
{/foreach}

