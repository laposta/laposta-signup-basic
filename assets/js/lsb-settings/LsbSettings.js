var Lsb = Lsb || {};
(function($) {
  Lsb.Settings = function(element) {
    var self = this;
    self.element = element;
    self.resetCacheUrl = element.data('resetCacheUrl');
    self.resetCacheButton = $('.js-reset-cache', element);
    self.resetCacheResultSuccess = $('.js-reset-result-success', element);
    self.resetCacheResultError = $('.js-reset-result-error', element);
    self.listElements = $('.js-list', element);
    self.shortcodeExampleWrapper = $('.js-shortcode-example-wrapper', element);
    self.shortcodeExample = $('.js-shortcode-example', element);
    self.shortcodeExampleListId = $('.js-shortcode-example-list-id', self.shortcodeExampleWrapper);
    self.shortcodeCopyButton = $('.js-copy-shortcode', element);
    self.shortcodeCopyButtonText = $('.js-copy-shortcode-text', self.shortcodeCopyButton);
    self.shortcodeCopyButtonSuccess = $('.js-copy-shortcode-success', self.shortcodeCopyButton);
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
      if (window.navigator && window.navigator.clipboard) {
        self.shortcodeCopyButton.show()
      }
    });

    self.shortcodeCopyButton.on('click', function(e) {
      e.preventDefault();
      self.copyShortcode();
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
        self.resetCacheResultSuccess.show();
        setTimeout(function() {
          self.resetCacheResultSuccess.hide();
        }, 3000)
      },
      error: function(response) {
        self.resetCacheResultError.show();
        setTimeout(function() {
          self.resetCacheResultError.hide();
        }, 3000)
      }
    });
  }

  Lsb.Settings.prototype.showShortcodeExample = function(listId) {
    var self = this;

    self.shortcodeExampleListId.text(listId);
    self.shortcodeExampleWrapper.show();
  }

  Lsb.Settings.prototype.copyShortcode = function() {
    var self = this;

    var text = self.shortcodeExample.text().trim();
    navigator.clipboard.writeText(text)
      .then(function() {
        self.shortcodeCopyButtonText.hide();
        self.shortcodeCopyButtonSuccess.show();
        setTimeout(function() {
          self.shortcodeCopyButtonText.show();
          self.shortcodeCopyButtonSuccess.hide();
        }, 1000)
      })
      .catch(function(err) {
        console.error('Copy to clipboard failed', err);
      });
  }

})(jQuery);