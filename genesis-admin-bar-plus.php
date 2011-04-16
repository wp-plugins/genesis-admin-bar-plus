<?php
/**
 * Main plugin file. The plugin adds resources links related the Genesis Framework to the admin bar.
 *
 * @package GenesisAdminBarPlus
 * @author Gary Jones
 */
/*
Plugin Name: Genesis Admin Bar Plus
Version: 1.1.2
Plugin URI: http://code.garyjones.co.uk/plugins/genesis-admin-bar-plus/
Description: The plugin adds resources links related the Genesis Framework to the admin bar. It is a complete rewrite, effectively forked from <a href="http://profiles.wordpress.org/users/DeFries/">DeFries</a>' <a href="http://wordpress.org/extend/plugins/genesis-admin-bar-addition/">Genesis Admin Bar Addition</a>. See the readme for how to add specific support boards and other items to the menu.
Author: Gary Jones
Author URI: http://garyjones.co.uk/
*/

/**
 * Uncomment to activate debug mode for this plugin.
 *
 * Currently all this does is add the calculated (given child position + parent
 * position) menu position to the visible menu item.
 *
 * @since 1.1
 */
//define ( 'GABP_DEBUG', true );

/**
 * The translation gettext domain for the plugin.
 *
 * @since 1.0
 */
define( 'GABP_DOMAIN', 'genesis-admin-bar-plus' );

/**
 * Ensure plugin is translatable.
 *
 * @since 1.0
 */
