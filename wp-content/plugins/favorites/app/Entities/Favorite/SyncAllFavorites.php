<?php
namespace Favorites\Entities\Favorite;

use Favorites\Config\SettingsRepository;

/**
* Sync all favorites for a specific site
*/
class SyncAllFavorites
{
	/**
	* Favorites to Save
	* @var array
	*/
	private $favorites;

	/**
	* Settings Repository
	*/
	private $settings_repo;

	public function __construct()
	{
		$this->settings_repo = new SettingsRepository;
	}

	/**
	* Sync the favorites
	*/
	public function sync($favorites)
	{
		$this->favorites = $favorites;
		$saveType = $this->settings_repo->saveType();
		$this->$saveType();
		$this->updateUserMeta();
	}

	/**
	* Sync Session Favorites
	*/
	private function session()
	{
		return $_SESSION['simplefavorites'] = $this->favorites;
	}

	/**
	* Sync a Cookie Favorite
	*/
	public function cookie()
	{
		setcookie( 'simplefavorites', json_encode( $this->favorites ), time() + apply_filters( 'simplefavorites_cookie_expiration_interval', 31556926 ), '/' );
		return;
	}

	/**
	* Update User Meta (logged in only)
	*/
	private function updateUserMeta()
	{
		if ( !isset($_POST['user_id']) ) return false;
		if ( !isset($_POST['logged_in']) && intval($_POST['logged_in']) !== 1 ) return false;
		return update_user_meta( intval($_POST['user_id']), 'simplefavorites', $this->favorites );
	}
}