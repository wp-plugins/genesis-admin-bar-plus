=== Plugin Name ===
Contributors: GaryJ
Donate link: http://code.garyjones.co.uk/donate/
Tags: admin bar, genesis
Requires at least: 3.1
Tested up to: 3.1.1
Stable tag: 1.1.1

A conceptual fork of the Genesis Admin Bar Addition plugin, re-written from scratch, adding new features.

== Description ==

* Plugin completely re-written as a class to remove function pollution from the global scope.
* Adds support for menu item positioning, so custom entries can be added anywhere, and not just as the final items.
* Adds *opens in a new window* non-image indicator for modern browsers.
* Easy addition of Support board links via single lines in theme `functions.php`.

== Installation ==

1. Unzip and upload `genesis-admin-bar-plus` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Ensure you're displaying the admin bar (front and / or back-end) via the settings on the User Profile page.

== Frequently Asked Questions ==

= Can I add my own entries to the Genesis menus? =

Yes - there is an action hook to do this. See the *Add Custom Items* section.

= How do I add a link to a certain support board? =

The plugin currently recognises all of the child theme support boards, Genesis, the translations board, the general plugins board and the GenesisConnect board.
See the *Add Support Boards* section for an example of how to add these to the Support menu.

You can also add a reference to another board via the `gabp_support_boards` filter.

= What Settings links are supported? =

* Design Settings (for Prose Theme)
* GenesisConnect
* Simple Edits
* Simple Hooks
* Simple Sidebars
* Simple URLs

A plugin author can add support for their own settings page link. See the *Add Custom Items* section.

== Screenshots ==

1. Support menu expanded, to show 3 optional items added via theme `functions.php` using `add_theme_support()`
2. Codex menu expanded
3. StudioPress menu expanded, along with FAQ submenu
4. Settings menu expanded, showing support for direct link to settings pages of several Genesis-related plugins.
5. Showing the StudioPress menu item removed, and new custom menu items added.

== Changelog ==

= 1.1.1 =
* Added option to enter GABP Debug mode by appending `gabp-debug` as querystring argument.
* Fixed translation files by renaming them.
* Added a Codex suggestion, to check for translation files in `wp-content/languages/` first.

= 1.1 =
* Improved menu position - now each sub menu can start numbering items from 0, as child menu item will automatically be given a minimum position value of its parent.
* Added debug mode (uncomment line at top of plugin file). Can be used to show calculated menu position.

= 1.0.1 =
* Added further checks to see if plugin is active.
* Improved inconsistent external link icon by replacing CSS Unicode characters with base64 encoded image.
* Included .pot file for translations.
* Added de_DE translation files (props [deckerweb.de](http://deckerweb.de/material/sprachdateien/genesis-plugins/)).

= 1.0 =
* First public version.

== Upgrade Notice ==

= 1.1 =
Improved menu position calculation, added debug mode.

= 1.0.1 =
Minor changes - improved external link indicator, translation improvements.

= 1.0 =
Update from nothingness. You will feel better for it.

== Add Custom Items ==

Here's an example which removes the StudioPress menu (you only need to remove the parent item to remove all of the child items too), moves the Support menu item to the bottom of the submenu and adds some custom menu items in:

`add_action( 'gabp_menu_items', 'child_gabp_menu_items', 10, 3 );
/**
 * Amend the menu items in the Genesis Admin Bar Plus plugin.
 *
 * @param Genesis_Admin_Bar_Plus_Menu $menu
 * @param string $prefix
 * @param string $genesis
 */
function child_gabp_menu_items( $menu, $prefix, $genesis ) {
	$garyjones = $prefix . 'gary-jones';

	// Remove StudioPress item
	$menu->remove_item('studiopress');

	// Add Gary Jones item
	$menu->add_item( 'gary-jones', array(
		'parent'   => $genesis,
		'title'    => 'Gary Jones',
		'href'     => 'http://garyjones.co.uk/',
		'position' => 30
	) );

	// Add Gary Jones submenu items
	$menu->add_item( 'code-gary-jones', array(
		'parent'   => $garyjones,
		'title'    => 'Code Gallery',
		'href'     => 'http://code.garyjones.co.uk/',
		'position' => 10
	) );
	$menu->add_item( 'garyj', array(
		'parent'   => $garyjones,
		'title'    => 'GaryJ',
		'href'     => 'http://twitter.com/GaryJ',
		'position' => 20
	) );

	// Amend position of Support menu item - child items will move correctly too
	// as of v1.1
	$menu->edit_item( 'support', array(
		'position' => 50
	) );
}`

== Add Support Boards ==

To a add a reference to a support board (perhaps for the child theme the active theme is based on, or a plugin the site uses, etc), you can add something like one of the following to the child theme `functions.php` file.
`add_theme_support('gabp-support-genesis'); // Adds direct link to Genesis support board
add_theme_support('gabp-support-pretty-young-thing'); // Adds link to Pretty Young Thing child theme support board
add_theme_support('gabp-support-prose');  // Adds link to Prose child theme support board
add_theme_support('gabp-support-focus'); // Adds link to Focus child theme support board
add_theme_support('gabp-support-translations'); // Adds direct link to Genesis Translations support board
add_theme_support('gabp-support-plugins'); // Adds direct link to StudioPress Plugins support board
add_theme_support('gabp-support-genesisconnect'); // Adds direct link to GenesisConnect support board`
For child themes, the bit after the `gabp-support-` string must be the theme name, lowercase, with spaces replaced with hyphens.