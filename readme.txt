=== Member Profile Forms / Custom Registration / Post From Profile in BuddyPress / BuddyBoss ===
Contributors: svenl77, themekraft, buddyforms, gfirem
Tags: buddypress, buddypress registration, post from profile, member profile, user profile, 
Requires at least: 3.9
Tested up to: 6.0.2
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress and BuddyBoss Registration and Profile Forms. Create Member Profile Tabs form Forms. Let your Members create, edit, and delete any kind of data from their profile.

== Description ==

Integrate Forms into the BuddyPress Members Profile. Allow your users to write - edit - update posts, images, videos, & just about any other content to your community, right from their BuddyPress Member Profile! 

Enable Submitting and managing any kind of data from the User Profile for Different Member Types. 

[youtube https://www.youtube.com/watch?v=K3JK9ISu0-w]

### The extension allows you to:
> * Integrate Forms into the BuddyPress Member Profile. Allow your users to write - edit - upload posts, images, videos, & just about any other content to your community, right from their BuddyPress Member Profile!
> * Create group forms: Many forms can share the same Profile Tab. All Forms with the same attached page can be grouped together. They will be listed as sub-navigation tabs in the main menu of the profile tab.
> * Manage the visibility of who can see your submissions in your profile
> * > Private - Only the logged-in member in his profile.
> * > Community - Logged in user can see other users' profile posts
> * > Public Visible - Unregistered users can see user profile posts
> * Manage permissions: define, create, edit and delete rights for each user role and form.
> * Create Member Type Forms.
> * Overwrite the BuddyPress and BuddyBoss Profile Forms with a Member Type specific Form.
> * MAny Addons for Moderation and other Integration

### Use ACF Fields in the BuddyPress Registration and Profile Forms.
Create Registration Forms Form ACF (Advanced Custo Fields)
Sync ACF with BuddyPress xProfile Fields 

### Use Pods Fields in the BuddyPress Registration and Profile Forms.
Create Registration Forms Form Pods
Sync Pods with BuddyPress xProfile Fields 

### How to Post and Manage Content from a BuddyPress/BuddyBoss Member Profile or Group 
Publishing Content in BuddyPress or BuddyBoss Made Easy. Enable your users to create content and bring your community to life. Easy Front-End Blogging with BuddyPress and BuddyForms!
[How To Member Profile CMS with BuddyPress or BuddyBoss](https://themekraft.com/wordpress-solutions/post-manage-content-buddypress/)

> #### Docs & Support
> * Find our Getting Started, How-to and Developer Docs on [docs.buddyforms.com](http://docs.buddyforms.com/)

> #### Follow Us
> [Blog](https://themekraft.com/blog/) | [Twitter](https://twitter.com/buddyforms) | [Facebook](https://www.facebook.com/buddyforms/) | [YouTube](https://www.youtube.com/playlist?list=PLYPguOC5yk7_aB2Q2FTaktqHCXDc_mzqb)

---

> **Powered with ❤ by [ThemeKraft](https://themekraft.com)**

== Installation ==

Upload the entire BuddyForms folder to the /wp-content/plugins/ directory or install the plugin through the WordPress plugins screen directly.
Activate the plugin through the 'Plugins' menu in WordPress.
Head to the 'BuddyForms' menu item in your admin sidebar

== Frequently Asked Questions ==

= Can user post from there profile =
Yes, you can create new tabs from any form type and post type

= Can I create a Registration Forms =
Yes, you can Create Registration Forms and use BuddyPress and WordPress or ACF and many other form elements and keep all in sync

= Can I create a Content Forms =
Yes, you can create Content forms for any post type and also group forms unde rone parent tab in the user profile.

= Can I Combine Registration Forms and Content Forms =
Yes, you can add login and registration Form Elements to any form and ask your user to Login or Register during form submission.

= Can I create different prtofile forms for different member types =
Yes, you can create different member type forms and also overwrite teh feault BuddyPress or BuddyBoss Profile edit form

= Can I use ACF Fields in the BuddyPress Registation =
Yes, you can use ACF and Podsfirlds and many other by using our Extensions


== Documentation & Support ==

> #### Extensive Documentation and Support
> * The Documentation with many how-to’s will help you on your way.
> * Find our Getting Started, How-to and Developer Docs on [docs.buddyforms.com](http://docs.buddyforms.com/)
> * If you still get stuck somewhere, our support gets you back on the right track. You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard and the Help Center!

== Screenshots ==

1. **Overview in Member Profile** - The overview of each author's posts to be seen in the related member profile.
2. **Create/Edit Post in Member Profile** - When creating a new post or editing an existing one, right from the member profile.
3. **Add New Form** - This is how it looks when you add a new form with BuddyForms.
4. **Form Builder** - Enjoy the easy drag-and-drop form builder of BuddyForms.
5. **Backend Overview** - The backend overview of your existing forms.

== Changelog ==
= 1.5.0 - 04 Oct 2022 =
* Fixed issue with plugin download url
* Tested up to WordPress 6.0.2

= 1.4.22 - 26 May 2022 =
* Fixed security issue.
* Tested up to WordPress 6.0

= 1.4.21 - 16 May 2022 =
* Updated readme.txt

= 1.4.20 - 9 Mar 2022 =
* Added descriptive video about the main features of the plugin.
* Updated the readme.text

= 1.4.19 - 6 Mar 2022 =
* Fixed issue with xprofile fields displaying in free version.
* Tested up WordPress to 5.9

= 1.4.18 - 6 Jan 2022 =
* Improved Freemius integration.
* Tested up BuddyPress 9.2

= 1.4.17 - 10 Jul 2021 =
* Tested up WordPress 5.8
* Fixed PHP exception when a notification doesn't have any send_to email checked.
* Added filter (hook) "buddyforms_shortcode_the_loop_post_status" used to fix compatibility issue with BuddyForms Moderation.
* Add filter (hook) "buddyforms_members_skip_setup_nav" to skip nav item on the Member Profile nav.
* Fixed issue with xProfile integration on Select and Radio form fields.
* Removed Member Type restriction on the "Override Edit Profile Form" functionality, now users can use this functionality even if there's no any Member Type created.

= 1.4.16 - 13 May 2021 =
* Remove un-used composer dev dependencies.
* Fixed issue with missing form's integrated tabs on the Member Profile.

= 1.4.15 - 9 Mar 2021 =
* Tested up with WordPress 5.7
* Added auto-refresh feature for the Xprofile field on the Form Builder.
* Fixed issue with Xprofile field type Selector, which was not showing the saved value in the form on the Frontend view.
* Fixed on the "Map with existing xProfile Field" feature to allow to set up the default visibility level on Xprofiles on the user registration.
* Fixed on HTML structure for Xprofile when labels are disabled.
* Improved support for locals translations to Singular Name on members nav items.
* Fixed issue with Checkboxes & Radios Xprofile fields. Unchecked values were not being saved.

= 1.4.14 - 3 Feb 2021 =
* Fixed issue with the "pro" fields (xprofile ones) that are remained disabled when BuddyForms was free but BF Members pro.
* Add support to form_action use in PR 714 to fix an issue on the parent extension.

= 1.4.13 - 12 Nov 2020 =
* Fixed CSS issue related with build-in modals in BuddyBoss.
* Added extra validation on the hook "buddyforms_check_send_message_to_member_conditions" to avoid notices.
* Feature added to auto-select "Send to Member" on at least one notification after enabled the option "Send Message to Member".
* Fixed issue related to the custom class option on the BP Member Type field which was not being added in the Front-end.
* Fixed issue on BP Member Type field that was avoiding the name being properly updated.

= 1.4.12 - 3 November 2020 =
* Added: New option to integrate a form as a member contact form.
* Feature: Show notice when the Send Message to Member feature is enabled and Send Email to Member has not been checked on any Notification.
* Fixed: Add missing validation in the code to avoid warnings and notices.
* Fixed: Spelling correction.
* Fixed: add missing pro labels and restriction over te form builder related to Member Type, xProfile Field, and xProfile Group.

= 1.4.10 - 17 May 2020 =
* Fixed error code related to the tmpga library.
* Improved the localization of the plugin.
* Added compatibility with loco translate.

= 1.4.9 - 17 March 2020 =
* Fixed the compatibility with BuddyBoss Platform.

= 1.4.8 - 6 Jan 2020 =
* Fixed the global setting from buddyforms to show the general tab.

= 1.4.7 - 8 Dec 2019 =
* Fixed the hook priority for the post list actions.
* Improved the compatibility with BF 2.5.8.

= 1.4.6 - 17 Oct 2019 =
* Removed the deprecate create function.
* Improved compatibility with the last version of BF.
* Added CSS to show Xprofile errors.

= 1.4.5 24 May. 2019 =
* Fixed an issue with the member types. The profile edit was broken if no member type was set

= 1.4.4 29 April. 2019 =
* Fixed an issue with the css rule that make the links not clickable in some themes.

= 1.4.3 4 April. 2019 =
* Fixed a issue in the Member Pages with latest BP and nouveau theme.

= 1.4.2 28 Nov. 2018 =
* Fixed an issue with the labels of the member types in the frontend registration forms

= 1.4.1 15 August. 2018 =
* Fixed an issue with the labels of the member types

= 1.4 21 Jun. 2018 =
* Created a new form element to select a "Member Taxonomy". This was done with the "Custom xProfile Field Types" plugin. But the plugin is discontinued. "This plugin was closed on January 26, 2018 and is no longer available for download. Reason: Security Issue."
* Added a new options for the data type and xprofile field select
* Added new option for the select box. multiple, create new, limit
* Added a new tab BuddyPress to all form elements with options for all xprofile relevant data. To map existing.
* Added a new tab BuddyPress to the general settings page to overwrite the BuddyPress edit screen. You can select a different form for any member type
* Added option to select a form for each member type
* Added data mapping for all registration for elements
* Added new option for user with no member type to select a default form. Otherwise user with no member type would always get the BuddyPress default.
* Added new function to redirect to profile or profile edit
* Added the label and description to the member taxonomy form element
* Fixed a issue with the xprofile description
* Fixed some array loop issues

= 1.3.4 27 Mar. 2018 =
* Fixed an issue with saving the xprofile fields if new user registration the user id was missing if bp_loggedin_user_id was used. switched to the attribute $user_id

= 1.3.3 27 Mar. 2018 =
* Fixed an issue with in the xProfile fields. The url and the member field was not validated.

= 1.3.2 22 Mar. 2018 =
* Added new options to the form element to select a member type default and hide the member type form element to auto assign a member type during submit of a form.

= 1.3.1 8 Feb. 2018 =
* Added select 2 support to the xProfile fields and display the field label in the form builder
* Front-end post edit link bug fixed buddyforms_posttypes_default props to Hannah93
* Created a new function to check if the login redirect is set to profile and redirect to the user profile
* Make sure we have a value or use te default redirect url
* Freemius update to the latest SDK

= 1.3.0.5 8 Sep. 2017 =
Added a filter buddyforms_user_posts_query_args_posts_per_page to allow change the posts_per_page

= 1.3.0.4 12 August 2017 =
* Fixed some issues in the freemius integration
* Fixed a redirect issue popped up in the latest version
* Make the registration redirect free available. Its was a pro feature but we decide to make it free its to general feature for pro

= 1.3.0.3 1 August 2017 =
* Freemius update to allow free pro add ons
* Make sure the core is loaded

= 1.3.0.2 27 July 2017 =
* Added freemius trial code

= 1.3.0.1 25 July 2017 =
* Fixed a freemius issue cursing fatal error in some instances during activation if BuddyForms is deactivated

= 1.3 25 July 2017 =
* New Professional Version available now with great new Features

* Added support for BuddyPress Registration page overwrite with the settings form BuddyForms
* Created tow new form elements xProfile Field and xProfile Group. Its now possible to add xprofile field as normal form elements.
* Created a new form elements Member Types to select the member types you want to make available in the Registration
* Add a new template to overwrite the BuddyPress default registration form
* Make sure registration options are used from BuddyForms general settings
* Added new option for Activity Support
* Added freemius free pro code

= 1.2.4 07.06.2017 =
* Added a new filter into the core buddyforms_loop_template_name to register new loop templates.
* Make sure the new templates do work in the member profile
* Added new filter buddyforms_the_lp_query to adjust the query result
* Added a new filter buddyforms_members_parent_tab to allow other plugins to move forms from parent to sub tabs
* Fixed the post count issue in Profile Tabs
* Added an extra check for capabilities during redirect and display an error message instead of a 404 if the role does not have the required capability

= 1.2.3 =
* Fixed an issue with the pagination probs go to @mfalk75 to find and fix the issue
* New freemius integration

= 1.2.2 =
* Make sure multiple child forms work with BuddyPress enabled even if one the forms is embedded and one is separated but both use the same parent.
* Added the bp->unfiltered_uri[3] to the create url to support child/ parent functionality so plugins not need to reinvent the wheel.
* Create a new function to make the rewrite working if ajax is disabled

= 1.2.1 =
* Fixed a strange issue. The plugin does not have a valid header. I fixed it by add more * to the header.

= 1.2 =
* Add a new option to define the profile visibility
* Private - Only the logged in member in his profile.
* Community - Logged in user can see other users profile posts
* Public Visible - Unregistered users can see user profile posts
* Code cleanup to meet the WordPress coding standards

= 1.1.9 =
* Remove the old wp_pagenavi dependencies.
* Fixed an issue with the pagination. This issue was created with the new parent page logic.
* Create a new function to overwrite the pagination

= 1.1.8 =
* Fixed an issue with the edit link. There was a conflict if the post was actually not created with BuddyForms and if you select display all posts of this post type in the form settings.
* By default it is set to display only posts from the post type created by this form. I have added a extra check so if the post type default setting is available this form ist used
* Rename extention.php to extension.php

= 1.1.7 =
* Fixed an issue with the dependencies management. If pro was activated it still ask for the free version. Fixed now with a new default BUDDYFORMS_PRO_VERSION in the core to check if the pro is active.

= 1.1.6 =
* Remove profiles_visible settings.
* Fixed an issue with the admin bar links creating a 404
* Add dependencies management with tgm
* Fixed an issue if parent sub nav tabs and parent tabs with same attached page but not as parent tab. Should work now in all combinations
* Change buddyforms_locate_template to use only the file slug

= 1.1.5 =
* Add multisite support
* Work on the form builder ui conditionals
* Loop Table View in BuddyPress Profiles
* Create new functions to show hide metaboxes
* Work on the conditionals admin ui
* Add postbox_classes to make the postbox visible.
* get_currentuserinfo change to wp_get_current_user
* Remove unneeded subnav tabs. Its not possible to register a buddy press endpoint without create the nav. To keep it constancy we do it like this and hide the subs with css and remove them with jQuery for mobil usage
* Add form elements select box support
* Fixed the my post tap issue
* Make sure hidden sub tabs in profile the li is hidden too
* Use buddyforms_display_field_group_table to display options
* No more sidebar
* Added CSS fixes for Loop Table View in BuddyPress Profiles
* Loop Table View in BuddyPress Profiles
* Added all BuddyPress related CSS of BuddyForms to this CSS file
* Add edit icon and aria label

= 1.1.4 =
* Fixed one small issue. In some situation the $bp->unfiltered_uri[2] could be empty. I added a if statement to avoid undefined index notice.

= 1.1.3 =
* Add a new function to rewrite the edit link for grouped forms. There have been some rewrite issues left from the 1.1 update.

= 1.1.2 =
* There was an issue cursed by the last update.I have added [$bp->current_component][$bp->current_action] as array to the new global to support many sub pages

= 1.1.1 =
* Create a new global buddyforms_member_tabs to find the needed form slug
* Fixed a redirect issue some users expected wired redirects in the profile. This was happen because of the missing form slug in some setups. Should be fixed now with the now global.

= 1.1 =
* Make it work with the latest version of BuddyForms. The BuddyForms array has changed so I adjust the code too the new structure
* Changed default BuddyForms to BUDDYFORMS_VERSION
* Fixe no post got displayed in the profile tab...
* Added post meta for selecting parent tab
* Added child tab
* Added new option to select the parent
* Add child parent form relationship. I use the attached page to group forms.
* Clean up code after rewrite
* Fix the pagination. The parent my posts pagination was broken. I have fixed this with a redirect to have always the same url structure in the profile.
* Add css for hide the home tab. Its not used and gets redirected.

= 1.0.11 =
* Fixed a small bug with BP_MEMBERS_SLUG. The constant does not work if the slug got changed

= 1.0.10 =
* only display posts created by the form
* remove the old delete post structure
* fixed the dependencies message
* rename session

= 1.0.9 =
* Fixed a conflict with the BP Group Hierarchy Plugin. Props to Mitch for reporting and helping me fix this issue.

= 1.0.8 =
* add a isset check to prevent a array_key_exists error if no form is created.

= 1.0.7 =
* new language files for hebrew thanks to Milena
* add support for the shortcodes button
* changed the query to only show post parents
* changed plugin uri

= 1.0.5 =
* display the form tab only if the user has the needed role
* check if the buddy press component exists
* load the js css if BuddyForms is displayed
* add new admin notice

= 1.0.4 =
* rewrite the integration and data object
* its now translation ready
* Small bug fixes

= 1.0.3 =
* Small bug fixes
* Spelling correction

= 1.0.2 =
* add wp 3.9 support and added a more detailed readme description

= 1.0.1 =
* add buddyforms_members_requirements check

= 1.0 =
* final 1.0 version

