<div id="seller_regulations_tab">
<textarea class="form-control" readonly="" >{$regulations[$current_id_lang]}</textarea>
<script>
    $(function() {
        $('textarea').each(function() {
            $(this).height($(this).prop('scrollHeight'));
        });
    })
</script>
</div>