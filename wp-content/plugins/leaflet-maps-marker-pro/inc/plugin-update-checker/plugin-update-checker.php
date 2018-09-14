<?php
/**
 * Plugin Update Checker Library 4.1
 * http://w-shadow.com/
 * last commit: 16.5.2017 (Fix an issue with Debug Bar 0.9 where the content of some PUC panels)
 * 
 * Copyright 2017 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/Puc/v4/Factory.php';
require dirname(__FILE__) . '/Puc/v4p1/Autoloader.php';
new MMPPuc_v4p1_Autoloader();

//Register classes defined in this file with the factory.
MMPPuc_v4_Factory::addVersion('Plugin_UpdateChecker', 'MMPPuc_v4p1_Plugin_UpdateChecker', '4.1');
//MMPPuc_v4_Factory::addVersion('Theme_UpdateChecker', 'MMPPuc_v4p1_Theme_UpdateChecker', '4.1');

MMPPuc_v4_Factory::addVersion('Vcs_PluginUpdateChecker', 'MMPPuc_v4p1_Vcs_PluginUpdateChecker', '4.1');
//MMPPuc_v4_Factory::addVersion('Vcs_ThemeUpdateChecker', 'MMPPuc_v4p1_Vcs_ThemeUpdateChecker', '4.1');

//MMPPuc_v4_Factory::addVersion('GitHubApi', 'MMPPuc_v4p1_Vcs_GitHubApi', '4.1');
//MMPPuc_v4_Factory::addVersion('BitBucketApi', 'MMPPuc_v4p1_Vcs_BitBucketApi', '4.1');
