<?php
if (!defined('ABSPATH')) exit;

@require_once NEWSLETTER_INCLUDES_DIR . '/logger.php';

class NewsletterStore {

    static $instance = null;

    /**
     * @var NewsletterLogger
     */
    var $logger;

    /**
     *
     * @return NewsletterStore
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterStore();
        }
        return self::$instance;
    }

    static function singleton() {
        return self::instance();
    }

    function __construct() {
        $this->logger = new NewsletterLogger('store');
    }

    function get_field($table, $id, $field_name) {
        global $wpdb;
        $field_name = (string)$field_name;
        if (preg_match('/^[a-zA-Z_]+$/', $field_name) == 0) {
            $this->logger->error('Invalis field name: ' . $field_name);
            return false;
        }
        $id = (int)$id;
        $r = $wpdb->get_var($wpdb->prepare("select $field_name from $table where id=%d limit 1", $id));
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $r;
    }

    function get_single($table, $id, $format = OBJECT) {
        global $wpdb;
        $id = (int)$id;
        return $this->get_single_by_query($wpdb->prepare("select * from $table where id=%d limit 1", $id), $format);
    }

    function get_single_by_field($table, $field_name, $field_value, $format = OBJECT) {
        global $wpdb;
        $field_name = (string)$field_name;
        $field_value = (string)$field_value;
        
        if (preg_match('/^[a-zA-Z_]+$/', $field_name) == 0) {
            $this->logger->error('Invalis field name: ' . $field_name);
            return false;
        }
        return $this->get_single_by_query($wpdb->prepare("select * from $table where $field_name=%s limit 1", $field_value), $format);
    }

    function get_count($table, $where = null) {
        global $wpdb;
        $r = $wpdb->get_var("select count(*) from $table " . ($where != null ? $where : ''));
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $r;
    }

    /**
     * Returns a single record executing the given query or null if no row can be found. If more rows are matching
     * the query, only the first one is returned. Returns "false" on error (use the strict type checking ===) and a log
     * is written.
     *
     * @global wpdb $wpdb
     * @param string $query
     * @param type $format
     * @return boolean|mixed
     */
    function get_single_by_query($query, $format = OBJECT) {
        global $wpdb;
        $r = $wpdb->get_row($query, $format);
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $r;
    }

    function sanitize($data) {
        global $wpdb;
        if (strpos($wpdb->charset, 'utf8mb4') === 0) return $data;
        foreach ($data as $key => $value) {
            $data[$key] = preg_replace('%(?:\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})%xs', '', $value);
        }
        return $data;
    }

    /**
     * Save a record on given table, updating it id the "id" value is set (as key or object property) or inserting it
     * if "id" is not set. Accepts objects or associative arrays as data.
     *
     * Returns "false" is an error occurred or the saved data re-read from the database in given format.
     *
     * @global wpdb $wpdb
     * @param type $table
     * @param type $data
     */
    function save($table, $data, $return_format = OBJECT) {
        global $wpdb;
        if (is_object($data)) {
            $data = (array) $data;
        }
        
        // Remove transient fields
        foreach (array_keys($data) as $key) {
            if (substr($key, 0, 1) == '_') unset($data[$key]);
        }

        if (isset($data['id'])) {
            $id = (int)$data['id'];
            unset($data['id']);
            if (!empty($data)) {
                $r = $wpdb->update($table, $this->sanitize($data), array('id' => $id));
                if ($r === false) {
                    $this->logger->fatal($wpdb->last_error);
                    $this->logger->fatal($wpdb->last_query);
                    die('Database error see the log files (log files path can be found on Newsletter diagnostic panel)');
                }
            }
            //$this->logger->debug('save: ' . $wpdb->last_query);
        } else {
            $r = $wpdb->insert($table, $this->sanitize($data));
            if ($r === false) {
                $this->logger->fatal($wpdb->last_error);
                $this->logger->fatal($wpdb->last_query);
                die('Database error see the log files (log files path can be found on Newsletter diagnostic panel)');
            }
            $id = $wpdb->insert_id;
        }
//        if ($wpdb->last_error) {
//            $this->logger->error('save: ' . $wpdb->last_error);
//            return false;
//        }

        return $this->get_single($table, $id, $return_format);
    }

    function increment($table, $id, $field) {
        global $wpdb;
        $id = (int)$id;
        $field = (string)$field;
        
        if (preg_match('/^[a-zA-Z_]+$/', $field) == 0) {
            $this->logger->error('Invalis field name: ' . $field);
            return false;
        }
        $result = $wpdb->query($wpdb->prepare("update $table set $field=$field+1 where id=%d", $id));

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }

        return $result;
    }

    /**
     * Deletes one or more rows by id (or an array of id)
     *
     * @param int|array $id
     * @return int Number of rows deleted
     */
    function delete($table, $id) {
        global $wpdb;
        if (is_array($id)) {
            for ($i=0; $i<count($id); $i++) {
                $id[$i] = (int)$id[$i];
            }
            $wpdb->query("delete from " . $table . " where id in (" . implode(',', $id) . ")");
        } else {
            $wpdb->delete($table, array('id' => (int)$id));
        }
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $wpdb->rows_affected;
    }

    /**
     *
     * @global wpdb $wpdb
     * @param type $table
     * @param type $order_by
     * @param type $format
     * @return type
     */
    function get_all($table, $where = null, $format = OBJECT) {
        global $wpdb;
        if ($where == null) {
            $result = $wpdb->get_results("select * from $table", $format);
        } else {
            $result = $wpdb->get_results("select * from $table $where", $format);
        }
        return $result;
    }

    function set_field($table, $id, $field, $value) {
        global $wpdb;
        $field = (string)$field;
        $id = (int)$id;
        $value = (string)$value;
        
        if (preg_match('/^[a-zA-Z_]+$/', $field) == 0) {
            $this->logger->error('Invalis field name: ' . $field_name);
            return false;
        }
        $result = $wpdb->query($wpdb->prepare("update $table set $field=%s where id=%d", $value, $id));

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }

        return $result;
    }

    function query($query) {
        global $wpdb;
        $result = $wpdb->query($query);
        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);
            return false;
        }
        return $result;
    }

}
