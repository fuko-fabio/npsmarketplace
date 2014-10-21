{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<script type="text/javascript">
    var generateReportUrl = "{$link->getAdminLink('AdminPdf')}&submitAction=generateSalesReportPDF&id_seller={$id_seller}"

    function generateReport() {
        generateReportUrl += '&start=' + $("[name='start_date']").val();
        generateReportUrl += '&end=' + $("[name='end_date']").val();
        window.open(generateReportUrl, '_blank');
    }
    $(function() {
        $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    });
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
                        <input type="text" name="start_date" class="datepicker" value="{date('Y-m-01', strtotime('-1 month'))}" />
                        <div class="input-group-addon">
                            <i class="icon-calendar-o"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label>{l s='Date to' mod='npsprzelewy24'}</label>
                    <div class="input-group fixed-width-xl">
                        <input type="text" name="end_date" class="datepicker" value="{date('Y-m-t', strtotime('-1 month'))}" />
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
