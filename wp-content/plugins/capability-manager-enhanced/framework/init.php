<?php
/**
 * Framework Initialization.
 * This file is called at framework load time.
 *
 * @version		$Rev: 199485 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Creates and returns the framework URL.
 *
 * @return string Framework URL
 */
function ak_styles_url ()
{
   $dir = str_replace('\\', '/', WP_CONTENT_DIR);
   $fmw = str_replace('\\', '/', AKK_FRAMEWORK);

   return str_replace($dir, content_url(), $fmw) . '/styles';
}

// ================================================= SET GLOBAL CONSTANTS =====

if ( ! defined('AK_STYLES_URL') ) {
    /** Define the framework URL */
    define ( 'AK_STYLES_URL', ak_styles_url() );
}

if ( ! defined('AK_INI_FILE') ) {
    /** Define the alkivia.ini filename and absoilute location */
    define ( 'AK_INI_FILE', WP_CONTENT_DIR . '/alkivia.ini');
}

if ( ! defined('AK_CLASSES') ) {
    /** Define the classes folder */
    define ( 'AK_CLASSES', AKK_FRAMEWORK . '/classes');
}
if ( ! defined('AK_LIB') ) {
    /** Library folder for functions files */
    define ( 'AK_LIB', AKK_FRAMEWORK . '/lib');
}

// ============================================== SET GLOBAL ACTION HOOKS =====


// ================================================ INCLUDE ALL LIBRARIES =====

require_once ( AK_LIB . '/formating.php' );

require_once ( AK_LIB . '/themes-agapetry.php' );
require_once ( AK_LIB . '/users.php' );
