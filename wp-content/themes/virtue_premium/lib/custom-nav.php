<?php 

class kt_custom_menu {
  /*--------------------------------------------*
   * Constructor
   *--------------------------------------------*/

	/**
	* Initializes the plugin by setting localization, filters, and administration functions.
	*/
	function __construct() {
		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'kt_add_nav_fields' ) );

		// add new fields via hook
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'kt_custom_nav_fields' ), 10, 4 );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( $this, 'kt_update_nav_fields'), 10, 3 );

		// edit menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'kt_edit_admin_walker'), 10, 2 );

	} // end constructor


  /**
   * Add custom fields to $item nav object
   * in order to be used in custom Walker
   *
   * @access      public
   * @since       1.0 
   * @return      void
  */
  function kt_add_nav_fields( $menu_item ) {
  
      $menu_item->ktlgmenu = get_post_meta( $menu_item->ID, '_menu_item_ktlgmenu', true );
      $menu_item->ktcolumnmenu = get_post_meta( $menu_item->ID, '_menu_item_ktcolumnmenu', true );
      $menu_item->kticonmenu = get_post_meta( $menu_item->ID, '_menu_item_kticonmenu', true );

      return $menu_item;
      
  }
  /**
  * Add fields to hook added in Walker
  * This will allow us to play nicely with any other plugin that is adding the same hook
  */
  public function kt_custom_nav_fields( $item_id, $item, $depth, $args ) {

    ?>

              <p class="field-ktlgmenu ktlgmenu description-wide ktlgmenu-wide" style="clear:both">
                <label for="edit-menu-item-ktlgmenu-<?php echo esc_attr($item->ID); ?>">
                   <input type="checkbox" id="edit-menu-item-ktlgmenu-<?php echo esc_attr($item->ID); ?>" value="enabled" name="menu-item-ktlgmenu[<?php echo esc_attr($item->ID); ?>]"  <?php checked( $item->ktlgmenu, 'enabled' ); ?>  />
                    <?php _e( 'Enable Fullwidth Sub-Menu', 'virtue' ); ?>
                </label>
              </p>
              <p class="field-ktcolumnmenu description-wide ktcolumnmenu ktcolumnmenu-wide" style="clear:both">
                  <label for="edit-menu-item-ktcolumnmenu-<?php echo esc_attr($item->ID); ?>">
                      <?php _e( 'Submenu Number of Columns', 'virtue'); ?>
                      <select id="edit-menu-item-ktcolumnmenu-<?php echo esc_attr($item->ID); ?>" class="widefat code edit-menu-item-ktcolumnmenu" name="menu-item-ktcolumnmenu[<?php echo esc_attr($item->ID); ?>]">
                        <option value="2" <?php selected( $item->ktcolumnmenu, '2' ); ?>>2</option>
                        <option value="3" <?php selected( $item->ktcolumnmenu, '3' ); ?>>3</option>
                        <option value="4" <?php selected( $item->ktcolumnmenu, '4' ); ?>>4</option>
                      </select>
                  </label>
              </p>
    <?php
  }
  /**
   * Save menu custom fields
   *
   * @access      public
   * @since       1.0 
   * @return      void
  */
  function kt_update_nav_fields( $menu_id, $menu_item_db_id, $args ) {
  
      // Check if element is properly sent
    if( !isset( $_REQUEST['menu-item-ktlgmenu'][$menu_item_db_id] ) ) {
          $_REQUEST['menu-item-ktlgmenu'][$menu_item_db_id] = '';
        }
      $value = $_REQUEST['menu-item-ktlgmenu'][$menu_item_db_id];
          update_post_meta( $menu_item_db_id, '_menu_item_ktlgmenu', $value );
      
      if( !isset( $_REQUEST['menu-item-ktcolumnmenu'][$menu_item_db_id] ) ) {
          $_REQUEST['menu-item-ktcolumnmenu'][$menu_item_db_id] = '';
        }
      $valuecolumn = $_REQUEST['menu-item-ktcolumnmenu'][$menu_item_db_id];
          update_post_meta( $menu_item_db_id, '_menu_item_ktcolumnmenu', $valuecolumn );

     if( !isset( $_REQUEST['menu-item-kticonmenu'][$menu_item_db_id] ) ) {
          $_REQUEST['menu-item-kticonmenu'][$menu_item_db_id] = '';
        }
      $valueicon = $_REQUEST['menu-item-kticonmenu'][$menu_item_db_id];
          update_post_meta( $menu_item_db_id, '_menu_item_kticonmenu', $valueicon );
      
  }
  
	/**
	* Define new Walker edit
	*
	* @access      public
	* @since       1.0 
	* @return      void
	*/
	function kt_edit_admin_walker( $walker, $menu_id ) {

		return 'Walker_Nav_Menu_KT_Custom';

	}

}
// instantiate plugin's class
$GLOBALS['kt_custom_menu'] = new kt_custom_menu();

