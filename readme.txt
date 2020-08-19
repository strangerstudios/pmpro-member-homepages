=== Paid Memberships Pro - Member Homepages Add On ===
Contributors: strangerstudios
Tags: paid memberships pro, pmpro, homepages, redirect, landing page, members
Requires at least: 3.5
Tested up to: 5.5
Stable tag: 0.3

Redirect members to a unique homepage or landing page based on their level.

== Description ==

This Add On for Paid Memberships Pro allows you to assign a unique homepage or landing page for each membership level. It will optionally redirect members to their level's page when trying to access your WordPress site's "front page" (either the all posts page or a static page) and on login.

== Installation ==

= Download, Install and Activate! =
1. Upload the `pmpro-member-homepages` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Edit your membership levels to assign a level's homepage and configure settings.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-member-homepages/issues

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at https://www.paidmembershipspro.com for more documentation and our support forums.

== Changelog ==

= 0.3 - 2020-08-19 =
* FEATURE: Added a setting to ignore other `redirect_to` attributes and always override with the member homepage.

= 0.2 - 2020-08-13 =
* FEATURE: Added a setting to redirect away from homepage or not.
* BUG FIX: Fixed some warnings.
* ENHANCEMENT: Added filter `pmpro_member_homepage_id` to set to any post ID, including a CPT.
* ENHANCEMENT: Prepared for localization.

= .1 =
* Initial commit.
