=== Laposta Signup Basic ===
Contributors: roelbousardt, stijnvanderree
Tags: laposta, nieuwsbrieven, aanmelden, formulier, AVG, newsletters, subscribe, form
Requires at least: 4.7
Tested up to: 6.1
Requires PHP: 7.1
Stable tag: 1.4.0
License: BSD 2-Clause License

Laposta is a Dutch email marketing solution.
This plugin can be used to load any of your Laposta lists and render its fields in a HTML form that can be fully customized using CSS.
You can choose between Bootstrap, our default, or a fully custom implementation.


== Installation ==

Unzip the file in the plugins directory, and activate the plugin in the
Plugins screen. Then go to the Settings to setup the connection to Laposta and customize the form rendering.
Finally, to render a form, simply use the shortcode as shown in the Settings.


== Screenshots ==

1. HTML form

2. HTML5 validation of fields

3. Datepicker for date fields

4. Settings: loading of lists with shortcode

5. Settings: What CSS to use? Option to set elements manually.

6. Settings: add inline CSS and misc settings


== Frequently Asked Questions ==

= The changes I made to my lists are not being shown on my website. What should I do?  =

Please login to your admin dashboard and go to "Settings" -> "Laposta Signup Basic" and click on the button with the text "Reset Cache"

= Ik heb mijn lijst aangepast, maar ik zie de veranderingen niet op mijn website. Wat kan ik doen?  =

Login op uw admin dashboard en ga naar "Instellingen" -> "Laposta Signup Basic" en klik op de knop met de tekst "Reset Cache"


== Upgrade Notice ==

= 1.4.0 =

* Tested up to 6.1


== Changelog ==

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
