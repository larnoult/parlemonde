<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
    class Kadence_Status {

        public function __construct() {
            add_action('init', array( $this, 'init' ) );
        }

        public function init() {

            add_action( 'admin_menu', array( $this, 'kadence_status_page' ) );
            // if ( isset ( $_GET['page'] ) && $_GET['page'] == "kadence-status" ) {
            //add_action( 'admin_head', array( $this, 'kadence_status_admin_styles' ) );
            // }
        }
        private static function kt_let_to_num( $size ) {
                $l   = substr( $size, - 1 );
                $ret = substr( $size, 0, - 1 );

                switch ( strtoupper( $l ) ) {
                    case 'P':
                        $ret *= 1024;
                    case 'T':
                        $ret *= 1024;
                    case 'G':
                        $ret *= 1024;
                    case 'M':
                        $ret *= 1024;
                    case 'K':
                        $ret *= 1024;
                }

                return $ret;
            }
        public static function kt_makeBoolStr( $var ) {
                if ( $var == false || $var == 'false' || $var == 0 || $var == '0' || $var == '' || empty( $var ) ) {
                    return 'false';
                } else {
                    return 'true';
                }
            }
        public static function kt_compileSystemStatus() {
                global $wpdb;

                $sysinfo = array();

                $sysinfo['home_url']       = home_url();
                $sysinfo['site_url']       = site_url();
                $f                         = 'fo' . 'pen';
                $sysinfo['wp_content_url']       = WP_CONTENT_URL;
                $sysinfo['wp_ver']               = get_bloginfo( 'version' );
                $sysinfo['wp_multisite']         = is_multisite();
                $sysinfo['permalink_structure']  = get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default';
                $sysinfo['front_page_display']   = get_option( 'show_on_front' );
                if ( $sysinfo['front_page_display'] == 'page' ) {
                    $front_page_id = get_option( 'page_on_front' );
                    $blog_page_id  = get_option( 'page_for_posts' );

                    $sysinfo['front_page'] = $front_page_id != 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset';
                    $sysinfo['posts_page'] = $blog_page_id != 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset';
                }

                $sysinfo['wp_mem_limit']['raw']  = self::kt_let_to_num( WP_MEMORY_LIMIT );
                $sysinfo['wp_mem_limit']['size'] = size_format( $sysinfo['wp_mem_limit']['raw'] );

                $sysinfo['db_table_prefix'] = 'Length: ' . strlen( $wpdb->prefix ) . ' - Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' );

                $sysinfo['wp_debug'] = 'false';
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    $sysinfo['wp_debug'] = 'true';
                }

                $sysinfo['wp_lang'] = get_locale();

                $sysinfo['server_info'] = esc_html( $_SERVER['SERVER_SOFTWARE'] );
                $sysinfo['php_ver']     = function_exists( 'phpversion' ) ? esc_html( phpversion() ) : 'phpversion() function does not exist.';
                $sysinfo['abspath']     = ABSPATH;

                if ( function_exists( 'ini_get' ) ) {
                    $sysinfo['php_mem_limit']      = size_format( self::kt_let_to_num( ini_get( 'memory_limit' ) ) );
                    $sysinfo['php_post_max_size']  = size_format( self::kt_let_to_num( ini_get( 'post_max_size' ) ) );
                    $sysinfo['php_time_limit']     = ini_get( 'max_execution_time' );
                    $sysinfo['php_max_input_var']  = ini_get( 'max_input_vars' );
                    $sysinfo['php_display_errors'] = self::kt_makeBoolStr( ini_get( 'display_errors' ) );
                }

                $sysinfo['suhosin_installed'] = extension_loaded( 'suhosin' );
                $sysinfo['mysql_ver']         = $wpdb->db_version();
                $sysinfo['max_upload_size']   = size_format( wp_max_upload_size() );

                $sysinfo['def_tz_is_utc'] = 'true';
                if ( date_default_timezone_get() !== 'UTC' ) {
                    $sysinfo['def_tz_is_utc'] = 'false';
                }

                $sysinfo['fsockopen_curl'] = 'false';
                if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
                    $sysinfo['fsockopen_curl'] = 'true';
                }

                $active_plugins = (array) get_option( 'active_plugins', array() );

                if ( is_multisite() ) {
                    $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
                }

                $sysinfo['plugins'] = array();

                foreach ( $active_plugins as $plugin ) {
                    $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                    $plugin_name = esc_html( $plugin_data['Name'] );

                    $sysinfo['plugins'][ $plugin_name ] = $plugin_data;
                }

                $active_theme = wp_get_theme();

                $sysinfo['theme']['name']       = $active_theme->Name;
                $sysinfo['theme']['version']    = $active_theme->Version;
                $sysinfo['theme']['author_uri'] = $active_theme->{'Author URI'};
                $sysinfo['theme']['is_child']   = self::kt_makeBoolStr( is_child_theme() );

                if ( is_child_theme() ) {
                    $parent_theme = wp_get_theme( $active_theme->Template );

                    $sysinfo['theme']['parent_name']       = $parent_theme->Name;
                    $sysinfo['theme']['parent_version']    = $parent_theme->Version;
                    $sysinfo['theme']['parent_author_uri'] = $parent_theme->{'Author URI'};
                }

                return $sysinfo;
            }
        public function kadence_status_page() {
            add_management_page(__( 'Kadence System Status', 'virtue' ), __( 'System Status', 'virtue' ), 'edit_theme_options', 'kadence-status', array($this, 'status_page_content') );
        }
        public function status_page_content() {

        $sysinfo = $this->kt_compileSystemStatus();

            ?>
            <style type="text/css" id="kt-status-css">
            table.kt_status_table tr:nth-child(2n) td {
                background: #fcfcfc;
            }
            table.kt_status_table td:first-child {
                width: 33%;
            }
            table.kt_status_table td mark {
                background: transparent none;
                color:#00b5e2;
            }
            table.kt_status_table {margin-bottom: 1em;}
            </style>
        <div style="max-width: 1170px; margin: 25px 40px 0 20px;">
        <h1><?php echo __( 'System Status', 'virtue' ); ?></h1>
        <table class="kt_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="WordPress Environment">
                <?php esc_html_e( 'WordPress Environment', 'virtue' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Home URL">
                <?php esc_html_e( 'Home URL', 'virtue' ); ?>:
            </td>
            <td><?php echo esc_url($sysinfo['home_url']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Site URL">
                <?php esc_html_e( 'Site URL', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo esc_url($sysinfo['site_url']); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="WP Content URL">
                <?php esc_html_e( 'WP Content URL', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo '<code>' . esc_url($sysinfo['wp_content_url']) . '</code> '; ?>
            </td>
        </tr>        
        <tr>
            <td data-export-label="WP Version">
                <?php esc_html_e( 'WP Version', 'virtue' ); ?>:
            </td>
            <td>
                <?php bloginfo( 'version' ); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="WP Multisite">
                <?php esc_html_e( 'WP Multisite', 'virtue' ); ?>:
            </td>
            <td><?php if ( $sysinfo['wp_multisite'] == true ) {
                    echo '&#10004;';
                } else {
                    echo '&ndash;';
                } ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Permalink Structure">
                <?php esc_html_e( 'Permalink Structure', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo esc_html($sysinfo['permalink_structure']); ?>
            </td>
        </tr>
        <?php $sof = $sysinfo['front_page_display']; ?>
        <tr>
            <td data-export-label="Front Page Display">
                <?php esc_html_e( 'Front Page Display', 'virtue' ); ?>:
            </td>
            <td><?php echo esc_html($sof); ?></td>
        </tr>

        <?php
            if ( $sof == 'page' ) {
?>
                <tr>
                    <td data-export-label="Front Page">
                        <?php esc_html_e( 'Front Page', 'virtue' ); ?>:
                    </td>
                    <td>
                        <?php echo esc_html($sysinfo['front_page']); ?>
                    </td>
                </tr>
                <tr>
                    <td data-export-label="Posts Page">
                        <?php esc_html_e( 'Posts Page', 'virtue' ); ?>:
                    </td>
                    <td>
                        <?php echo esc_html($sysinfo['posts_page']); ?>
                    </td>
                </tr>
<?php
            }
?>
        <tr>
            <td data-export-label="WP Memory Limit">
                <?php esc_html_e( 'WP Memory Limit', 'virtue' ); ?>:
            </td>
            <td>
<?php
                    $memory = $sysinfo['wp_mem_limit']['raw'];

                    if ( $memory < 40000000 ) {
                        echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 40MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'virtue' ), esc_html($sysinfo['wp_mem_limit']['size']), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
                    } else {
                        echo '<mark class="yes">' . esc_html($sysinfo['wp_mem_limit']['size']) . '</mark>';
                    }
?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Database Table Prefix">
                <?php esc_html_e( 'Database Table Prefix', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo esc_html($sysinfo['db_table_prefix']); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="WP Debug Mode">
                <?php esc_html_e( 'WP Debug Mode', 'virtue' ); ?>:
            </td>
            <td>
                <?php if ( $sysinfo['wp_debug'] === 'true' ) {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } else {
                    echo '<mark class="no">' . '&ndash;' . '</mark>';
                } ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Language">
                <?php esc_html_e( 'Language', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo esc_html($sysinfo['wp_lang']); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table class="kt_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="Server Environment">
                <?php esc_html_e( 'Server Environment', 'virtue' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Server Info">
                <?php esc_html_e( 'Server Info', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo esc_html($sysinfo['server_info']); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="PHP Version">
                <?php esc_html_e( 'PHP Version', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo esc_html($sysinfo['php_ver']); ?>
            </td>
        </tr>
        <tr>
            <td data-export-label="ABSPATH">
                <?php esc_html_e( 'ABSPATH', 'virtue' ); ?>:
            </td>
            <td>
                <?php echo '<code>' . esc_html($sysinfo['abspath']) . '</code>'; ?>
            </td>
        </tr>
        
        <?php if ( function_exists( 'ini_get' ) ) { ?>
            <tr>
                <td data-export-label="PHP Memory Limit"><?php esc_html_e( 'PHP Memory Limit', 'virtue' ); ?>:</td>
                <td><?php echo esc_html($sysinfo['php_mem_limit']); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Post Max Size"><?php esc_html_e( 'PHP Post Max Size', 'virtue' ); ?>:</td>
                <td><?php echo esc_html($sysinfo['php_post_max_size']); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Time Limit"><?php esc_html_e( 'PHP Time Limit', 'virtue' ); ?>:</td>
                <td><?php echo esc_html($sysinfo['php_time_limit']); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Max Input Vars"><?php esc_html_e( 'PHP Max Input Vars', 'virtue' ); ?>:</td>
                <td><?php echo esc_html($sysinfo['php_max_input_var']); ?></td>
            </tr>
            <tr>
                <td data-export-label="PHP Display Errors"><?php esc_html_e( 'PHP Display Errors', 'virtue' ); ?>:</td>
                <td><?php
                        if ( 'true' === $sysinfo['php_display_errors'] ) {
                            echo '<mark class="yes">' . '&#10004;' . '</mark>';
                        } else {
                            echo '<mark class="no">' . '&ndash;' . '</mark>';
                        }
                    ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td data-export-label="SUHOSIN Installed"><?php esc_html_e( 'SUHOSIN Installed', 'virtue' ); ?>:</td>
            <td>
                <?php if ( $sysinfo['suhosin_installed'] == true ) {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } else {
                    echo '<mark class="no">' . '&ndash;' . '</mark>';
                } ?>
            </td>
        </tr>

        <tr>
            <td data-export-label="MySQL Version"><?php esc_html_e( 'MySQL Version', 'virtue' ); ?>:</td>
            <td><?php echo esc_html($sysinfo['mysql_ver']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Max Upload Size"><?php esc_html_e( 'Max Upload Size', 'virtue' ); ?>:</td>
            <td><?php echo esc_html($sysinfo['max_upload_size']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Default Timezone is UTC">
                <?php esc_html_e( 'Default Timezone is UTC', 'virtue' ); ?>:
            </td>
            <td>
<?php
                if ( $sysinfo['def_tz_is_utc'] === 'false' ) {
                    echo '<mark class="error">' . '&#10005; ' . sprintf( __( 'Default timezone is %s - it should be UTC', 'virtue' ), esc_html(date_default_timezone_get()) ) . '</mark>';
                } else {
                    echo '<mark class="yes">' . '&#10004;' . '</mark>';
                } 
?>
            </td>
        </tr>
        <?php
            $posting = array();

            // fsockopen/cURL
            $posting['fsockopen_curl']['name'] = 'fsockopen/cURL';
            $posting['fsockopen_curl']['help'] = '';

            if ( $sysinfo['fsockopen_curl'] === 'true' ) {
                $posting['fsockopen_curl']['success'] = true;
            } else {
                $posting['fsockopen_curl']['success'] = false;
                $posting['fsockopen_curl']['note']    = esc_html__( 'Your server does not have fsockopen or cURL enabled - cURL is used to communicate with other servers. Please contact your hosting provider.', 'virtue' ) . '</mark>';
            }

            foreach ( $posting as $post ) {
                $mark = ! empty( $post['success'] ) ? 'yes' : 'error';
                ?>
                <tr>
                    <td data-export-label="<?php echo esc_html( $post['name'] ); ?>">
                        <?php echo esc_html( $post['name'] ); ?>:
                    </td>
                    <td class="help">
                        <mark class="<?php echo esc_attr($mark); ?>">
                            <?php echo ! empty( $post['success'] ) ? '&#10004' : '&#10005'; ?>
                            <?php echo ! empty( $post['note'] ) ? wp_kses_data( $post['note'] ) : ''; ?>
                        </mark>
                    </td>
                </tr>
            <?php
            }
        ?>
        </tbody>
    </table>
    <table class="kt_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="Active Plugins (<?php echo esc_html(count( (array) get_option( 'active_plugins' ) ) ); ?>)">
                <?php esc_html_e( 'Active Plugins', 'virtue' ); ?> (<?php echo esc_html(count( (array) get_option( 'active_plugins' ) ) ); ?>)
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach ( $sysinfo['plugins'] as $name => $plugin_data ) {
                $version_string = '';
                $network_string = '';

                if ( ! empty( $plugin_data['Name'] ) ) {
                    // link the plugin name to the plugin url if available
                    $plugin_name = esc_html( $plugin_data['Name'] );

                    if ( ! empty( $plugin_data['PluginURI'] ) ) {
                        $plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Visit plugin homepage', 'virtue' ) . '">' . esc_html($plugin_name) . '</a>';
                    }
?>
                    <tr>
                        <td><?php echo $plugin_name; ?></td>
                        <td>
                            <?php echo sprintf( _x( 'by %s', 'by author', 'virtue' ), $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?>
                        </td>
                    </tr>
<?php
                }
            }
        ?>
        </tbody>
    </table>
   
    <table class="kt_status_table widefat" cellspacing="0" id="status">
        <thead>
        <tr>
            <th colspan="3" data-export-label="Theme"><?php esc_html_e( 'Theme', 'virtue' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td data-export-label="Name"><?php esc_html_e( 'Name', 'virtue' ); ?>:</td>
            <td><?php echo esc_html($sysinfo['theme']['name']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Version"><?php esc_html_e( 'Version', 'virtue' ); ?>:</td>
            <td>
<?php
                echo esc_html($sysinfo['theme']['version']);

                if ( ! empty( $theme_version_data['version'] ) && version_compare( $theme_version_data['version'], $active_theme->Version, '!=' ) ) {
                    echo ' &ndash; <strong style="color:red;">' . esc_html($theme_version_data['version']) . ' ' . esc_html__( 'is available', 'virtue' ) . '</strong>';
                }
?>
            </td>
        </tr>
        <tr>
            <td data-export-label="Author URL"><?php esc_html_e( 'Author URL', 'virtue' ); ?>:</td>
            <td><?php echo esc_url($sysinfo['theme']['author_uri']); ?></td>
        </tr>
        <tr>
            <td data-export-label="Child Theme"><?php esc_html_e( 'Child Theme', 'virtue' ); ?>:</td>
            <td>
            <?php
                echo is_child_theme() ? '<mark class="yes">' . '&#10004;' . '</mark>' : '&#10005;';
            ?>
            </td>
        </tr>
        <?php
            if ( is_child_theme() ) {
        ?>
                <tr>
                    <td data-export-label="Parent Theme Name"><?php esc_html_e( 'Parent Theme Name', 'virtue' ); ?>:
                    </td>
                    <td><?php echo esc_html($sysinfo['theme']['parent_name']); ?></td>
                </tr>
                <tr>
                    <td data-export-label="Parent Theme Version">
                        <?php esc_html_e( 'Parent Theme Version', 'virtue' ); ?>:
                    </td>
                    <td><?php echo esc_html($sysinfo['theme']['parent_version']); ?></td>
                </tr>
                <tr>
                    <td data-export-label="Parent Theme Author URL">
                        <?php esc_html_e( 'Parent Theme Author URL', 'virtue' ); ?>:
                    </td>
                    <td><?php echo esc_url($sysinfo['theme']['parent_author_uri']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
<?php
            
        }
    }
    new Kadence_Status();