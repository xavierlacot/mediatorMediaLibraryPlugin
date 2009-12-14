/**
 * Facebox media choice window
 */
$(function(){
  $('a[rel*=facebox]').livequery(function() {
    $(this).facebox();
  });
});