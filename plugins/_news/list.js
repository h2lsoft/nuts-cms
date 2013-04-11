// facebook
function openFacebook(url){
    window.open(url, 'facebook_publish', 'width=780,height=250, top=0, left=0, scrollbars=no');
}

// twitter
function openTwitter(status){
    uri = "http://twitter.com/home?status="+status;
    popupModal(uri, "Twitter", 800, 600);
}

// google+
function openGoogleP(url){

    window.open(url, 'googlep_publish', 'width=780,height=250, top=0, left=0, scrollbars=no');

}