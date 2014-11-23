function statusChangeCallback(response) {
    if (response.status === 'connected') {
        fbAuthRequest(response.authResponse);
    } else if (response.status === 'not_authorized') {
        //$('.npsfacebooklogin').show('slow');
    } else {
        //$('.npsfacebooklogin').show('slow');
    }
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
}

window.fbAsyncInit = function() {
    FB.init({
        appId : npsFbAapId,
        cookie : true, // enable cookies to allow the server to access the session
        xfbml : true, // parse social plugins on this page
        version : 'v2.2' // use version 2.1
    });

    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
};
( function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/" + npsFbLang + "/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

function fbAuthRequest(authResponse) {
    $.fancybox.showLoading();
    FB.api('/me', function(response) {
        $.ajax({
            url : npsFbController,
            type : "POST",
            headers : {
                "cache-control" : "no-cache"
            },
            dataType : "json",
            data: response,
            success: function(result) {
                location.reload();
            }
        });
    });
}
