<?php
/**
 * Plugin Name: SuitePlugins - Advanced XProfile Fields for BuddyPress
 * Plugin URI:  http://suiteplugins.com
 * Description: Enhance your BuddyPress profile fields with Advanced XProfile Fields for BuddyPress. Manage fields labels, validation and show fields in admin.
 * Author:      SuitePlugins
 * Author URI:  http://suiteplugins.com
 * Version:     1.0.3
 * Text Domain: sp-advanced-xprofile
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 *
 */
// If this file is called directly, abort.
if (! defined('WPINC') ) {
    die;
}
/**
 *  XProfile Advanced Labels
 */
if(	! class_exists('SP_Advanced_XProfile') ) :
    /**
    *
    */
    class SP_Advanced_XProfile
    {
        /**
         * [$id description]
         * @var [type]
         */
        public $id;

        protected static $_instance = null;
        /**
    	 * Main UM_Gallery Instance
    	 *
    	 * Ensures only one instance of UM_Gallery is loaded or can be loaded.
    	 *
    	 * @static
    	 * @return UM_Gallery - Main instance
    	 */
    	public static function instance() {
    		if ( is_null( self::$_instance ) )
    			self::$_instance = new self();
    		return self::$_instance;
    	}
    	/**
    	 * Initiate construct
    	 */
        public function __construct()
        {
            $this->id = 'sp_xprofile';
            if( bp_is_active( 'xprofile' ) ) {
                add_action('xprofile_field_after_contentbox', array( $this, 'add_content_box' ), 12, 1);
                add_action('xprofile_fields_saved_field', array( $this, 'save_options' ), 12, 1);
                add_filter('bp_get_the_profile_field_name', array( $this, 'sp_replace_labels' ), 12, 1);
                add_filter('bp_has_profile', array( $this, 'sp_make_noneditable' ), 12, 2);
                add_filter('bp_has_profile', array( $this, 'sp_hide_registration_fields' ), 12, 2);
                add_action('bp_after_profile_edit_content', array( $this, 'add_validation' ));
				add_action('bp_after_register_page', array( $this, 'add_validation' ));
                add_filter( 'manage_users_columns', array( $this, 'add_bp_field_colums' ) );
                add_action( 'manage_users_custom_column', array( $this, 'bp_field_column_content' ), 10, 3);
            }
        }
        /**
         * [field_value description]
         * @param  [type] $value [description]
         * @return [type]        [description]
         */
        public function field_value( $value )
        {
            if ( ! empty( $value ) ) {
                return sanitize_text_field( $value );
            }
        }
        /**
         * [add_content_box description]
         * @param [type] $field [description]
         */
        public function add_content_box( $field )
        {
            do_action('sp_before_advanced_xprofile_fields', $field);
            $labels = bp_xprofile_get_meta($field->id, 'field', $this->id.'_labels');
            $r = wp_parse_args($labels, array(
                'registration' 	=> '',
                'self' 			=> '',
                'user' 			=> '',
                'edit'          => '',
                'admin'         => ''
            ));
            $validation_methods = bp_xprofile_get_meta($field->id, 'field', $this->id.'_validation');
            $v = wp_parse_args($validation_methods, array(
            'enable'        => array(
                'char_limit'    =>  0,
                'min_chars'     =>  0,
                'text_format'   =>  0
            ),
            'char_limit'    => '',
            'min_chars'     => '',
            'text_format'   => ''
            ));

            $option_values = bp_xprofile_get_meta($field->id, 'field', $this->id.'_options');
            $options = wp_parse_args($option_values, array(
                'hide_registration' => '',
                'admin_approval'    => '',
                'admin_column'      => '',
                'non_editable'      => ''
            ));
                ?>
        <style type="text/css">
        .sp_xprofile_item {
            margin-top: 10px;
            background-color: #fafafa;
            border: 1px solid #e5e5e5;
            padding: 10px;
        }
        .sp_xprofile_item label {
            font-weight: bold;
        }
        .sp-advance-xprofile label{
            font-weight: bold;
        }
        .sp-advance-xprofile table {
            width: 100%;
        }
        .sp-advance-xprofile td{
            border-bottom: 1px solid #e5e5e5;
            padding-top: 10px;
        }
        </style>
        <div class="postbox sp-advance-xprofile">
          <h3>
            <label for="default-visibility">
                <?php _e('Labels', 'sp-advanced-xprofile'); ?>
            </label>
          </h3>
          <div class="inside">
            <table>
              <tr>
            <td><label>
                <?php _e('Registration', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="text" name="advanced_xprofile[registration]" value="<?php echo $r['registration']; ?>" />
              <p>
                <?php _e('Label on registration page', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
              <tr>
            <td><label>
                <?php _e('Self Profile', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="text" name="advanced_xprofile[self]" value="<?php echo $r['self']; ?>" />
              <p>
                <?php _e('Label while viewing your profile', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
              <tr>
            <td><label>
                <?php _e('User\'s Profile', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="text" name="advanced_xprofile[user]" value="<?php echo $r['user']; ?>" />
              <p>
                <?php _e('Label while viewing another member\'s page', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
              <tr>
            <td><label>
                <?php _e('Edit Profile', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="text" name="advanced_xprofile[edit]" value="<?php echo $r['edit']; ?>" />
              <p>
                <?php _e('Label on edit profile page', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
              <tr>
            <td><label>
                <?php _e('Admin Column', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="text" name="advanced_xprofile[admin]" value="<?php echo $r['admin']; ?>" />
              <p>
                <?php _e('Admin column title ', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
            </table>
          </div>
          <div class="inside">
            <h3>
              <label for="validation">
            <?php _e('Validation', 'sp-advanced-xprofile'); ?>
              </label>
            </h3>
            <div class="sp-validation-type sp_xprofile_item">
              <label>
            <input type="checkbox" name="advanced_xprofile_validation[enable][char_limit]" value="1" <?php checked($v['enable']['char_limit'], 1, true); ?> />
            <?php _e('Character Limit', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="number" name="advanced_xprofile_validation[char_limit]" placeholder="" value="<?php echo $v['char_limit']; ?>" />
              <p class="description">
            <?php _e('Set the maximum amount of characters for this field.', 'sp-advanced-xprofile'); ?>
              </p>
            </div>
            <div class="sp-validation-type sp_xprofile_item">
              <label>
            <input type="checkbox" name="advanced_xprofile_validation[enable][min_chars]" value="1" <?php checked($v['enable']['min_chars'], 1, true); ?> />
            <?php _e('Minimum Characters', 'sp-advanced-xprofile'); ?>
              </label>
              <input type="number" name="advanced_xprofile_validation[min_chars]" value="<?php echo $v['min_chars']; ?>" />
              <p class="description">
            <?php _e('Set the minimum amount of characters for this field.', 'sp-advanced-xprofile'); ?>
              </p>
            </div>
            <div class="sp-validation-type sp_xprofile_item">
              <label style="display:inline-block">
            <input type="checkbox" name="advanced_xprofile_validation[enable][text_format]" value="1" <?php checked($v['enable']['text_format'], 1, true); ?> />
            <?php _e('Text Format', 'sp-advanced-xprofile'); ?>
              </label>
              <select name="advanced_xprofile_validation[text_format]">
              <option value="0"><?php _e('-Select Format-', 'sp-advanced-xprofile'); ?></option>
              <option value="alphanumeric" <?php echo ($v['text_format']=='alphanumeric' ? 'selected="selected"' : ''); ?>>
                <?php _e('Alphanumeric', 'sp-advanced-xprofile'); ?>
              </option>
              <option value="alpha" <?php echo ($v['text_format']=='alpha' ? 'selected="selected"' : ''); ?>>
                <?php _e('Alpha', 'sp-advanced-xprofile'); ?>
              </option>
              <option value="email" <?php echo ($v['text_format']=='email' ? 'selected="selected"' : ''); ?>>
                <?php _e('Email', 'sp-advanced-xprofile'); ?>
              </option>
              <option value="url" <?php echo ($v['text_format']=='url' ? 'selected="selected"' : ''); ?>>
                <?php _e('URL', 'sp-advanced-xprofile'); ?>
              </option>
            </select>
              <p class="description">
            <?php _e('Choose the text format for an input field.', 'sp-advanced-xprofile'); ?>
              </p>
            </div>
          </div>
          <h3>
            <label for="validation">
                <?php _e('Advanced Options', 'sp-advanced-xprofile'); ?>
            </label>
          </h3>
          <div class="inside">
            <table>
              <tr>
            <td><input type="checkbox" name="advanced_xprofile_options[hide_registration]" value="1" <?php checked($options['hide_registration'], 1, true); ?> />
              <label>
                <?php _e('Hide on registration', 'sp-advanced-xprofile'); ?>
              </label>
              <p>
                <?php _e('Hide field on registration page', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
              <tr>
            <td><input type="checkbox" name="advanced_xprofile_options[non_editable]" value="1" <?php checked($options['non_editable'], 1, true); ?> />
              <label>
                <?php _e('Non editable', 'sp-advanced-xprofile'); ?>
              </label>
              <p>
                <?php _e('Stop profile field from being updated', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
              <tr>
            <td><input type="checkbox" name="advanced_xprofile_options[admin_column]" value="1" <?php checked($options['admin_column'], 1, true); ?> />
              <label>
                <?php _e('Show in Admin Column', 'sp-advanced-xprofile'); ?>
              </label>
              <p>
                <?php _e('Display a column on admin user listing page', 'sp-advanced-xprofile'); ?>
              </p></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- #post-body -->
    <?php
        do_action('sp_after_advanced_xprofile_fields', $field);
        }

        public function save_options($field)
        {
            bp_xprofile_update_field_meta($field->id, $this->id.'_labels', $_POST['advanced_xprofile']);
            bp_xprofile_update_field_meta($field->id, $this->id.'_options', $_POST['advanced_xprofile_options']);
            bp_xprofile_update_field_meta($field->id, $this->id.'_validation', $_POST['advanced_xprofile_validation']);
            //Save Admin Columns
            $user_columns = get_option($this->id.'user_columns');
            if (empty($user_columns)) {
                $user_columns = array();
            }
            if (!empty($_POST['advanced_xprofile_options']['admin_column'])) :
                $user_columns[] = $field->id;
            else :
                if (in_array($field->id, $user_columns)) {
                    if (($key = array_search($field->id, $user_columns)) !== false) {
                        unset($user_columns[$key]);
                    }
                }
            endif;

            update_option($this->id.'user_columns', $user_columns);
        }

        public function sp_replace_labels($name)
        {
            global $field;
            global $bp;
            $labels = bp_xprofile_get_meta($field->id, 'field', $this->id.'_labels');
            $r = wp_parse_args($labels, array(
                'registration'  => $field->name,
                'self'          => $field->name,
                'user'          => $field->name,
                'edit'          => $field->name
            ));
            if ($bp->current_component=='profile' && $bp->current_action=='edit') :
                $name = ($r['edit'] ? $r['edit'] : $name);
            elseif ($bp->current_component=='profile' && $bp->current_action=='public') :
                if ($bp->displayed_user->id == $bp->loggedin_user->id) :
                    $name = ($r['self'] ? $r['self'] : $name);
                else :
                    $name = ($r['user'] ? $r['user'] : $name);
                endif;
            elseif ($bp->current_component=='register') :
                $name = ($r['registration'] ? $r['registration'] : $name);
            endif;
            return $name;
        }

        public function sp_hide_registration_fields($has_groups, $profile_template)
        {
            global $bp;
            $user_id = $profile_template->user_id;
            if ($bp->current_component == 'register') {
                if (!empty($profile_template->groups)) {
                    $keep_group = array();
                    $group_inc = 0;
                    foreach ($profile_template->groups as $group) {
                        if (!empty($group->fields)) {
                                $keep_field = array();
                            foreach ($group->fields as $field) {
                                $option_values = bp_xprofile_get_meta($field->id, 'field', $this->id.'_options');
                                if (empty($option_values['hide_registration']) || $option_values['hide_registration']!=1) :
                                    $keep_field[] = $field;
                                endif;
                            }
                            if (!empty($keep_field)) :
                                $profile_template->groups[$group_inc]->fields = $keep_field;
                            else :
                                    unset($profile_template->groups[$group_inc]);
                            endif;
                        }
                        $group_inc++;
                    }
                }
            }
            return $profile_template;
        }
        public function sp_make_noneditable($has_groups, $profile_template)
        {
            global $bp;
            if (current_user_can('edit_users')) {
                return $profile_template;
            }
            $user_id = $profile_template->user_id;
            if ($bp->current_component == 'profile') {
                if (!empty($profile_template->groups)) {
                    $keep_group = array();
                    $group_inc = 0;
                    foreach ($profile_template->groups as $group) {
                        if (!empty($group->fields)) {
                            $keep_field = array();
                            foreach ($group->fields as $field) {
                                $option_values = bp_xprofile_get_meta($field->id, 'field', $this->id.'_options');
                                if (empty($option_values['non_editable']) || $option_values['non_editable']!=1 && !empty($field->value)) :
                                    $keep_field[] = $field;
                                endif;
                            }
                            if (!empty($keep_field)) :
                                $profile_template->groups[$group_inc]->fields = $keep_field;
                            else :
                                    unset($profile_template->groups[$group_inc]);
                            endif;
                        }
                        $group_inc++;
                    }
                }
            }
            return $profile_template;
        }
        /**
         * Add Validation
         */
        public function add_validation()
        {
            global $wpdb;
            global $bp;
            $field_ids = $wpdb->get_col("SELECT id FROM {$bp->profile->table_name_fields} WHERE group_id = ". bp_get_current_profile_group_id());
            if (!empty($field_ids)) :
            ?>
            <?php if( is_ssl() ) : ?>
            <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.1/jquery.validate.min.js"></script>
            <?php else: ?>
            <script type="text/javascript" src="//cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>

            <?php endif; ?>
<script type="text/javascript">
            jQuery(document).ready(function($) {
             $.validator.addMethod("alpha", function(value, element) {
                return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
             }, "<?php _e('Please enter only letters', 'sp-advanced-xprofile'); ?>");

            $.validator.addMethod("alphanumeric", function(value, element) {
                return this.optional(element) || /^\w+$/i.test(value);
            }, "<?php _e('Letters, numbers, and underscores only', 'sp-advanced-xprofile'); ?>");

            $.validator.addMethod("stateUS", function(value, element, options) {
                var isDefault = typeof options === "undefined",
                    caseSensitive = ( isDefault || typeof options.caseSensitive === "undefined" ) ? false : options.caseSensitive,
                    includeTerritories = ( isDefault || typeof options.includeTerritories === "undefined" ) ? false : options.includeTerritories,
                    includeMilitary = ( isDefault || typeof options.includeMilitary === "undefined" ) ? false : options.includeMilitary,
                    regex;

                if (!includeTerritories && !includeMilitary) {
                    regex = "^(A[KLRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])$";
                } else if (includeTerritories && includeMilitary) {
                    regex = "^(A[AEKLPRSZ]|C[AOT]|D[CE]|FL|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEINOPST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$";
                } else if (includeTerritories) {
                    regex = "^(A[KLRSZ]|C[AOT]|D[CE]|FL|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEINOPST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$";
                } else {
                    regex = "^(A[AEKLPRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])$";
                }

                regex = caseSensitive ? new RegExp(regex) : new RegExp(regex, "i");
                return this.optional(element) || regex.test(value);
            },
            "<?php _e('Please specify a valid state', 'sp-advanced-xprofile'); ?>");

            $.validator.addMethod("zipcodeUS", function(value, element) {
                return this.optional(element) || /^\d{5}(-\d{4})?$/.test(value);
            }, "<?php _e('The specified US ZIP Code is invalid', 'sp-advanced-xprofile'); ?>");

            $( "#profile-edit-form,#signup_form" ).validate({
                rules: {
            <?php
            foreach ($field_ids as $field_key => $field_id) :
                $validation_methods = bp_xprofile_get_meta($field_id, 'field', $this->id.'_validation');
                $v = wp_parse_args($validation_methods, array(
                'enable' => array(
                    'char_limit'=>0,
                    'min_chars'=> 0,
                    'text_format'=>0
                ),
                'char_limit' => '',
                'min_chars' => '',
                'text_format' => ''
                ));
                if (!empty($validation_methods['enable'])) :
                    echo "\n\t\t\t\t\t";
                    echo "field_".$field_id.": {";
                    if ($v['enable']['char_limit']) {
                        echo "\n\t\t\t\t\t\t";
                        if ($v['char_limit']) :
                            echo "maxlength: ".$v['char_limit'].",";
                        endif;
                    }
                    if ($v['enable']['min_chars']) {
                        echo "\n\t\t\t\t\t\t";
                        if ($v['min_chars']) :
                            echo "minlength: ".$v['min_chars'].",";
                        endif;
                    }
                    if ($v['enable']['text_format']) {
                        echo "\n\t\t\t\t\t\t";
                        if ($v['text_format']) :
                            switch ($v['text_format']) {
                                case 'email':
                                    echo "email: true,";
                                    break;
                                case 'url':
                                    echo "url: true,";
                                    break;
                                case 'alpha':
                                    echo "alpha: true,";
                                    break;
                                case 'alphanumeric':
                                    echo "alphanumeric: true,";
                                    break;
                            }
                        endif;
                    }
                    echo "\n\t\t\t\t\t";
                    echo "},";
                endif;
            endforeach;
            echo "\n\t\t\t\t";
            echo '}';
            echo "\n\t\t\t";
            echo '});';
            echo "\n\t\t\t";
            echo '});';
            echo "\n";
            ?>
            </script>
<?php
            endif;
        }
        /**
         * Create extra admin columns
         * @param array $columns
         */
        public function add_bp_field_colums( $columns = array() )
        {
            $user_columns = get_option($this->id.'user_columns');
            if (!empty($user_columns)) :
                foreach ($user_columns as $key => $field_id) :
                    $labels = bp_xprofile_get_meta($field_id, 'field', $this->id.'_labels');
                    $field =  xprofile_get_field($field_id);
                    $name = (!empty($labels['admin']) ? $labels['admin'] : $field->name );
                    $id = sanitize_title($name);
                    $id = str_replace('-', '_', $id);
                    $columns['field_'.$field->id] = $name;
                endforeach;
            endif;
            return $columns;
        }
        /**
         * Get field value for table column
         * @param  string  $value       [description]
         * @param  string  $column_name [description]
         * @param  integer $user_id     [description]
         * @return string
         */
        public function bp_field_column_content( $value = '', $column_name = '', $user_id = 0 ) {
            $user_columns = get_option($this->id . 'user_columns');
            if ( !empty( $user_columns ) ) :
                $field = str_replace('field_', '', $column_name);
                if ( in_array( $field, $user_columns ) ) {
                    $field_data =  xprofile_get_field_data( $field, $user_id, 'comma' );
                    return maybe_unserialize( $field_data );
                }
            endif;

            return $value;
        }
    }
endif;

add_action('bp_init', 'sp_advanced_xprofile_initiate');
function sp_advanced_xprofile_initiate(){
    SP_Advanced_XProfile::instance();
}

/**
 * Load language
 */
add_action('plugins_loaded', 'bp_advanced_xprofile_language');
function bp_advanced_xprofile_language(){
    load_plugin_textdomain('sp-advanced-xprofile', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
