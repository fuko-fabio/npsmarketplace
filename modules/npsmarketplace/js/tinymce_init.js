tinymce.init({
    selector : "textarea.tinymce",
    plugins : "preview spellchecker textcolor",
    toolbar : " bold italic | forecolor backcolor | spellchecker | preview",
    insertdatetime_formats : ["%Y.%m.%d", "%H:%M"],
    menubar : false,
    statusbar : false,
    skin : 'nps',
    language : 'pl'
}); 