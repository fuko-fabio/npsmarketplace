{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*}

<script type="text/x-tmpl" id="question-tmpl">
  <div class="item question-index-{literal}{%=o.index%}{/literal}">
      <a href="javascript: void(0)" class="icon-btn pull-right" title="{l s='Delete'  mod='npsmarketplace'}" onclick="removeQuestion({literal}{%=o.index%}{/literal});"><i class="icon-trash right"></i></a>
      <span class="question">
          {literal}{%=o.question%}{/literal}
      </span>
      
      {l s='Answer required' mod='npsmarketplace'}:
      {literal}{% if (o.required) { %}{/literal}
        <i class="icon-ok">
      {literal}{% } else { %}{/literal}
        <i class="icon-remove">
      {literal}{% } %}{/literal}
      {literal}
      <input type="hidden" name="questions[{%=o.index%}][question]" value="{%=o.question%}" />
      <input type="hidden" name="questions[{%=o.index%}][required]" value="{%=o.required%}" />
      {/literal}
  </div>
</script>

 <div style="display:none">
    <div id="question_box" class="event-question">
        <h2 class="page-subheading">{l s='New question' mod='npsmarketplace'}</h2>
        <form id="question_form">
            <div class="row">
                <div class="required form-group col-md-9">
                    <label for="question_content">{l s='Your question' mod='npsmarketplace'}</label>
                    <input type="text" class="is_required validate form-control" data-validate="isMessage" id="question_content" name="question"/>
                </div>
                <div class="required form-group col-md-3">
                    <label>{l s='Requirements' mod='npsmarketplace'}</label>
                    <div class="checkbox">
                        <label for="required_question">
                            <input type="checkbox" name="required" id="required_question" value="1"/>
                            {l s='Answer is required' mod='npsmarketplace'}</label>
                    </div>
                </div>

            </div>
        </form>

        <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeQuestionBox('#question_form');"/>
            <input class="button" onclick="addQuestion('#question_form');" value="{l s='Add' mod='npsmarketplace'}"/>
        </p>
    </div>
</div>
