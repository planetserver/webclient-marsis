/*jQuery.ajaxSetup({
  beforeSend: function() {
     $('#loader').show();
  },
  complete: function(){
     $('#loader').hide();
  },
  success: function() {}
});*/
function showLoader() {
    $("#toc").css(
    {
        'display': 'none'
    });
    $("#spectra").css(
    {
        'display': 'none'
    });
    $("#query").css(
    {
        'display': 'none'
    });
}
function hideLoader() {
	$("#toc").css(
    {
        'display': ''
    });
    $("#spectra").css(
    {
        'display': ''
    });
    $("#query").css(
    {
        'display': ''
    });
}
function initloader()
    {
    $('#loader')
        .hide()  // hide it initially
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();
    });
    }