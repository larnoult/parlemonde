<?php

class Solvease_Roles_Capabilities_User_Caps
{

    public static function solvease_roles_capabilities_caps()
    {
        $caps = array(
            'manage_all_capabilities' => 'wprc_manage_all_capabilities',
            'manage_user_capabilities' => 'wprc_manage_user_capabilities',
            'add_new_role' => 'wprc_add_new_role',
            'delete_role' => 'wprc_delete_role',
            'change_default_role' => 'wprc_change_default_role',
            'add_new_capability' => 'wprc_add_new_capability',
            'remove_capability' => 'wprc_remove_capability',
            'rename_role' => 'wprc_rename_role',
            'export' => 'wprc_export_role_caps',
            'import' => 'wprc_import_role_caps'

        );
        return $caps;
    }

    public static function solvease_roles_capabilities_builtin_caps()
    {
        $built_in_capabilities = array(
            'plugins' => array(
                'name' => 'Plugins Related Capabilities',
                'cap' => array(
                    'activate_plugins' => array(
                        'name' => 'activate_plugins',
                        'desc' => 'Activate Plugins'
                    ),
                    'delete_plugins' => array(
                        'name' => 'delete_plugins',
                        'desc' => 'Delete Plugins'
                    ),
                    'edit_plugins' => array(
                        'name' => 'edit_plugins',
                        'desc' => 'Edit Plugins'
                    ),
                    'install_plugins' => array(
                        'name' => 'install_plugins',
                        'desc' => 'Install Plugins'
                    ),
                    'update_plugins' => array(
                        'name' => 'update_plugins',
                        'desc' => 'Update Plugins'
                    ),
                )
            ),
            'users' => array(
                'name' => 'Users Related Capabilities',
                'cap' => array(
                    'add_users' => array(
                        'name' => 'add_users',
                        'desc' => 'Add Users'
                    ),
                    'create_users' => array(
                        'name' => 'create_users',
                        'desc' => 'Create Users'
                    ),
                    'delete_users' => array(
                        'name' => 'delete_users',
                        'desc' => 'Delete Users'
                    ),
                    'edit_users' => array(
                        'name' => 'edit_users',
                        'desc' => 'Edit Users'
                    ),
                    'list_users' => array(
                        'name' => 'list_users',
                        'desc' => 'List Users'
                    ),
                    'promote_users' => array(
                        'name' => 'promote_users',
                        'desc' => 'Promote Users'
                    ),
                    'remove_users' => array(
                        'name' => 'remove_users',
                        'desc' => 'Remove Users'
                    ),
                )
            ),
            'themes' => array(
                'name' => 'Themes Related Capabilities',
                'cap' => array(
                    'delete_themes' => array(
                        'name' => 'delete_themes',
                        'desc' => 'Delete Themes'
                    ),
                    'edit_themes' => array(
                        'name' => 'edit_themes',
                        'desc' => 'Edit Themes'
                    ),
                    'install_themes' => array(
                        'name' => 'install_themes',
                        'desc' => 'Install Themes'
                    ),
                    'switch_themes' => array(
                        'name' => 'switch_themes',
                        'desc' => 'Switch Themes'
                    ),
                    'update_themes' => array(
                        'name' => 'update_themes',
                        'desc' => 'Update Themes'
                    ),
                    'edit_theme_options' => array(
                        'name' => 'edit_theme_options',
                        'desc' => 'Edit Themes Options'
                    ),
                )
            ),
            'posts' => array(
                'name' => 'Post Related Capabilities',
                'cap' => array(
                    'edit_others_posts' => array(
                        'name' => 'edit_others_posts',
                        'desc' => 'Edit Others Posts'
                    ),
                    'delete_others_posts' => array(
                        'name' => 'delete_others_posts',
                        'desc' => 'Delete Others Posts'
                    ),
                    'delete_private_posts' => array(
                        'name' => 'delete_private_posts',
                        'desc' => 'Delete Private Posts'
                    ),
                    'edit_private_posts' => array(
                        'name' => 'edit_private_posts',
                        'desc' => 'Edit Private Posts'
                    ),
                    'read_private_posts' => array(
                        'name' => 'read_private_posts',
                        'desc' => 'Read Private Posts'
                    ),
                    'edit_published_posts' => array(
                        'name' => 'edit_published_posts',
                        'desc' => 'Edit Published Posts'
                    ),
                    'publish_posts' => array(
                        'name' => 'publish_posts',
                        'desc' => 'Publish Posts'
                    ),
                    'delete_published_posts' => array(
                        'name' => 'delete_published_posts',
                        'desc' => 'Delete Published Posts'
                    ),
                    'edit_posts' => array(
                        'name' => 'edit_posts',
                        'desc' => 'Edit Posts'
                    ),
                    'delete_posts' => array(
                        'name' => 'delete_posts',
                        'desc' => 'Delete Posts'
                    ),
                    'manage_categories' => array(
                        'name' => 'manage_categories',
                        'desc' => 'Manage Categories'
                    ),
                )
            ),
            'pages' => array(
                'name' => 'Pages Related Capabilities',
                'cap' => array(
                    'edit_pages' => array(
                        'name' => 'edit_pages',
                        'desc' => 'Edit Pages'
                    ),
                    'edit_others_pages' => array(
                        'name' => 'edit_others_pages',
                        'desc' => 'Edit Others Pages'
                    ),
                    'edit_published_pages' => array(
                        'name' => 'edit_published_pages',
                        'desc' => 'Edit Published Pages'
                    ),
                    'publish_pages' => array(
                        'name' => 'publish_pages',
                        'desc' => 'Publish Pages'
                    ),
                    'delete_pages' => array(
                        'name' => 'delete_pages',
                        'desc' => 'Delete Pages'
                    ),
                    'delete_others_pages' => array(
                        'name' => 'delete_others_pages',
                        'desc' => 'Delete others pages'
                    ),
                    'delete_published_pages' => array(
                        'name' => 'delete_published_pages',
                        'desc' => 'Delete Published Pages'
                    ),
                    'delete_private_pages' => array(
                        'name' => 'delete_private_pages',
                        'desc' => 'Delete Private Pages'
                    ),
                    'edit_private_pages' => array(
                        'name' => 'edit_private_pages',
                        'desc' => 'Edit Private Pages'
                    ),
                    'read_private_pages' => array(
                        'name' => 'read_private_pages',
                        'desc' => 'Read Private Pages'
                    )
                )
            ),
            'media' => array(
                'name' => 'Media Related Capabilities',
                'cap' => array(
                    'edit_files' => array(
                        'name' => 'edit_files',
                        'desc' => 'Edit Files'
                    ),
                    'upload_files' => array(
                        'name' => 'upload_files',
                        'desc' => 'Upload Files'
                    ),
                    'unfiltered_upload' => array(
                        'name' => 'unfiltered_upload',
                        'desc' => 'Unfiltered Upload'
                    ),
                )
            ),
            'links' => array(
                'name' => 'Links Related Capabilities',
                'cap' => array(
                    'manage_links' => array(
                        'name' => 'manage_links',
                        'desc' => 'Manage Links'
                    ),
                )
            ),
            'import_export' => array(
                'name' => 'Import / Export Related Capabilities',
                'cap' => array(
                    'export' => array(
                        'name' => 'export',
                        'desc' => 'Export'
                    ),
                    'import' => array(
                        'name' => 'import',
                        'desc' => 'Import'
                    ),
                )
            ),
            'comments' => array(
                'name' => 'Comments Related Capabilities',
                'cap' => array(
                    'moderate_comments' => array(
                        'name' => 'moderate_comments',
                        'desc' => 'Moderate Comments'
                    ),
                    'edit_comment' => array(
                        'name' => 'edit_comment',
                        'desc' => 'Edit Comment'
                    ),
                )
            ),
            'dashboard' => array(
                'name' => 'Dashboard Related Capabilities',
                'cap' => array(
                    'edit_dashboard' => array(
                        'name' => 'edit_dashboard',
                        'desc' => 'Edit Dashboard'
                    ),
                    'read' => array(
                        'name' => 'read',
                        'desc' => 'Read'
                    ),
                )
            ),
            'admin_options' => array(
                'name' => 'Admin Options Capabilities',
                'cap' => array(
                    'manage_options' => array(
                        'name' => 'manage_options',
                        'desc' => 'manage_options'
                    ),
                    'update_core' => array(
                        'name' => 'update_core',
                        'desc' => 'Update Core'
                    ),
                    'unfiltered_html' => array(
                        'name' => 'Unfiltered HTML',
                        'desc' => 'Unfiltered HTML'
                    ),
                )
            ),
            'deprecated' => array(
                'name' => 'Deprecated Capabilities',
                'cap' => array(
                    'level_0' => array(
                        'name' => 'level_0',
                        'desc' => 'level_0'
                    ),
                    'level_1' => array(
                        'name' => 'level_1',
                        'desc' => 'level_1'
                    ),
                    'level_2' => array(
                        'name' => 'level_2',
                        'desc' => 'level_2'
                    ),
                    'level_3' => array(
                        'name' => 'level_3',
                        'desc' => 'level_3'
                    ),
                    'level_4' => array(
                        'name' => 'level_4',
                        'desc' => 'level_4'
                    ),
                    'level_5' => array(
                        'name' => 'level_5',
                        'desc' => 'level_5'
                    ),
                    'level_6' => array(
                        'name' => 'level_6',
                        'desc' => 'level_6'
                    ),
                    'level_7' => array(
                        'name' => 'level_7',
                        'desc' => 'level_7'
                    ),
                    'level_8' => array(
                        'name' => 'level_8',
                        'desc' => 'level_8'
                    ),
                    'level_9' => array(
                        'name' => 'level_9',
                        'desc' => 'level_9'
                    ),
                    'level_10' => array(
                        'name' => 'level_10',
                        'desc' => 'level_10'
                    ),
                )
            ),
        );

        return $built_in_capabilities;
    }

}