if( ! load_plugin_textdomain( GABP_DOMAIN, '/wp-content/languages/' ) )
	load_plugin_textdomain( GABP_DOMAIN, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

add_action( 'init', 'genesis_admin_bar_plus' );
/**
 * Initialise the class, once theme and plugin data is available.
 *
 * @since 1.0
 * @uses Genesis_Admin_Bar_Plus Main plugin class.
 */
function genesis_admin_bar_plus() {

	$genesis_admin_bar_plus = new Genesis_Admin_Bar_Plus;

}

/**
 * Main plugin class. Adds Genesis-related resource links to the admin bar
 * present in WordPress 3.1 and above.
 *
 * @since 1.0
 */
class Genesis_Admin_Bar_Plus {

	/**
	 * Prefix to ensure IDs are unique.
	 *
	 * @var string
	 */
	var $prefix = 'genesis-admin-bar-plus-';

	/**
	 * @var Genesis_Admin_Bar_Plus
	 */
	var $menu;

	/**
	 * @var Array Menu Items collection once finally pulled from Genesis_Admin_Bar_Plus
	 */
	var $menu_items = array();

	/**#@+
	 * Create parent menu item references.
	 *
	 * @var type
	 */
	var $genesis;
	var $support;
	var $dev;
	var $studiopress;
	var $settings;
	/**#@-*/

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	function genesis_admin_bar_plus() {

		// Populate parent menu references
		$this->genesis     = $this->prefix . 'genesis';
		$this->support     = $this->prefix . 'support';
		$this->dev         = $this->prefix . 'dev';
		$this->studiopress = $this->prefix . 'studiopress';
		$this->faqs        = $this->prefix . 'faqs';
		$this->settings    = $this->prefix . 'settings';

		// Create menu items holder and populate with default items
		$this->menu = new Genesis_Admin_Bar_Plus_Menu;

		// Hook style and menu items in
		add_action( 'wp_head', array( &$this, 'style' ) );
		add_action( 'admin_head', array( &$this, 'style' ) );
		add_action( 'admin_bar_menu', array( &$this, 'set_default_menu_items' ), 95 );
		add_action( 'admin_bar_menu', array( &$this, 'add_menus' ), 96 );

	}

	/**
	 * Populate the $menu property with the default menu items for the plugin.
	 *
	 * This includes the Genesis Support item, Genesis Codex menu and submenu
	 * items, StudioPress menu and submenu items, and if Genesis or a child
	 * theme is active, a Settings menu with Theme and SEO items, along with
	 * other items, depending on what child theme and Genesis-related plugins
	 * are active.
	 *
	 * @since 1.0
	 */
	function set_default_menu_items() {

		$menu = $this->menu;

		// Add top-level Genesis item
		$menu->add_item( 'genesis', array(
			'title'    => __( 'Genesis', GABP_DOMAIN ),
			'href'     => admin_url( '' ),
			'position' => 0,
			'meta'     => array( 'class' => 'icon-genesis', 'target' => '' )
		) );

		// Add Genesis menu items
		$menu->add_item( 'support', array(
			'parent'   => $this->genesis,
			'title'    => __( 'Genesis Support', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/support',
			'position' => 10
		) );
		$menu->add_item( 'dev', array(
			'parent'   => $this->genesis,
			'title'    => __( 'Genesis Codex', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/',
			'position' => 20
		) );
		$menu->add_item( 'studiopress', array(
			'parent'   => $this->genesis,
			'title'    => __( 'StudioPress', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/',
			'position' => 30
		) );

		// Add Support submenu items
		$boards = $this->get_support_boards();
		$i = 0;
		foreach( $boards as $key => $board ) {
			if ( current_theme_supports( 'gabp-support-' . $key ) ) {
				$menu->add_item( $key . '-support', array(
					'parent'   => $this->support,
					'title'    => $board[0],
					'href'     => 'http://www.studiopress.com/support/forumdisplay.php?f=' . $this->get_support_board( $key ),
					'position' => 10 + 2 * $i
				) );
			}
			$i++;
		}

		// Add Codex / Dev submenu items
		$menu->add_item( 'sitemap', array(
			'parent'   => $this->dev,
			'title'    => __( 'Dev.StudioPress Sitemap', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/sitemap',
			'position' => 10
		) );
		$menu->add_item( 'hooks', array(
			'parent'   => $this->dev,
			'title'    => __( 'Action Hooks Reference', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/hook-reference',
			'position' => 20
		) );
		$menu->add_item( 'filters', array(
			'parent'   => $this->dev,
			'title'    => __( 'Filter Hooks Reference', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/filter-reference',
			'position' => 30
		) );
		$menu->add_item( 'functions', array(
			'parent'   => $this->dev,
			'title'    => __( 'Functions Reference', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/function-reference',
			'position' => 40
		) );
		$menu->add_item( 'shortcodes', array(
			'parent'   => $this->dev,
			'title'    => __( 'Shortcodes Reference', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/shortcode-reference',
			'position' => 50
		) );
		$menu->add_item( 'visual', array(
			'parent'   => $this->dev,
			'title'    => __( 'Visual Markup Guide', GABP_DOMAIN ),
			'href'     => 'http://dev.studiopress.com/visual-markup-guide',
			'position' => 60
		) );

		// Add StudioPress submenu items
		$menu->add_item( 'themes', array(
			'parent'   => $this->studiopress,
			'title'    => __( 'Themes', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/themes',
			'position' => 10
		) );
		$menu->add_item( 'plugins', array(
			'parent'   => $this->studiopress,
			'title'    => __( 'Plugins', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/plugins',
			'position' => 20
		) );
		$menu->add_item( 'faqs', array(
			'parent'   => $this->studiopress,
			'title'    => __( '<abbr title="Frequently asked question">FAQ</abbr>s', GABP_DOMAIN ),
			'href'     => '#',
			'position' => 30,
			'meta'     => array( 'target' => '' )
		) );

		// Add FAQs sub-submenu items
		$menu->add_item( 'general-faqs', array(
			'parent'   => $this->faqs,
			'title'    => __( 'General <abbr>FAQ</abbr>s', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/general-faqs',
			'position' => 10
		) );
		$menu->add_item( 'support-faqs', array(
			'parent'   => $this->faqs,
			'title'    => __( 'Support <abbr>FAQ</abbr>s', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/support-faqs',
			'position' => 20
		) );
		$menu->add_item( 'theme-faqs', array(
			'parent'   => $this->faqs,
			'title'    => __( 'Theme <abbr>FAQ</abbr>s', GABP_DOMAIN ),
			'href'     => 'http://www.studiopress.com/theme-faqs',
			'position' => 30
		) );

		// Add Settings menu only if Genesis or a child theme is active
		if ( defined( 'GENESIS_SETTINGS_FIELD' ) ) {

			// Add Settings menu item
			$menu->add_item( 'settings', array(
				'parent'   => $this->genesis,
				'title'    => __( 'Settings', 'genesis' ),
				'href'     => is_admin() ? menu_page_url( 'genesis', false ) : admin_url( add_query_arg( 'page', 'genesis', 'admin.php' ) ),
				'position' => 40,
				'meta'     => array( 'target' => '' )
			) );

			// Add Settings submenu items
			$menu->add_item( 'theme-settings', array(
				'parent'   => $this->settings,
				'title'    => __( 'Theme Settings', 'genesis' ),
				'href'     => is_admin() ? menu_page_url( 'genesis', false ) : admin_url( add_query_arg( 'page', 'genesis', 'admin.php' ) ),
				'position' => 10,
				'meta'     => array( 'target' => '' )
			) );
			$menu->add_item( 'seo-settings', array(
				'parent'   => $this->settings,
				'title'    => __( 'SEO Settings', 'genesis' ),
				'href'     => is_admin() ? menu_page_url( 'seo-settings', false ) : admin_url( add_query_arg( 'page', 'seo-settings', 'admin.php' ) ),
				'position' => 20,
				'meta'     => array( 'target' => '' )
			) );

			// Add Prose Design Settings if Prose is active
			if ( defined( 'PROSE_DOMAIN' ) ) {
				$menu->add_item( 'design-settings', array(
					'parent'   => $this->settings,
					'title'    => __( 'Design Settings', PROSE_DOMAIN ),
					'href'     => is_admin() ? menu_page_url( 'design-settings', false ) : admin_url( add_query_arg( 'page', 'design-settings', 'admin.php' ) ),
					'position' => 30,
					'meta'     => array( 'target' => '' )
				) );
			}

			// Add GenesisConnect Settings if active
			if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'genesisconnect/genesisconnect.php' ) ) || function_exists( 'genesisconnect_init' ) ) {
				$menu->add_item( 'genesisconnect', array(
					'parent'   => $this->settings,
					'title'    => __( 'GenesisConnect', GABP_DOMAIN ),
					'href'     => is_admin() ? menu_page_url( 'connect-settings', false ) : admin_url( add_query_arg( 'page', 'connect-settings', 'admin.php' ) ),
					'position' => 40,
					'meta'     => array( 'target' => '' )
				) );
			}

			// Add Simple Edits Settings if active
			if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'genesis-simple-edits/plugin.php' ) ) || defined( 'GSE_SETTINGS_FIELD' ) ) {
				$menu->add_item( 'simple-edits', array(
					'parent'   => $this->settings,
					'title'    => __( 'Simple Edits', GABP_DOMAIN ),
					'href'     => is_admin() ? menu_page_url( 'genesis-simple-edits', false ) : admin_url( add_query_arg( 'page', 'genesis-simple-edits', 'admin.php' ) ),
					'position' => 50,
					'meta'     => array( 'target' => '' )
				) );
			}

			// Add Simple Hooks Settings if active
			if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'genesis-simple-hooks/plugin.php' ) ) || defined( 'SIMPLEHOOKS_SETTINGS_FIELD' ) ) {
				$menu->add_item( 'simple-hooks', array(
					'parent'   => $this->settings,
					'title'    => __( 'Simple Hooks', GABP_DOMAIN ),
					'href'     => is_admin() ? menu_page_url( 'simplehooks', false ) : admin_url( add_query_arg( 'page', 'simplehooks', 'admin.php' ) ),
					'position' => 60,
					'meta'     => array( 'target' => '' )
				) );
			}

			// No Simple Menus, as it has no settings page.

			// Add Simple Sidebars Settings if active
			if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'genesis-simple-sidebars/plugin.php' ) ) || defined( 'SS_SETTINGS_FIELD' ) ) {
				$menu->add_item( 'simple-sidebars', array(
					'parent'   => $this->settings,
					'title'    => __( 'Simple Sidebars', GABP_DOMAIN ),
					'href'     => is_admin() ? menu_page_url( 'simple-sidebars', false ) : admin_url( add_query_arg( 'page', 'simple-sidebars', 'admin.php' ) ),
					'position' => 70,
					'meta'     => array( 'target' => '' )
				) );
			}

			// Add Simple URLs Settings if active
			if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'simple-urls/plugin.php' ) ) || class_exists( 'SimpleURLs' ) ) {
				$menu->add_item( 'simple-urls', array(
					'parent'   => $this->settings,
					'title'    => __( 'Simple URLs', GABP_DOMAIN ),
					'href'     => admin_url( add_query_arg( 'post_type', 'surl', 'edit.php' ) ),
					'position' => 80,
					'meta'     => array( 'target' => '' )
				) );
			}

			do_action( 'gabp_menu_items', $menu, $this->prefix, $this->genesis, $this->support, $this->dev, $this->studiopress, $this->settings, $this->faqs );
		}

	}

	/**
	 * Ensure that child item has a minimum position equal to that of its parent. Private
	 *
	 * @since 1.1
	 *
	 * @param array $menu_items
	 * @return array
	 */
	function _pre_sort() {

		foreach ( $this->menu_items as $id => $menu_item ) {
			if ( isset( $menu_item['parent'] ) ) {
				$parent = $this->menu->get_item( str_replace($this->prefix, '', $menu_item['parent'] ) );
				$this->menu_items[$id]['position'] += $parent['position'];
			}
		}

	}

	/**
	 * Helper function to sort the menu items by position. Private.
	 *
	 * @since 1.0
	 * @todo Try and find some way of sorting children after parent ID first
	 */
	function _sort( $a, $b ) {

		$ap = (int) $a['position'];
		$bp = (int) $b['position'];

		if ( $ap == $bp ) {
            return 0;
        }
        return ( $ap > $bp ) ? +1 : -1;

	}

	/**
	 * Add the menus to the admin bar.
	 *
	 * The menu items are filterable via the 'genesis_admin_bar_plus_menu_items'
	 * filter.
	 *
	 * @since 1.0
	 * @uses Genesis_Admin_Bar_Plus::sort() Helper function for uasort()
	 * @uses Genesis_Admin_Bar_Plus_Menu::get_items() Return default menu items
	 * @uses validate_child_item_position() Pre-sort menu items
	 *
	 * @global WP_Admin_Bar $wp_admin_bar
	 */
	function add_menus() {

		global $wp_admin_bar;

		// Allow menu items to be filtered, but pass in parent menu item IDs
		$this->menu_items = (array) apply_filters( 'genesis_admin_bar_plus_menu_items', $this->menu->get_items(), $this->prefix, $this->genesis, $this->support, $this->dev, $this->studiopress, $this->settings );

		// Ensure that child item has a minimum position equal to that of its parent.
		$this->_pre_sort();

		// Final sort by position
		uasort( $this->menu_items, array( &$this, '_sort' ) );

		// Loop through menu items
		foreach ( $this->menu_items as $id => $menu_item ) {

			// Add in item ID
			$menu_item['id'] = $this->prefix . $id;

			// Add meta target to each item where it's not already set, so links open in new tab
			if ( ! isset( $menu_item['meta']['target'] ) )
				$menu_item['meta']['target'] = '_blank';

			// Each menu item position is calculated by the given child item position + parent item position.
			// This ensures that when sorted, the parent item will always be before the child item, so
			// the child item has something to be a child of, when added.
			if ( $this->is_debug() )
				$menu_item['title'] .= ' <small title="menu-position" class="gabp-debug">(' . $menu_item['position'] . ')</small>';

			// Add item
			$wp_admin_bar->add_menu( $menu_item );
		}

	}

	/**
	 * A theme can link to one of these support boards by adding:
	 *   add_theme_support( 'gabp-support-X' );
	 * to the theme, where X is one of the keys below ('genesis', 'agency', etc)
	 *
	 * The key must be lowercase, and use hyphen for spaces e.g.
	 *   add_theme_support( 'gabp-support-pretty-young-thing' );
	 *
	 * @since 1.0
	 *
	 * @return array Array of support boards.
	 */
	function get_support_boards() {

		$boards = array(
			'genesis'            => array( __( 'Genesis Framework', GABP_DOMAIN ), 75 ),
			'agency'             => array( __( 'Agency Child Theme', GABP_DOMAIN ), 119 ),
			'agentpress'         => array( __( 'AgentPress Child Theme', GABP_DOMAIN ), 86 ),
			'amped'              => array( __( 'Amped Child Theme', GABP_DOMAIN ), 93 ),
			'beecrafty'          => array( __( 'BeeCrafty Child Theme', GABP_DOMAIN ), 138 ),
			'church'             => array( __( 'Church Child Theme', GABP_DOMAIN ), 124 ),
			'corporate'          => array( __( 'Corporate Child Theme', GABP_DOMAIN ), 109 ),
			'crystal'            => array( __( 'Crystal Child Theme', GABP_DOMAIN ), 160 ),
			'delicious'          => array( __( 'Delicious Child Theme', GABP_DOMAIN ), 130 ),
			'education'          => array( __( 'Education Child Theme', GABP_DOMAIN ), 126 ),
			'enterprise'         => array( __( 'Enterprise Child Theme', GABP_DOMAIN ), 102 ),
			'executive'          => array( __( 'Executive Child Theme', GABP_DOMAIN ), 79 ),
			'expose'             => array( __( 'Expose Child Theme', GABP_DOMAIN ), 136 ),
			'family-tree'        => array( __( 'Family Tree Child Theme', GABP_DOMAIN ), 100 ),
			'focus'              => array( __( 'Focus Child Theme', GABP_DOMAIN ), 167 ),
			'freelance'          => array( __( 'Freelance Child Theme', GABP_DOMAIN ), 121 ),
			'going-green'        => array( __( 'Going Green Child Theme', GABP_DOMAIN ), 116 ),
			'landscape'          => array( __( 'Landscape Child Theme', GABP_DOMAIN ), 108 ),
			'lexicon'            => array( __( 'Lexicon Child Theme', GABP_DOMAIN ), 146 ),
			'lifestyle'          => array( __( 'Lifestyle Child Theme', GABP_DOMAIN ), 92 ),
			'magazine'           => array( __( 'Magazine Child Theme', GABP_DOMAIN ), 128 ),
			'manhattan'          => array( __( 'Manhattan Child Theme', GABP_DOMAIN ), 152 ),
			'metric'             => array( __( 'Metric Child Theme', GABP_DOMAIN ), 114 ),
			'mocha'              => array( __( 'Mocha Child Theme', GABP_DOMAIN ), 80 ),
			'news'               => array( __( 'News Child Theme', GABP_DOMAIN ), 118 ),
			'outreach'           => array( __( 'Outreach Child Theme', GABP_DOMAIN ), 112 ),
			'pixel-happy'        => array( __( 'Pixel Happy Child Theme', GABP_DOMAIN ), 87 ),
			'platinum'           => array( __( 'Platinum Child Theme', GABP_DOMAIN ), 73 ),
			'pretty-young-thing' => array( __( 'Pretty Young Thing Child Theme', GABP_DOMAIN ), 166 ),
			'prose'              => array( __( 'Prose Child Theme', GABP_DOMAIN ), 147 ),
			'serenity'           => array( __( 'Serenity Child Theme', GABP_DOMAIN ), 84 ),
			'sleek'              => array( __( 'Sleek Child Theme', GABP_DOMAIN ), 132 ),
			'social-eyes'        => array( __( 'Social Eyes Child Theme', GABP_DOMAIN ), 165 ),
			'streamline'         => array( __( 'Streamline Child Theme', GABP_DOMAIN ), 81 ),
			'tapestry'           => array( __( 'Tapestry Child Theme', GABP_DOMAIN ), 154 ),
			'venture'            => array( __( 'Venture Child Theme', GABP_DOMAIN ), 149 ),
			'translations'       => array( __( 'Genesis Translations', GABP_DOMAIN ), 168 ),
			'plugins'            => array( __( 'StudioPress Plugins', GABP_DOMAIN ), 142 ),
			'genesisconnect'     => array( __( 'GenesisConnect', GABP_DOMAIN ), 155 )
		);
		return (array) apply_filters( 'gabp_support_boards', $boards );

	}

	/**
	 * Return single forum ID from array of support boards. If name not found,
	 * returns false.
	 *
	 * @since 1.0
	 *
	 * @param string $name Lowercase, hyphen-spaced theme name, e.g. family-tree.
	 * @return integer|boolean Support board ID, or false if board not found.
	 */
	function get_support_board( $name ) {

		$boards = $this->get_support_boards();
		if ( isset( $boards[$name] ) )
			return $boards[$name][1];
		return false;

	}

	/**
	 * Add inline style to front and back-end pages (as WP does) if admin bar is
	 * showing.
	 *
	 * Most of the CSS here is for modern browsers - the use of attribute
	 * selectors, child selectors, generated content and so on will likely kill
	 * semi-older browsers, but the effects here (adding a "new tab" indicator)
	 * are non-critical.
	 *
	 * @since 1.0
	 */
	function style() {

		if ( ! is_admin_bar_showing() )
			return;

		?><style type="text/css">
			#wpadminbar a[target=_blank]:after,
			#wpadminbar .menupop a[target=_blank] span:after {
				background-image: url(data:image/gif;base64,R0lGODlhBwAIAKIEAM7Ozt7e3u/v7729rf///wAAAAAAAAAAACH5BAEAAAQALAAAAAAHAAgAAAMUSEoCsyqAOQMDERPMld7eolGTkgAAOw%3D%3D);
				display: inline-block;
				content: "";
				height: 8px;
				margin-bottom: 1px;
				margin-left: 5px;
				width: 7px;
			}
			#wpadminbar .menupop>a[target=_blank]:after {
				display: none;
			}
			<?php
			if ( defined( 'GENESIS_SETTINGS_FIELD' ) ) {
			?>
			#wpadminbar .icon-genesis>a {
				background: url(<?php echo PARENT_URL; ?>/images/genesis.gif) no-repeat 0.85em 50%;
			}
			#wpadminbar .icon-genesis>a span {
				padding-left: 20px;
			}
			<?php } ?>
		</style>
		<?php

	}

	/**
	 * Returns if debug mode is activated for this plugin.
	 *
	 * Can be activated by uncommenting the line near the top of this file.
	 *
	 * @since 1.1
	 *
	 * @return boolean
	 */
	function is_debug() {

		if (  ( defined( 'GABP_DEBUG') && GABP_DEBUG ) || ( isset( $_GET['gabp-debug'] ) ) )
			return true;
		return false;

	}

}

