=== GetResponse Integration ===
Contributors: GetResponse
Tags: getresponse, email, newsletter, signup, marketing, plugin, widget, mailing list, subscriber, contacts, subscribe form, woocommerce
Requires at least: 3.3.0
Tested up to: 4.2
Stable tag: 3.0.1

The GetResponse Integration plugin allows you to quickly and easily add a sign-up form to your site.

== Description ==

This plug-in enables installation of a GetResponse fully customizable sign up form on your WordPress site or blog. Once a web form is created and added to the site the visitors can sign up to your contact list. They will be automatically added to your GetResponse campaign and sent a confirmation email. The plug-in additionally offers sign-up upon leaving a comment or registration form.
If you have a WooCommerce account, this plugin will allow newsletter subscrpition via your checkout page.

If you have any question please contact us via contact form:
https://app.getresponse.com/feedback.html?devzone=yes

== Installation ==
= Method 1. =

1. Go to your WordPress admin account.
2. Open Plug-Ins in the left-side bar menu, choose Add New, and search for GetResponse plug-in. Choose the available GetResponse Integration version.
3. Install the plug-in and activate it in your account.

= Method 2. =

1. Download the GetResponse plug-in for your WordPress version.
2. Unzip the downloaded file and extract the code to to your /wp-content/plugins/ folder.
3. To complete installation you should activate the module in the plug-ins section of your administration panel.

= Configuration: =

1. Create the web form in your GetResponse account.
2. Go to your plug-in settings in your WordPress account.
3. Enter the API key and click "Save" to enable it.
4. Enable the “Subscribe via Comment” option if you want to offer all commenting visitors to join your mailing list. Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
5. Enable the “Subscribe via Registration Page” option if you want to offer your users to join your mailing list. Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
6. Enable the “Subscribe via Checkout Page” option if you want to offer your customers to join your mailing list at the checkout stage. (available only if WooCommerce is activated). Type in the invitation to subscribe e.g. “Subscribe to join the buzz”.
7. In the top menu bar inside the Wordpress WYSIWYG editor you will find a dropdown menu with all your GetResponse Web Forms. Click on the selected Web Form and it will be added into your Wordpress post.
8. On the Wordpress Widgets page you can drag the GetResponse Web Form module into desired page areas.

With GetResponse form builder you can fully adjust the form to your needs: add brand logo and image, custom fields, and confirmation URLs, or enable pop-up option. Note that to modify your WordPress form you need to do it from GetResponse account – the changes will be displayed automatically on your site.

== Frequently Asked Questions ==

= Where can I place my web form on my Wordpress page? =
You can embed your web form in the sidebar, pages, posts or in a lightbox. In order to use a lighbox, choose this form type in the web form type section, made available in the form editor in your GetResponse account.

= Where can I find my API Key? =
You can find it on your GetResponse profile in Account Details -> GetResponse API

== Screenshots ==

1. Widgets
2. Plugin settings
3. WYSIWYG editor
4. Webform view
5. Site view
6. Leave a comment


== Changelog ==

= v3.0.1 =

* Changed param name WebformId => formId in API v3 forms method
* Added Check webform/form status on list
* Improved performance on widget list
* Fixed js issues to display variants
* Added Translations

= v3.0 =

* Integration is based on new REST API v3.0
* Added support for new Web forms (Forms)

= v2.2.2 =

* Fixed js issue with undefined param

= v2.2.1 =

* Fixed sort method (compatible with php < 5.4)

= v2.2 =

* Added Subscribe via BuddyPress Registration page
* Added Subscribe via WooCommerce Registration page
* Added Convert special characters to HTML entities in Webform url
* Added Shortcode attribute "center" - allows to center Webform easily
* Added Shortcode attribute "center_margin" - allows to edit margin (center position)
* Campaign names and Web Forms are now sorted by name using case-insensitive ordering

= v2.1.4 =

* Fixed bug, hook register_post changed to user_register. Thanks to @Wieckowy.

= v2.1.3 =

* Fixed problem with HTTPS blog

= v2.1.2 =

* Fixed problem with displaying web forms with the same names on drop down list

= v2.1.1 =

* Shortcode updated to TinyMCE 4 (fixed broken visual editor)
* Description, Installation and FAQ updated

= v2.1 =

* Added subscribe via the registration page
* Campaign names and Web Forms are now sorted by name
* Added checking if curl extension is set and curl_init method is callable
* Removed typo and deprecated unused params
* Tested up to: 3.9.1

= v2.0.7 =

* Fixed Class name changed in class-gr-widget-webform

= v2.0.6 =

* Class name changed from GetResponse to GetResponseIntegration, in some cases caused error: Cannot redeclare class GetResponse

= v2.0.5 =

* Tested up to: 3.9
* Shortcode updated to TinyMCE 4

= v2.0.4 =

* Changelog updated
* Screenshot updated
* Default ref custom (ref => wordpress) added to API request

= v2.0.3 =

* Fixed typo, Thanks to @Reza
* Tested up to: 3.8.3

= v2.0.2 =

* Fixed curl error notification
* Trigger error deleted

= v2.0.1 =

* Fixed Strict standards: non-static method
* Fixed empty variables
* Fixed empty campaigns notice
* Register actions moved to constructor
* Tested up to: 3.8.2

= v2.0 =

* Integration is based on API Key;
* Web form ID needs no longer to be copied; now web form is selected from the drop-down menu;
* Customer details can be updated at Checkout page;
* Checkout subscription checkbox now can be ticked by default;
* Comments subscription checkbox now can be ticked by default;
* Shortcode now contains webform url instad of webform id;
* Drop-down menu with webforms has been added to WYSIWYG editor;
* Web forms can now be instantly added into multiple places inside the WordPress page via Widgets;
* Custom fields can be easily mapped via the web form upon subscription;

= v1.3.2 =

* Fixed bugs in getting options. Thanks to @norcross. FAQ updated

= v1.3.1 =

* Added shortcode

= v1.3.0 =

* Added integration with WooCommerce to allow users to subscribe via the checkout page.

= v1.2.1 =

* Fixed code.

= v1.2.0 =

* Note that the web form installed via the old version of the plug-in will still be fully operational, so you do not need to replace it with the new one. If you want to add the new “Subscribe via comment” function, simply delete old plug-in and install new – and use the same web form ID.

= v1.1.1 =

* Fixed integration with new WebForms.

= v1.1 =

* Added possiblity to use Wordpress styles,
* Added integration with new WebForms.

= v1.0 =

* Inital release.