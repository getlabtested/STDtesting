=== Contact Form 7 - Campaign Monitor Addon ===
Contributors: joshuabettigole
Donate link: http://www.bettigole.us/donate/
Tags: Campaign Monitor, Contact Form 7, Newsletter, Opt In, Email Marketing
Requires at least: 2.9
Tested up to: 3.0
Stable tag: 0.99

Add the capability to create newsletter opt-in forms with Contact Form 7. Automatically submit subscribers to predetermined lists in Campaign Monitor.

== Description ==

The Contact Form 7 - Campaign Monitor Addon plugin adds functionality into Contact Form 7 generated forms to automatically submit subscribers to a predetermined list within a Campaign Monitor client account. The plugin settings are configured on a per-form basis on the Contact Form 7 configuration pages.

### Requirements

#### WordPress
This plugin was built and tested on WordPress version 3.0. It should work with version 2.9, but this configuration is untested. Earlier versions are not supported by Contact Form 7, therefore, can not be supported by this plugin.

#### Contact Form 7
Contact Form 7 provides the form configuration and processing functionality necessary for this plugin to work. There are no configuration options for this plugin outside of the Contact Form 7 configuration screens. You will also need a basic understanding of how to configure Contact Form 7. View the Contact Form 7 plugin [documentation](http://contactform7.com/docs/) for an explanation of fields and how to configure forms.

#### Campaign Monitor Reseller Account
The Campaign Monitor API (Application Programming Interface) requires the API Key provided to members with accounts directly on Campaign Monitor. Client accounts do not have access to this key. If you did not sign up directly with Campaign Monitor, you will need to ask your service provider for the API Key.

== Installation ==

1. Upload `contact-form-7-campaign-monitor-addon` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Obtain API Key, API Client ID and the API Subscriber List ID from Campaign Monitor
1. Enable and configure the Campaign Monitor section on the Contact Form 7 "Edit" page.

For detailed configuration, [download the complete manual](http://www.bettigole.us/downloads/cf7_cm_user_manual.pdf).

== Frequently Asked Questions ==

= Does this plugin allow for an opt-in checkbox =

Yes, by including a checkbox tag in the form such as:
[checkbox add-email-list default:1 "Add Me To Your Mailing List"]

Then add [add-email-list] to the "Required Acceptance Field" option in the Campaign Monitor section.


== Screenshots ==

1. Campaign Monitor Addon Configuration
2. Form Configuration with Opt In

== Changelog ==

= 0.99 =
First Release

== ToDo ==

* Add support for the same languages supported by Contact Form 7.
* White Label the configuration options.
