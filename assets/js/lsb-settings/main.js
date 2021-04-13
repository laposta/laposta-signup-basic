(function($) {
  var lsbSettings = $('.lsb-settings');
  if (lsbSettings.length) {
    new Lsb.Settings(lsbSettings);
  }
})(jQuery);