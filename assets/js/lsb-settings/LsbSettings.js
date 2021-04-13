var Lsb = Lsb || {};
(function($) {
  Lsb.Settings = function(element) {
    var self = this;
    self.element = element;
    self.resetCacheUrl = element.data('resetCacheUrl');
    self.resetCacheButton = $('.js-reset-cache', element);
    self.resetCacheResult = $('.js-reset-result', element);
    self.listElements = $('.js-list', element);
    self.shortcodeExample = $('.js-shortcode-example', element);
    self.shortcodeExampleListId = $('.js-shortcode-example-list-id', self.shortcodeExample);
    self.classTypeInput = $('.js-class-type-input', element);
    self.customClassesSection = $('.js-custom-classes-section', element);
    self.externalClassesSection = $('.js-external-classes-section', element);

    self.binds();
  }

  Lsb.Settings.prototype.binds = function() {
    var self = this;

    self.resetCacheButton.on('click', function(e) {
      e.preventDefault();
      self.resetCache();
    })

    self.listElements.on('click', function(e) {
      e.preventDefault();
      var listId = $(this).data('listId');
      self.showShortcodeExample(listId);
    })

    self.classTypeInput.on('change', function(e) {
      if ($(this).val() === 'custom') {
        self.customClassesSection.show();
      } else {
        self.customClassesSection.hide();
      }
      if ($(this).val() === 'bootstrap_v4' || $(this).val() === 'bootstrap_v5') {
        self.externalClassesSection.show();
      } else {
        self.externalClassesSection.hide();
      }
    })
  }

  Lsb.Settings.prototype.resetCache = function() {
    var self = this;

    $.ajax({
      url: self.resetCacheUrl,
      type: 'get',
      dataType: 'json',
      success: function(response) {
        self.resetCacheResult.text('De cache is geleegd')
      },
      error: function(response) {
        self.resetCacheResult.text('Er ging iets mis')
      }
    });
  }

  Lsb.Settings.prototype.showShortcodeExample = function(listId) {
    var self = this;

    self.shortcodeExampleListId.text(listId);
    self.shortcodeExample.show();
  }
})(jQuery);