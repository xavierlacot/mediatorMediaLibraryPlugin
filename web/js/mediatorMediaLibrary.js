/**
 * Facebox media choice window
 */
$(function(){
  $('a[rel*=facebox]').live('click', function() {
    $(this).facebox();
    return false;
  });
});