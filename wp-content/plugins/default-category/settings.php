<?php

/* ------------------------------------------------------------------------ *
 * Create page in menu for settings page
 * ------------------------------------------------------------------------ */

function default_category_plugin_menu() {
 
    add_options_page(
        'Default Category Plugin',           // The title to be displayed in the browser window for this page.
        'Default Category',           // The text to be displayed for this menu item
        'administrator',            // Which type of users can see this menu item
        'options-default-category',   // The unique ID - that is, the slug - for this menu item
        'default_category_page_callback'    // The name of the function to call when rendering the page for this menu
    );
 
} // default_category_plugin_menu
add_action('admin_menu', 'default_category_plugin_menu');

/**
 * Initializes the options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */
add_action('admin_init', 'default_category_initialize_options');
function default_category_initialize_options() {
 
    // First, we register a section. This is necessary since all future options must belong to one.
    add_settings_section(
        'default_category_section',         // ID used to identify this section and with which to register options
        'Default Category Options',                  // Title to be displayed on the administration page
        'default_category_page_section_callback', // Callback used to render the description of the section
        'options-default-category'                           // Page on which to add this section of options
    );

    // Next, we will introduce the fields
    add_settings_field( 
        'default_category_id',                      // ID used to identify the field throughout the theme
        '',                           // The label to the left of the option interface element
        'default_category_id_callback',   // The name of the function responsible for rendering the option interface
        'options-default-category',                          // The page on which this option will be displayed
        'default_category_section',         // The name of the section to which this field belongs
        array(                              // The array of arguments to pass to the callback. In this case, just a description.
            'Activate this setting to display the header.'
        )
    );

    // Finally, we register the fields with WordPress
    register_setting(
        'options-default-category',
        'default_category_id',
        'default_category_id_validate'
    );

} 

/* ------------------------------------------------------------------------ *
 * Section Callbacks
 * ------------------------------------------------------------------------ */
/*
 * HTML render section
 */
function default_category_page_callback() {
  ?>
  <form method="POST" action="options.php">
  <?php settings_fields( 'options-default-category' ); //pass slug name of page, also referred
                                          //to in Settings API as option group name
  do_settings_sections( 'options-default-category' );  //pass slug name of page

  submit_button();
  ?>
  </form>
<?php
}

/*
 * Default section
 * Render checkboxes
 */
function default_category_page_section_callback() {
  echo '<p>Select the default category for new posts. Multiple categories can be selected.</p>';
  $default_category_id = get_option('default_category_id');

  echo '<ul>';
  wp_category_checklist(0,0, $default_category_id['default_category_id']);
  echo '</ul>';
}

/*
 * Render category id field
 */
function default_category_id_callback() {
  ?><input type="hidden" value="<?php echo get_option('default_category_id'); ?>" />
  <?php
}

/*
 * Validate category id field
 * This is where the magic happens
 * The values from the category checkboxes are saved as an array in our custom option
 */
function default_category_id_validate($input) {
  $input['default_category_id'] = $_POST['post_category'];

  return $input;
}

?>