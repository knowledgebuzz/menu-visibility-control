=== Menu Visibility Control ===
Contributors: knowledgebuzz
Donate link: https://knowledge.buzz/donate
Tags: menu, visibility, roles, navigation, conditional
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple plugin to control WordPress menu item visibility based on login status or user roles.

== Description ==

Menu Visibility Control lets you easily choose who can see each menu item:
* Everyone
* Only logged-in users
* Only logged-out users
* Specific user roles

The options appear directly in the WordPress menu editor. Lightweight and minimal – no complicated settings page needed.

This is useful for membership sites, communities, or any site where you want custom navigation per user type.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/menu-visibility-control` directory, or install via the WordPress plugin installer.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Appearance > Menus**, edit a menu item, and set the **Visibility** option.

== Frequently Asked Questions ==

= Where do I find the settings? =
There is no separate settings page. You will see the new **Visibility** options when editing individual menu items.

= Can I restrict menus by user role? =
Yes. Select "User Roles" as the visibility option, then tick which roles can see the menu item.

= Will it work with any theme? =
Yes. It uses WordPress core menu filters, so it works with all themes.

== Screenshots ==

1. Visibility options in the menu editor.
2. Example of role selection.

== Changelog ==

= 1.0.1 =
* Initial public release.
* Added role-based visibility.
* Added nonces and sanitization for security.

== Upgrade Notice ==

= 1.0.1 =
First release of Menu Visibility Control. Secure and lightweight.
