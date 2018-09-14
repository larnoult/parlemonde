<?php
/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage Define Constants
 * @category Items
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.05.17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//   USERS  CONFIGURABLE  CONSTANTS           //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!defined('OPSD_SHOW_INFO_IN_FORM'))                define('OPSD_SHOW_INFO_IN_FORM',  false );                 // This feature can impact to the performance
if (!defined('OPSD_SHOW_OPSD_NOTES'))               define('OPSD_SHOW_OPSD_NOTES', false );                 // Set notes of the specific item visible by default.
if (!defined('OPSD_CUSTOM_FORMS_FOR_REGULAR_USERS'))   define('OPSD_CUSTOM_FORMS_FOR_REGULAR_USERS',  false );    // Only for MultiUser version 
if (!defined('OPSD_SHOW_DEPOSIT_AND_TOTAL_PAYMENT'))   define('OPSD_SHOW_DEPOSIT_AND_TOTAL_PAYMENT',  false );    // Show both deposit and total cost payment forms, after visitor submit item. Important! Please note, in this case at admin panel for item will be saved deposit cost and notes about deposit, do not depend from the visitor choise of this payment. So you need to check each such payment manually.
if (!defined('OPSD_STRICTLY_FROM_EMAILS'))             define('OPSD_STRICTLY_FROM_EMAILS',  true );               // If true, plugin will send emails with "From" address that  defined in "From" field at item > Settings > Emails page. Otherwise (if false), when sending the copy of Confirmation email to admin, sends a "from" field of email not the email of server, but email from the person, who made reservation. Its useful for "reply to this emails", but when receiving such email, Yahoo mail for instance rejects it, and google mail puts a warning about fishing etc.
if (!defined('OPSD_IS_SEND_EMAILS_ON_COST_CHANGE'))    define('OPSD_IS_SEND_EMAILS_ON_COST_CHANGE',  false );     //FixIn: 6.0.1.7   // Is send modification email, if cost  was changed in admin panel
if (!defined('OPSD_LAST_CHECKOUT_DAY_AVAILABLE'))      define('OPSD_LAST_CHECKOUT_DAY_AVAILABLE',  false );       //FixIn: 6.2.3.6   // Its will remove last selected day  of item during saving it as item. 
if (!defined('OPSD_PAYMENT_FORM_ONLY_IN_REQUEST'))     define('OPSD_PAYMENT_FORM_ONLY_IN_REQUEST', false );       // Its will show payment form  only in payment request during sending from  item Listing page and do not show payment form  after  visitor made the item.
if (!defined('OPSD_AUTO_APPROVE_WHEN_ZERO_COST'))      define('OPSD_AUTO_APPROVE_WHEN_ZERO_COST',  false );        // Auto  approve item,  if the cost of item == 0
if (!defined('OPSD_CHECK_LESS_THAN_PARAM_IN_SEARCH'))  define('OPSD_CHECK_LESS_THAN_PARAM_IN_SEARCH',  false );    // Check in search  results custom fields parameters relative to  less than  in search  form,  and not only equal.

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//   SYSTEM  CONSTANTS                        //////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!defined('OPSD_VERSION_NUM'))      define('OPSD_VERSION_NUM',     '1.0' );
if (!defined('OPSD_MINOR_UPDATE'))     define('OPSD_MINOR_UPDATE',    false );    
if (!defined('IS_USE_OPSD_CACHE'))  define('IS_USE_OPSD_CACHE', true );    
if (!defined('OPSD_DEBUG_MODE'))       define('OPSD_DEBUG_MODE',      false );
if (!defined('OPSD_MIN'))              define('OPSD_MIN',             false );//TODO: Finish  with  this contstant, right now its not working correctly with TRUE status
if (!defined('OPSD_RESPONSE'))         define('OPSD_RESPONSE',        false ); 