/**
 * @version 1.0
 * @package Secure Downloads 
 * @subpackage JS Variables
 * @category Scripts
 * 
 * @author wpdevelop
 * @link http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2014.05.20
 */

////////////////////////////////////////////////////////////////////////////////
// Eval specific variable value (integer, bool, arrays, etc...)
////////////////////////////////////////////////////////////////////////////////

function opsd_define_var( opsd_global_var ) {
    if (opsd_global_var === undefined) { return null; }
    else { return jQuery.parseJSON(opsd_global_var); }                          //FixIn:6.1
}

////////////////////////////////////////////////////////////////////////////////
// Define global Secure Downloads Varibales based on Localization
////////////////////////////////////////////////////////////////////////////////
var opsd_ajaxurl                       = opsd_global1.opsd_ajaxurl; 
var opsd_plugin_url                    = opsd_global1.opsd_plugin_url;
var opsd_today                         = opsd_define_var( opsd_global1.opsd_today );
var opsd_plugin_filename               = opsd_global1.opsd_plugin_filename;
var message_verif_requred               = opsd_global1.message_verif_requred;
var message_verif_requred_for_check_box = opsd_global1.message_verif_requred_for_check_box;
var message_verif_requred_for_radio_box = opsd_global1.message_verif_requred_for_radio_box;
var message_verif_emeil                 = opsd_global1.message_verif_emeil;
var message_verif_same_emeil            = opsd_global1.message_verif_same_emeil;
var opsd_active_locale                  = opsd_global1.opsd_active_locale;
var opsd_message_processing             = opsd_global1.opsd_message_processing;
var opsd_message_deleting               = opsd_global1.opsd_message_deleting;
var opsd_message_updating               = opsd_global1.opsd_message_updating;
var opsd_message_saving                 = opsd_global1.opsd_message_saving;