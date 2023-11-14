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
    self.addClassesInput = $('.js-add-classes-input', element);
    self.defaultClassesInfo = $('.js-default-classes-info', element);
    self.externalClassesInfo = $('.js-external-classes-info', element);
    self.customClassesInfo = $('.js-custom-classes-info', element);
    self.customClassesSection = $('.js-custom-classes-section', element);

    self.binds();
  }

  Lsb.Settings.prototype.binds = function() {
    var self = this;

    self.resetCacheButton.on('click', function(e) {
      e.preventDefault();
      self.resetCache();
    });

    self.listElements.on('click', function(e) {
      e.preventDefault();
      var listId = $(this).data('listId');
      self.showShortcodeExample(listId);
    });

    self.classTypeInput.on('change', function(e) {
      self.showHideSections();
    });

    self.addClassesInput.on('change', function(e) {
      self.showHideSections();
    });
  }

  Lsb.Settings.prototype.showHideSections = function() {
    var self = this;
    var classType = self.classTypeInput.filter(':checked').val();
    var addClass = self.addClassesInput.filter(':checked').val();

    self.defaultClassesInfo.hide();
    self.externalClassesInfo.hide();
    self.customClassesInfo.hide();
    self.customClassesSection.hide();

    if (classType === 'default') {
      self.defaultClassesInfo.show();
    }

    if (classType === 'bootstrap_v4' || classType === 'bootstrap_v5') {
      self.externalClassesInfo.show();
    }

    if (classType === 'custom') {
      self.customClassesInfo.show();
    }

    if (addClass === '1') {
      self.customClassesSection.show();
    }
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