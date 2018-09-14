<?php
/**
 * @package    Stiphle
 * @subpackage Stiphle\Throttle\LeakyBucket\Storage
 */
if( ! defined( 'WP_SESSION_COOKIE' ) ) {
    define( 'WP_SESSION_COOKIE', '_wp_session' );
}
require_once  LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle'. DIRECTORY_SEPARATOR .'Storage'. DIRECTORY_SEPARATOR .'Session'. DIRECTORY_SEPARATOR .'class-wp-session-utils.php';
require_once  LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle'. DIRECTORY_SEPARATOR .'Storage'. DIRECTORY_SEPARATOR .'Session'. DIRECTORY_SEPARATOR .'class-recursive-arrayaccess.php';
require_once  LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle'. DIRECTORY_SEPARATOR .'Storage'. DIRECTORY_SEPARATOR .'Session'. DIRECTORY_SEPARATOR .'class-wp-session.php';
require_once  LEAFLET_PLUGIN_DIR . 'inc' . DIRECTORY_SEPARATOR . 'Stiphle'. DIRECTORY_SEPARATOR .'Storage'. DIRECTORY_SEPARATOR .'Session'. DIRECTORY_SEPARATOR .'wp-session.php';
/**
 * Use Session as the storage.
 *
 */
class LMM_Session implements StorageInterface
{
    /**
     * @var int
     */
    protected $lockWaitTimeout = 1000;

    /**
     * @var int  Time to sleep when attempting to get lock in microseconds
     */
    protected $sleep = 100;

    /**
     * @var int 
     */
    protected $ttl = 10000000;

    /**
     * Set lock wait timeout
     *
     * @param int $milliseconds
     */
    public function setLockWaitTimeout($milliseconds)
    {
        $this->lockWaitTimeout = $milliseconds;
        return;
    }

    /**
     * Set the sleep time in microseconds
     *
     * @param int 
     * @return void
     */
    public function setSleep($microseconds)
    {
        $this->sleep = $microseconds;
        return;
    }

    /**
     * Set the ttl for the session records in seconds
     *
     * @param int $seconds
     * @return void
     */
    public function setTtl($microseconds)
    {
        $this->ttl = $microseconds;
        return;
    }

    /**
     * Lock 
     *
     * If we're using storage, we might have multiple requests coming in at
     * once, so we lock the storage
     *
     * @return void
     */
    public function lock($key)
    {
        $wp_session = LMM_WP_Session::get_instance();
        $key = $key . "::LOCK";
        $start = microtime(true);
        while($wp_session[$key]) {
            $passed = (microtime(true) - $start) * 1000;
            if ($passed > $this->lockWaitTimeout) {
                throw new LockWaitTimeoutException();
            }
            usleep($this->sleep);
        }

        return;
    }

    /**
     * Unlock
     *
     * @return void
     */
    public function unlock($key)
    {
        $wp_session = LMM_WP_Session::get_instance();
        $key = $key . "::LOCK";
        unset($wp_session[$key]);
    }

    /**
     * Get last modified
     *
     * @param string $key
     * @return int
     */
    public function get($key)
    {
        $wp_session = LMM_WP_Session::get_instance();
        return isset($wp_session[$key])?$wp_session[$key]:null;
    }

    /**
     * set 
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $wp_session = LMM_WP_Session::get_instance();
        $wp_session[$key]  = $value;
        return;
    }
}