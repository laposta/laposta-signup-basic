=== Laposta Signup Basic ===
Contributors: roelbousardt, stijnvanderree
Tags: laposta, newsletters, marketing, form, GDPR
Requires at least: 4.7
Tested up to: 6.9
Requires PHP: 7.1
Stable tag: 3.2.4
License: BSD 2-Clause License

Laposta is a Dutch email marketing tool. Load your Laposta lists and render fields in a HTML form with custom styling.


== Installation ==

Unzip the file in the plugins directory, and activate the plugin in the
Plugins screen. Then go to the Settings to setup the connection to Laposta and customize the form rendering.
Finally, to render a form, simply use the shortcode as shown in the Settings.


== Screenshots ==

1. Rendered HTML form, which can be based on the styling of your choice

2. Realtime validation of fields, example 1

3. Realtime validation of fields, example 2

4. Example of HTML5 date field

5. Settings: Overview of lists with shortcode to copy

6. Settings: Choose predefined styles and add custom classes

7. Settings: Inline CSS and other settings


== Frequently Asked Questions ==

= The changes I made to my lists are not being shown on my website. What should I do?  =

Please login to your admin dashboard and go to "Settings" -> "Laposta Signup Basic" and click on the button with the text "Reset Cache"

= How do I enable logging of errors for debugging?  =

* By default, the logger in our plugin follows the setting of `WP_DEBUG`: if `WP_DEBUG` is enabled (true), logging is active.
* To override this default behavior, you can use the filter 'laposta_signup_basic_enable_logging'.
* When logging is enabled, messages are recorded using the PHP `error_log` function. To view these logs, you have three options:
    - **Server Log File**: Typically, you can find the error log in your server's PHP log file. Its location varies depending on your hosting environment.
    - **When `WP_DEBUG_LOG` is Enabled**: If `WP_DEBUG_LOG` is set to true, WordPress logs errors to a `debug.log` file inside the `wp-content` directory. You can access this file via FTP or your hosting file manager.
    - **Using a Plugin**: Plugins like 'Debug' can help you view log messages directly within the WordPress admin area.
    - **Note**: Check your hosting provider's documentation or contact their support for more details on locating and accessing log files.

= What are the available Wordpress filters?  =

* Enable logging - 'laposta_signup_basic_enable_logging': A filter to enable or disable logging of errors within this plugin. The first and only argument is the default value, which is based on WP_DEBUG.
* Settings page capability - 'laposta_signup_basic_settings_page_capability': Modifies the required capability for editing the plugin settings. The first and only argument is the capability.
* Menu position - 'laposta_signup_basic_menu_position': Modifies the position of the menu item in the admin environment. The first and only argument is the position.
* Field label - 'laposta_signup_basic_filter_field_label': Modifies the field label. The first argument is the field label, the second is the list ID, and the third is an array of the field.
* Required indicator - 'laposta_signup_basic_filter_required_indicator': Modifies the required indicator at the end of the field label. The first argument is the indicator, the second is the list ID, and the third is an array of the field.
* Field placeholder - 'laposta_signup_basic_filter_field_placeholder': Modifies the field placeholder. The first argument is the field placeholder, the second is the list ID, and the third is an array of the field.
* Field default select option text - 'laposta_signup_basic_filter_default_select_option_text': Modifies the text of the default select option. The first argument is the default text, the second is the list ID, and the third is an array of the field.
* Submit button text filter - 'laposta_signup_basic_filter_submit_button_text': Modifies the submit button text. The first argument is the button text, the second is the list ID, and the third is an array of arguments provided in the shortcode.
* Success title filter - 'laposta_signup_basic_filter_success_title': Alters the success title text. The first argument is the success title, the second is the list ID, and the third is an array containing the submitted fields.
* Success text filter - 'laposta_signup_basic_filter_success_text': Changes the success message text. The first argument is the success text, the second is the list ID, and the third is an array containing the submitted fields.


== Upgrade Notice ==

= 3.2.4 =
Changed: Bundled the scoped Laposta API PHP library (v2.2.0) to avoid PSR-7 namespace conflicts with other plugins.

== Changelog ==

= 3.2.4 =
Changed: Bundled the scoped Laposta API PHP library (v2.2.0) to avoid PSR-7 namespace conflicts with other plugins.

= 3.2.3 =
Tested up to 6.9

= 3.2.2 =
Fixed compatibility issues with Laposta API v2.


