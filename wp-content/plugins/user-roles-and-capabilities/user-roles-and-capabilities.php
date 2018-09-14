<?php
/*
  Plugin Name: User Roles and Capabilities
  Plugin URI: http://solvease.com
  Description: manage user roles and capabilities. Create new roles and delete existing roles. Using this plugin you will not be able to modify any capabilities for administrator user role. WordPress built in roles cant be deleted.
  Version: 1.2.3
  Author: mahabub
  Author URI: http://solvease.com
 License: GPLv2 or later
 */

/*  Copyright 2015 Mahabub (email: mahabub at solvease dot com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('WPINC')) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-solvease-wp-roles-capabilities-caps.php';

function activate_solvease_roles_capabilities() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-solvease-wp-roles-capabilities-activator.php';
    Solvease_Roles_Capabilities_Activator::activate();
}

function deactivate_solvease_roles_capabilities() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-solvease-wp-roles-capabilities-deactivator.php';
    Solvease_Roles_Capabilities_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_solvease_roles_capabilities');
register_deactivation_hook(__FILE__, 'deactivate_solvease_roles_capabilities');


require_once plugin_dir_path(__FILE__) . "includes/class-solvease-wp-roles-capabilities.php";

function execute_solvease_roles_capabilities() {
    $solvease_roles_capabilities = new Solvease_Roles_Capabilities();
    $solvease_roles_capabilities->execute();
}

execute_solvease_roles_capabilities();