/**
 * Container for the menu items.
 *
 * @since 1.0
 */
class Genesis_Admin_Bar_Plus_Menu {

	/**
	 * Holds menu items. Private.
	 *
	 * @var array
	 */
	var $menu_items = array();

	/**
	 * Assign the menu item to the array using the ID as the key. Public.
	 *
	 * @since 1.0
	 *
	 * @param string $id Menu item identifier
	 * @param array $args Menu item arguments
	 */
	function add_item( $id, $args ) {

		$this->menu_items[$id] = $args;

	}

	/**
	 * Retrieve single menu item.
	 *
	 * @since 1.1
	 *
	 * @param string $id Menu item identifier
	 * @return array Menu item arguments
	 */
	function get_item( $id ) {

		if( isset( $this->menu_items[$id] ) )
			return $this->menu_items[$id];
		return false;

	}

	/**
	 * Edit the menu item arguments, merging with the existing values. Public.
	 *
	 * @since 1.0
	 *
	 * @param string $id Menu item identifier
	 * @param array $args Menu item arguments
	 */
	function edit_item( $id, $args ) {

		$this->menu_items[$id] = wp_parse_args( $args, $this->menu_items[$id] );

	}

	/**
	 * Remove the menu item from the array using the ID as the key. Public.
	 *
	 * @since 1.0
	 *
	 * @param string $id Menu item identifier
	 */
	function remove_item( $id ) {

		if( isset( $this->menu_items[$id] ) )
			unset( $this->menu_items[$id] );

	}

	/**
	 * Return the array of menu items. Public.
	 *
	 * @since 1.0
	 *
	 * @return array All menu items
	 */
	function get_items() {

		return $this->menu_items;

	}

}