class Walker_Nav_Menu_KT_Custom extends Walker_Nav_Menu {
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker_Nav_Menu::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker_Nav_Menu::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Start the element output.
     *
     * @see Walker_Nav_Menu::start_el()
     * @since 3.0.0
     *
     * @global int $_wp_nav_menu_max_depth
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     * @param int    $id     Not used.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = get_the_title( $original_object->ID );
        } elseif ( 'post_type_archive' == $item->type ) {
            $original_object = get_post_type_object( $item->object );
            $original_title = $original_object->labels->archives;
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( __( '%s (Invalid)' , 'virtue' ), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( __('%s (Pending)', 'virtue' ), $item->title );
        }

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

        $submenu_text = '';
        if ( 0 == $depth )
            $submenu_text = 'style="display: none;"';

        ?>
        <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
            <div class="menu-item-bar">
                <div class="menu-item-handle">
                    <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' , 'virtue' ); ?></span></span>
                    <span class="item-controls">
                        <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                        <span class="item-order hide-if-js">
                            <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-up-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                            ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up', 'virtue' ); ?>">&#8593;</abbr></a>
                            |
                            <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-down-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                            ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down', 'virtue' ); ?>">&#8595;</abbr></a>
                        </span>
                        <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item', 'virtue' ); ?>" href="<?php
                            echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>"><?php _e( 'Edit Menu Item' , 'virtue' ); ?></a>
                    </span>
                </div>
            </div>

            <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
                <?php if ( 'custom' == $item->type ) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                            <?php _e( 'URL' , 'virtue' ); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-wide">
                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                        <?php _e( 'Navigation Label' , 'virtue' ); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                    </label>
                </p>
                <p class="field-title-attribute description description-wide">
                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                        <?php _e( 'Title Attribute' , 'virtue' ); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                        <?php _e( 'Open link in a new tab' , 'virtue' ); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                        <?php _e( 'CSS Classes (optional)' , 'virtue' ); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                        <?php _e( 'Link Relationship (XFN)' , 'virtue' ); ?><br />
                        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                        <?php _e( 'Description' , 'virtue' ); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                        <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', 'virtue' ); ?></span>
                    </label>
                </p>

                <?php 
                // This is the added section
                do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args );
                // end added section 
                ?>

                <p class="field-move hide-if-no-js description description-wide">
                    <label>
                        <span><?php _e( 'Move' , 'virtue' ); ?></span>
                        <a href="#" class="menus-move menus-move-up" data-dir="up"><?php _e( 'Up one' , 'virtue' ); ?></a>
                        <a href="#" class="menus-move menus-move-down" data-dir="down"><?php _e( 'Down one' , 'virtue' ); ?></a>
                        <a href="#" class="menus-move menus-move-left" data-dir="left"></a>
                        <a href="#" class="menus-move menus-move-right" data-dir="right"></a>
                        <a href="#" class="menus-move menus-move-top" data-dir="top"><?php _e( 'To the top' , 'virtue' ); ?></a>
                    </label>
                </p>

                <div class="menu-item-actions description-wide submitbox">
                    <?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
                        <p class="link-to-original">
                            <?php printf( __('Original: %s', 'virtue' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                    echo wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'delete-menu-item',
                                'menu-item' => $item_id,
                            ),
                            admin_url( 'nav-menus.php' )
                        ),
                        'delete-menu_item_' . $item_id
                    ); ?>"><?php _e( 'Remove' , 'virtue' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
                        ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel', 'virtue' ); ?></a>
                </div>

                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }

}