= 3.2.1 =
* Fixed: A logic bug caused the plugin to always use the legacy v1.6 Laposta API wrapper, even on PHP >= 8.0. This has been corrected to ensure proper detection and use of the v2 wrapper.


= 3.2.0 =
* Added: Bundled Laposta API PHP wrapper v2.0.1 for compatibility with PHP >= 8.0.
* Maintained: Laposta API wrapper v1.6 remains included for PHP < 8.0 compatibility.

= 3.1.3 =
* Fixed: A bug where the default success classes were not added when custom classes were provided.


= 3.1.2 =
* Fixed an issue where the shortcode was not executing correctly in WordPress 6.7.


= 3.1.1 =
* Removed an unnecessary variable from form.php that triggered an error in PHP 8.


= 3.1.0 =
* Important Notice: The plugin settings have been relocated. You can now access them directly from the main menu instead of the settings submenu.
* The default Dutch translation is now informal instead of formal, providing a friendlier tone better suited for most users.

Key Changes:
* The plugin is now WCAG 2.1 compliant, enhancing accessibility with appropriate aria attributes, improved keyboard navigation, and screen reader support.
* Error feedback is now provided immediately below fields for clearer and more immediate guidance to users.
* Checkbox and radio groups are now enclosed in a fieldset with a legend for improved accessibility and structure.
* Additional wrappers have been added for better layout control, and the success message is now contained within the form element, improving compatibility with screen readers.
* Date inputs now use the HTML5 date type for improved user experience in supported browsers. For unsupported browsers, placeholders will display the correct format.


= 2.7.0 =
* Tested up to: 6.6


= 2.6.0 =
* Support for Page Caching: Enhanced the plugin to automatically refresh nonces on forms when detected as invalid.


= 2.5.2 =
* Fixed Dutch translation error


= 2.5.1 =
* Fix: added missing locales for datepicker


= 2.5.0 =
* Multi-language support: The plugin now defaults to English.
* Dutch translations: Added formal Dutch translations. These translations are automatically applied when the site's language is set to Dutch.


= 2.4.0 =
* Added the autocomplete attribute to the relevant form fields


= 2.3.0 =
* Integrated custom error logger for optional error logging, enhancing debugging and troubleshooting capabilities, see FAQ for more information.
* Resolved an issue where AJAX was not handling form submissions in dynamically added HTML forms.


= 2.2.0 =
* More filters were added. See FAQ for the details.

= 2.1.0 =
* Filters were added for submit button text, success title and success text. See FAQ for details.

= 2.0.1 =
Please note, this is a major update and may not be 100% backwards compatible with previous versions.

Key changes:
* Instant Feedback: Forms are now submitted using AJAX, providing immediate feedback on errors or success directly within the user's current view. This enhances user experience by eliminating page reloads and keeping important feedback prominently visible.
* Error container placement: The error container is therefore placed above the submit button. This change enhances error visibility.
* Conditional custom class loading: Before, some custom classes were always loaded and some were only loaded if the chosen styling was set to 'custom'. Now all classes are loaded based on the selection in the admin UI. This update aims to improve the consistency of the admin UI.

Impact on Your Site:
* If you have custom styling or scripts that depend on the old error container placement, you may need to adjust them.
* Customizations relying on the custom classes should be reviewed to ensure compatibility.

= 1.4.3 =

* fixed issue #6 for undefined variable $globalErrorClass in templates/form/form.php

= 1.4.2 =

* Added CSRF protection for clean cache implementation & tested up to: 6.3

= 1.4.1 =

* Fixed bug by forcing lists to be an array in settings.php

= 1.4.0 =

* Tested up to 6.1


= 1.3.0 =

* Tested up to 6.0 and added Settings link in plugins overview.


= 1.2.3 =

* Tested up to: 5.9


= 1.2.2 =

* Fixes errors for PHP 8


= 1.2.1 =

* Fix for the action "reset cache" not respecting the filter "laposta_signup_basic_settings_page_capability".


= 1.2.0 =

* Filter added for the capability of the options page: "laposta_signup_basic_settings_page_capability".


= 1.1.1 =

* Bugfix for an error being shown at first install when the laposta api key is not set.


= 1.1.0 =

* The submit button text can be provided in the plugin settings


= 1.0.1 =

* Minor text fixes in plugin settings


= 1.0.0 =

* Plugin initialised
