{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<script type="text/javascript">
    function gotoCombinations() {
        $(jQuery.browser.webkit ? "body": "html").animate({ scrollTop: $('.tabs-container').offset().top }, 1000);
        $('.combinations-tab').click();
    }
</script>

<li class="goto-combinations"><a href="javascript:void(0);" class="btn btn-default button button-small" onclick="gotoCombinations();"><i class="icon-ticket"></i> {l s='Other tickets' mod='npscombinations'}</a></li>
