<?php
/**
* @version $ Id; ----.php 21-03-2012 03:22:10 Ahmed Said $
*/

/**
* No direct access.
*/
defined('ABSPATH') or die("Access denied");
		
/**
* 
* @author Ahmed Said
* @version 6
*/
class CJTStatisticsMetaboxModel {
	
	/**
	* 
	*/
	const CJT_LASTEST_SCRIPT_OPTION_NAME = 'CJTStatisticsMetaboxModel.latestscripts';
	
	/**
	* 
	*/
	const LATEST_SCRIPT_EXPIRES = 86400;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $dbDriver;
	
	/**
	* put your comment there...
	* 
	*/
	public function __construct() {
		$this->dbDriver =& cssJSToolbox::getInstance()->getDBDriver();
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $state
	* @param mixed $type
	*/
	public function getBlocksCount($state, $type) 
    {
        
        $query = "  SELECT count(*) blocksCount   
                    FROM #__cjtoolbox_blocks 
                    WHERE backupId IS NULL AND state = '{$state}' AND type='{$type}';";
        
		$result = $this->dbDriver->select($query, ARRAY_A);
        
        $count = $result[0]['blocksCount'];
        
		return $count;
	}
	
    /**
    * put your comment there...
    * 
    */
    public function getCodeFilesCount()
    {
        
        $query = '  SELECT count(*) codeFilesCount
                    FROM #__cjtoolbox_blocks b LEFT JOIN #__cjtoolbox_block_files f
                    ON b.id = f.blockId
                    WHERE b.BackupId IS NULL AND b.type != "revision";';
        
        $result = $this->dbDriver->select($query, ARRAY_A);
        
        $count = $result[0]['codeFilesCount'];
        
        return $count;
    }
    
	/**
	* put your comment there...
	* 
	*/
	public function getFeed() {
		// Initialize.
		$widgetTransitFeed = get_option(self::CJT_LASTEST_SCRIPT_OPTION_NAME, array('time' => 0));
		// Only if cache is expires read feed from server.
		if ((time() - $widgetTransitFeed['time']) > self::LATEST_SCRIPT_EXPIRES) {
			# INitiaize
			$fieldsNames = array('title', 'link', 'description', 'pubDate');
			# Get Latest Scripts/Packages from feed.
			$scriptsFeed = new CJT_Framework_Wordpress_Feed(cssJSToolbox::CJT_WEB_SITE_DOMAIN, 'category/scripts/feed/', $fieldsNames);
			$widgetTransitFeed['scripts'] = $scriptsFeed->getLatestItems(7);
			# Get latest extensions from feed
			$extensionsFeed = new CJT_Framework_Wordpress_Feed(cssJSToolbox::CJT_WEB_SITE_DOMAIN, 'category/extensions/feed/', $fieldsNames);			
			$widgetTransitFeed['extensions'] = $extensionsFeed->getLatestItems(7);
			# Get latest news from news feed.
			$newsFeed = new CJT_Framework_Wordpress_Feed(cssJSToolbox::CJT_WEB_SITE_DOMAIN, 'category/news/feed/', $fieldsNames);
			$widgetTransitFeed['news'] = $newsFeed->getLatestItems(1);
			# Cache only if there is no errors.
			if (!$newsFeed->isError() && !$scriptsFeed->isError() && !$extensionsFeed->isError()) {
				# Store cache time.
				$widgetTransitFeed['time'] = time();
				update_option(self::CJT_LASTEST_SCRIPT_OPTION_NAME, $widgetTransitFeed);				
			}
		}
		# For versions 8.0 it will produce PHP notice because extensions item is not being exists in the cache
		# The error will be always visible until next caching cycle!
		# temporary solution
		else if (!isset($widgetTransitFeed['extensions'])) {
			$widgetTransitFeed['extensions'] = array();
		}
		# Returns
		return $widgetTransitFeed;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getPackagesCount() {
		$result = $this->dbDriver->select('SELECT count(*) packagesCount FROM #__cjtoolbox_packages;', ARRAY_A);
		return $result[0]['packagesCount'];
	}

	/**
	* put your comment there...
	* 
	*/
	public function getTemplatesCount() {
		$result = $this->dbDriver->select('SELECT count(*) templatesCount FROM #__cjtoolbox_templates WHERE (attributes & 1) = 0;', ARRAY_A);
		return $result[0]['templatesCount'];		
	}

} // End class.