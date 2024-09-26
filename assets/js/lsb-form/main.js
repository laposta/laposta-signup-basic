(function ($) {
  var CLASS_VISUALLY_HIDDEN = 'lsb-visually-hidden';
  var SELECTOR_FORM = '.js-lsb-form';
  var SELECTOR_FIELD_WRAPPER = '.lsb-form-field-wrapper';
  var SELECTOR_INPUT = '.lsb-form-input';
  var SELECTOR_CHECKBOX_INPUT = '.lsb-form-check-input';
  var SELECTOR_FIELD_ERROR = '.lsb-form-field-error-feedback';

  var lsb = {};

  lsb.getElementValue = function($element) {
    if (!$element.length) return null;

    var type = $element.attr('type');
    var tag = $element.prop('tagName').toLowerCase();

    if (tag === 'input') {
      if (type === 'checkbox') {
        return $element.is(':checked');
      } else if (type === 'radio') {
        var $fieldset = $element.closest('fieldset');
        if ($fieldset.length) {
          return $fieldset.find('input[name="' + $element.attr('name') + '"]:checked').val();
        }
        return $element.is(':checked') ? $element.val() : null;
      } else {
        return $element.val();
      }
    } else if (tag === 'select') {
      return $element.prop('multiple') ? $element.val() : $element.find('option:selected').val();
    } else if (tag === 'textarea') {
      return $element.val();
    }
    return null;
  }

  lsb.validateForm = function($form) {
    var isValid = true;
    $form.find(SELECTOR_FIELD_WRAPPER).each(function() {
      var $field = $(this);
      if (!lsb.validateField($field) && isValid) {
        isValid = false;
        var $input = $field.find(SELECTOR_INPUT);
        if (!$input.length) {
          $input = $field.find(SELECTOR_CHECKBOX_INPUT);
        }
        $input.focus();
      }
    });

    return isValid;
  }
  
  lsb.validateField = function($field) {
    var $input = $field.find(SELECTOR_INPUT);
    if (!$input.length) {
      $input = $field.find(SELECTOR_CHECKBOX_INPUT);
    }
    if (!$input.length) {
      console.error('[LSB] no input found for field', $field);
      return false;
    }

    var val = lsb.getElementValue($input);
    if (typeof val === "string") {
      val = val.trim();
    }

    var fieldType = $field.data('fieldType');
    var fieldName = $field.find('.lsb-form-label-name').text();
    var error = null;
    var required = $field.data('required');
    if (!val) {
      if (required) {
        error = lsbConfig.trans['field.error.required.'+fieldType];
        if (error === undefined) {
          error = lsbConfig.trans['field.error.required'].replace('%field_name%', fieldName);
        }
      }
    } else if (fieldType === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
      error = lsbConfig.trans['field.error.invalid.email'];
    } else if (fieldType === 'date') {
      var date = new Date(val);
      // Check of de datum geldig is
      if (isNaN(date.getTime())) {
        error = lsbConfig.trans['field.error.invalid.date'];
      }
    } else if (fieldType === 'number') {
      if (val.indexOf(',') >= 0 && val.indexOf('.') === -1) {
        // assume wrong decimal
        val = val.replace(',', '.');
        $input.val(val); // give direct feedback
      }
      if (isNaN(parseFloat(val)) || !isFinite(val)) {
        error = lsbConfig.trans['field.error.invalid.number'];
      }
    }

    var hasError = error !== null;
    if (hasError) {
      lsb.showFieldError($field, $input, error);
    } else {
      lsb.hideFieldError($field, $input, error);
    }

    return !hasError
  }

  /**
   * @param $field
   * @param $input
   * @param error html must be escaped
   */
  lsb.showFieldError = function($field, $input, error) {
    if ($field.attr('aria-invalid') !== undefined) {
      $field.attr('aria-invalid', 'true');
    }
    if ($input.attr('aria-invalid') !== undefined) {
      $input.attr('aria-invalid', 'true');
    }

    $input.addClass(lsbConfig.class.inputHasErrorClass);
    $field
      .addClass(lsbConfig.class.fieldHasErrorClass)
      .find(SELECTOR_FIELD_ERROR)
      .removeClass(CLASS_VISUALLY_HIDDEN)
      .html(error);
  }

  lsb.hideFieldError = function($field, $input) {
    if ($field.attr('aria-invalid') !== undefined) {
      $field.attr('aria-invalid', 'false');
    }
    if ($input.attr('aria-invalid') !== undefined) {
      $input.attr('aria-invalid', 'false');
    }

    $input.removeClass(lsbConfig.class.inputHasErrorClass);
    $field
      .removeClass(lsbConfig.class.fieldHasErrorClass)
      .find(SELECTOR_FIELD_ERROR)
      .addClass(CLASS_VISUALLY_HIDDEN)
      .text('');
  }

  lsb.showGlobalError = function($form, error) {
    $form.find('.lsb-form-global-error')
      .removeClass(CLASS_VISUALLY_HIDDEN)
      .html(error);
  }

  lsb.hideGlobalError = function($form) {
    $form.find('.lsb-form-global-error')
      .addClass(CLASS_VISUALLY_HIDDEN)
      .text('');
  }

  lsb.onSubmitForm = function($form) {
    var defaultErrorMessage = lsbConfig.trans['global.unknown_error'];
    var $submitButton = $form.find('button[name=lsb_form_submit]');
    var $nonceInput = $form.find('.js-nonce-input');
    var $loader = $form.find('.lsb-loader');
    var $loaderAria = $form.find('.lsb-loader-aria');

    var isValid = lsb.validateForm($form);
    if (!isValid) {
      lsb.showGlobalError($form, lsbConfig.trans['global.form_contains_errors']);
      return;
    }

    lsb.hideGlobalError($form);
    $loader.show();
    $loaderAria.text(lsbConfig.trans['global.loading']);
    $submitButton.attr('aria-disabled', 'true');
    $submitButton.attr('disabled', 'disabled')
    $submitButton[0].disabled = true;


    var url = $form.data('formPostUrl');
    var data = $form.serialize();
    $.ajax({
      data: data,
      url: url,
      method: 'post',
      dataType: 'json',
      success: function(data) {
        var status = null;
        if (data && data.hasOwnProperty('status')) {
          status = data.status;
        }

        if (status === 'invalid_nonce' && data.hasOwnProperty('nonce')) {
          $nonceInput.val(data.nonce);
          var nonceRefreshedAt = $nonceInput.data('refreshedAt');
          if (nonceRefreshedAt && Date.now() - nonceRefreshedAt < 1000 * 10) {
            // prevent refresh loop
            doOnError(defaultErrorMessage);
            return;
          }
          $nonceInput.data('refreshedAt', Date.now())
          $form.trigger('submit');
          return;
        }

        var html = defaultErrorMessage;
        if (data && data.hasOwnProperty('html')) {
          html = data.html;
        }

        if (status === 'success') {
          doOnSuccess(html)
        } else {
          doOnError(html);
        }
      },
      error: function() {
        doOnError(defaultErrorMessage)
      }
    });

    function doOnSuccess(html) {
      $form.find('.lsb-form-body').remove();
      var $successMessageContainer = $form.find('.lsb-form-success-container');
      $successMessageContainer
        .html(html)
        .removeClass(CLASS_VISUALLY_HIDDEN);

      var containerTop = $successMessageContainer.offset().top;
      var windowTop = $(window).scrollTop();
      var windowHeight = $(window).height();

      var $header = $('header');
      var headerHeight = 0;
      if ($header.length) {
        var headerPosition = $header.css('position');
        if (headerPosition === 'fixed' || headerPosition === 'absolute') {
          headerHeight = $header.outerHeight();
        }
      }

      if (containerTop < windowTop || containerTop > (windowTop + windowHeight)) {
        $('html, body').animate({
          scrollTop: containerTop - headerHeight - 20
        }, 'smooth');
      }
    }

    function doOnError(html) {
      lsb.showGlobalError($form, html);
      $loader.hide();
      $loaderAria.text('');
      $submitButton.removeAttr('disabled');
      $submitButton.removeAttr('aria-disabled');
      $submitButton[0].disabled = 0;
    }
  }

  $(function() {
    // submit form
    $('body').on('submit', SELECTOR_FORM, function(e) {
      e.preventDefault();
      var $form = $(this);
      lsb.onSubmitForm($form);
    });

    // validation on blur
    $('body').on('blur', SELECTOR_FORM + ' ' + SELECTOR_FIELD_WRAPPER, function(e) {
      var $form = $(this).closest(SELECTOR_FORM);
      var $field = $(this).closest(SELECTOR_FIELD_WRAPPER);

      // The timeout is required to get the correct active element
      setTimeout(function() {
        var $activeField = null;
        if (document.activeElement) {
          $activeField = $(document.activeElement).closest(SELECTOR_FIELD_WRAPPER);
          // Validation is done if the current focus is not in same group.
          // This prevents errors from showing up when user uses tab to go to the preferred checkbox.
          if ($activeField.length && $activeField[0] === $field[0]) {
            return;
          }
        }

        lsb.validateField($field);
        if ($activeField && $activeField.length) {
          // only hide the global error if there is an active field
          lsb.hideGlobalError($form);
        }
      }, 10);
    });

    // Validation of previous fields
    $('body').on('focus', SELECTOR_FORM + ' ' + SELECTOR_FIELD_WRAPPER, function(e) {
      var $form = $(this).closest(SELECTOR_FORM);
      var $fields = $form.find(SELECTOR_FIELD_WRAPPER);
      var currentFieldIndex = $fields.index($(this));

      $fields.slice(0, currentFieldIndex).each(function() {
        lsb.validateField($(this));
      });
    });

  });
})(jQuery);