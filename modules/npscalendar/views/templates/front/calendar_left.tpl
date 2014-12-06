{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{literal}
<script>
    $(document).ready(function(){

    $("#accordion div.panel:first").addClass('active');

    $("#accordion").delegate('.panel', 'click', function(e){
        console.log('Clicked');
        if( $(this).hasClass('active') ){
            $(this).animate({width: "44px"}, 500);
            $(this).removeClass('active');
         } else {
            $(this).animate({width: "848px"}, 500);
            $(this).addClass('active');
         }
    });
});
</script>
{/literal}
<div class="nps-events-calendar-big block">
    <h4 class="title_block">{l s='Calendar' mod='npscalendar'}</h4>
    <div class="block_content">
        <div id="accordion">
            <div class="panel">
              <div class="pink"></div>
              <div class="panelContent p1"> <strong>Section 1 Header</strong><br/>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. In iaculis volutpat quam, non suscipit arcu accumsan at. Aliquam pellentesque.
              </div>
        </div>
        
        
        <button>>>></button>
        <div class="content row seven-col">
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                W
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                C
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                N
            </div>
        </div>
        <div class="content row seven-col">
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                W
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                C
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                N
            </div>
        </div>
        <div class="content row seven-col">
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                W
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                C
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                N
            </div>
        </div>
        <div class="content row seven-col">
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                W
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                C
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                N
            </div>
        </div>
        <div class="content row seven-col">
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                W
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                C
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                P
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                S
            </div>
            <div class="col-xs-12 col-sm-1 col-md-1">
                N
            </div>
        </div>
        
    </div>
</div>