<script type="text/javascript">
    var generateReportUrl = "{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&submitAction=generateSalesReportPDF&id_seller={$id_seller}"

    function generateReport() {
        generateReportUrl = unescape(generateReportUrl);
        generateReportUrl += '&start=' + $("[name='start_date']").val();
        generateReportUrl += '&end=' + $("[name='end_date']").val();
        console.log(generateReportUrl);
        window.open(generateReportUrl, '_blank');
    }
</script>
<div id="container-reports">
    <div class="panel clearfix">
        <div class="panel-heading">
            <i class="icon-gear"></i>
            {l s='Sales report' mod='npsprzelewy24'}
        </div>
        <div class="form-horizontal">
            <div class="row">
                <div class="col-lg-6">
                    <label>{l s='Date from' mod='npsprzelewy24'}</label>
                    <div class="input-group fixed-width-xl">
                        <input type="text" name="start_date" class="datepicker" value="{date('Y-m-d')}" />
                        <div class="input-group-addon">
                            <i class="icon-calendar-o"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label>{l s='Date to' mod='npsprzelewy24'}</label>
                    <div class="input-group fixed-width-xl">
                        <input type="text" name="end_date" class="datepicker" value="{date('Y-m-d')}" />
                        <div class="input-group-addon">
                            <i class="icon-calendar-o"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <button type="button" class="btn btn-primary pull-right" onclick="generateReport();">
                        <i class="icon-file"></i>
                        {l s='Generate' mod='npsprzelewy24'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
