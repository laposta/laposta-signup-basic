(function ($) {
  $(function() {
    // defaults
    let defaultErrorMessage = lsbTranslations['global.unknown_error'];

    // submit form
    $('body').on('submit', '.js-lsb-form', function(e) {
      e.preventDefault();
      let $form = $(this);
      let $errorContainer = $form.find('.lsb-form-global-error');
      let $submitButton = $form.find('button[name=lsb_form_submit]');
      let $nonceInput = $form.find('.js-nonce-input');
      let $loader = $form.find('.lsb-loader');

      $errorContainer.hide();
      $loader.show();
      $submitButton.attr('disabled', 'distabled')
      $submitButton[0].disabled = true;

      let url = $form.data('formPostUrl');
      let data = $form.serialize();
      $.ajax({
        data: data,
        url: url,
        method: 'post',
        dataType: 'json',
        success: function(data) {
          let status = null;
          if (data && data.hasOwnProperty('status')) {
            status = data.status;
          }

          if (status === 'invalid_nonce' && data.hasOwnProperty('nonce')) {
            $nonceInput.val(data.nonce);
            let nonceRefreshedAt = $nonceInput.data('refreshedAt');
            if (nonceRefreshedAt && Date.now() - nonceRefreshedAt < 1000 * 10) {
              // prevent refresh loop
              doOnError(defaultErrorMessage);
              return;
            }
            $nonceInput.data('refreshedAt', Date.now())
            $form.trigger('submit');
            return;
          }

          let html = defaultErrorMessage;
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
        $errorContainer.html('')
        $errorContainer.hide();
        $form.replaceWith(html);
        doAlways();
      }

      function doOnError(html) {
        $errorContainer.html(html)
        $errorContainer.show();
        doAlways();
      }

      function doAlways() {
        $loader.hide();
        $submitButton.removeAttr('disabled');
        $submitButton[0].disabled = 0;
      }

    });
  });
})(jQuery);