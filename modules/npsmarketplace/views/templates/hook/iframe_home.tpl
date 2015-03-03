{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

<!doctype html>
<html lang="{$lang}">
  <head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    <meta name="author" content="LabsInTown">
    <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Dosis' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Calligraffitti' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
    {foreach from=$css_urls item=css}
    <link rel="stylesheet" href="{$css}">
    {/foreach}
    {foreach from=$js_urls item=js}
    <script type="text/javascript" src="{$js}"></script>
    {/foreach}
    {$HOOK_IFRAME_HOME_HEADER}
    <base target="_blank" />
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body id="index" class="index hide-left-column hide-right-column">
    <div id="page">
        <div class="columns-container">
            <div id="columns" class="container">
                <div class="row">
                    <div id="top_column" class="center_column col-xs-12">
                        {$HOOK_IFRAME_HOME}
                    </div>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>
