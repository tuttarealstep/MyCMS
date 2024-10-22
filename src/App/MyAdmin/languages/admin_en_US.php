<?php
/*                     *\
|	MyCMS    |
\*                     */

//LINGUA : ITALIANA
$language = [

    'Lorem ipsum'                                => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',

    //PAGE NAME
    'page_login_page_name'                       => 'Administration Panel',

    //ERROR
    'error_email_password'                       => "The email or password are incorrect.",
    'error_wrong_new_password'                   => "The new password is too short or too long!",
    'error_wrong_password'                       => "The password is incorrect.",
    'error_repeat_password'                      => "The passwords won't match!",
    'error_page_settings_general_name'           => "You must enter the name of the site!",
    'error_page_settings_general_url'            => "You must enter the address of your site!",
    'error_page_settings_general_save'           => "Error on saving changes, contact the administrator!",

    //LOGIN
    'page_login_panel-title'                     => '{@siteNAME@} Administration Panel',
    'page_login_placeholder_email'               => 'E-mail',
    'page_login_placeholder_password'            => 'Password',
    'page_login_remember'                        => 'Remember',
    'page_login_button'                          => 'Login',
    'page_home_li_return_site'                   => 'Return to site',

    //TOPBAR
    'topbar_li_user_profile'                     => 'User Profile',
    'topbar_li_settings'                         => 'Settings',
    'topbar_li_logout'                           => 'Logout',
    'topbar_li_return_site'                      => 'Return to site',
    'topbar_menu_users'                          => 'Users',

    //Dashboard
    'page_home_page_name'                        => 'Dashboard',
    'page_home_page_header'                      => 'Dashboard',
    'page_home_general_info'                     => 'General Information',
    'page_home_info_in_use'                      => 'You\'re are using',
    'page_home_info_theme_in_use'                => 'You\'re using the theme:',
    'page_home_info_theme_created_by'            => 'Created by:',
    'page_home_general_info_post'                => 'Posts',
    'page_home_general_info_comments'            => 'Comments',
    'page_home_general_info_category'            => 'Categories',
    'page_home_general_info_notifications'       => 'Notifications',
    'page_home_no_notifications'                 => 'There are no notifications.',
    'page_home_danger_info'                      => 'MyCMS version <b>{@my_cms_version@}</b> is developing if you encounter bugs or other problems report them on GitHub!',
    'page_home_general_info_update_all'          => 'There is a new version of MyCMS ( Core and Database, Your data will not be changed )',
    'page_home_general_info_db_update'           => 'There is a new version for the database of MyCMS( Your data will not be changed )',
    'page_home_general_info_core_update'         => 'There is a new version of MyCMS ( Core, Your data will not be changed )',
    'page_home_general_info_button_update'       => 'Update',

    //Settings
    'page_settings_page_name'                    => 'Settings',
    'page_settings_general'                      => 'General',
    'page_settings_page_header'                  => 'General Settings',
    'page_settings_site_name'                    => 'Site Name',
    'page_settings_site_description'             => 'Site Description',
    'page_settings_site_description_help'        => 'Simply put a description of your site',
    'page_settings_site_url'                     => 'Site Url',
    'page_settings_site_url_help'                => 'Enter the address of your website without "/" final. Ex: http://localhost',
    'page_settings_site_timezone'                => 'Time Zone',
    'page_settings_site_timezone_help'           => 'Choose the time zone for your site',
    'page_settings_site_button_save'             => 'Save',
    'page_settings_site_mainteinance'            => 'Maintenance',
    'page_settings_site_mainteinance_on'         => 'Maintenance Enabled',
    'page_settings_site_mainteinance_off'        => 'Maintenance Off',
    'page_settings_site_web_private'             => 'Website private (no robots)',
    'page_settings_site_web_private_on'          => 'Enabled',
    'page_settings_site_web_private_off'         => 'Disabled',
    'page_settings_site_use_cache'               => 'Use cache (if Twig template engine enabled)',
    'page_settings_site_use_cache_on'            => 'Enabled',
    'page_settings_site_use_cache_off'           => 'Disabled',
    'page_settings_site_reload_htaccess'         => 'Reload .htaccess?',
    'page_settings_site_reload_htaccess_button'  => 'Reload',
    'page_settings_blog'                         => 'Blog',
    'page_settings_blog_header'                  => 'Blog Settings',
    'page_settings_blog_private'                 => 'Show Blog',
    'page_settings_blog_private_on'              => 'Yes',
    'page_settings_blog_private_off'             => 'No',
    'page_settings_blog_comments_active'         => 'Users can comment?',
    'page_settings_blog_comments_active_on'      => 'Yes',
    'page_settings_blog_comments_active_off'     => 'No',
    'page_settings_blog_comments_approve'        => 'The comments made are approved?',
    'page_settings_blog_comments_approve_on'     => 'Yes',
    'page_settings_blog_comments_approve_off'    => 'No',
    'page_settings_style'                        => 'Style',
    'page_settings_style_header'                 => 'Style and Language Settings',
    'page_settings_my_admin_language'            => 'MyAdmin Language',
    'page_settings_theme_language'               => 'Theme Language',
    'page_settings_theme_language_info'          => 'If you change theme remember of save before change language!',
    'page_settings_style_template'               => 'Site Theme',
    'page_settings_site_button_clear_all_caches' => "Clear all caches",

    //Posts
    'page_posts_name'                            => 'Post',
    'page_posts_all'                             => 'All Posts',
    'page_posts_header'                          => 'All Posts',
    'page_posts_header_create_new'               => 'Create a new post',
    'page_posts_table_title'                     => 'Title',
    'page_posts_table_author'                    => 'Author',
    'page_posts_table_category'                  => 'Category',
    'page_posts_table_date'                      => 'Date',
    'page_posts_table_select'                    => 'Select',
    'page_posts_table_status'                    => 'Status',

    //Table Post
    '_table_sEmptyTable'                         => 'There are no data in the table',
    '_table_sInfo'                               => 'View by _START_ to _END_ of _TOTAL_ elements',
    '_table_sInfoEmpty'                          => 'View 0 to 0 of 0 elements',
    '_table_sInfoFiltered'                       => '(filtered by _MAX_ total items)',
    '_table_sLengthMenu'                         => 'View _MENU_ elements',
    '_table_sLoadingRecords'                     => 'Loading...',
    '_table_sProcessing'                         => 'Processing...',
    '_table_sSearch'                             => 'Search:',
    '_table_sZeroRecords'                        => 'The research did not bring any result.',
    '_table_sFirst'                              => 'First',
    '_table_sPrevious'                           => 'Previous',
    '_table_sNext'                               => 'Next',
    '_table_sLast'                               => 'Last',
    '_table_sSortAscending'                      => ': active to sort the column in ascending order',
    '_table_sSortDescending'                     => ': active to sort the column in descending order',
    'page_posts_if_check'                        => 'If selected:',
    'page_posts_check_delete'                    => 'Delete',
    'page_posts_check_edit'                      => 'Edit',
    'page_posts_check_button'                    => 'Do',
    'page_posts_delete_successfull'              => 'Articles cleared successfully',
    'page_posts_delete_empty_checklist'          => 'You haven\'t selected anything',

    //Posts New
    'page_post_create'                             => 'Add a new Post',
    'page_post_create_header'                      => 'Create a New Post',
    'page_post_create_title'                       => 'Title',
    'page_post_create_publish'                     => 'Publication',
    'page_post_create_publish_button'              => 'Publish',
    'page_post_create_permalink'                   => 'Permalink:',
    'page_post_create_permalink_info'              => 'If the title is the same as another article the permalink will add a number ex:<br> {@siteURL@}/2014/10/test_123',
    'page_post_create_category'                    => 'Categories',
    'page_post_create_select_category'             => 'Select Category',
    'page_post_create_error_title'                 => 'Add a title',
    'page_post_create_error_content'               => 'Enter the content',
    'page_post_create_success_posted'              => 'Article posted successfully,',
    'page_post_create_success_show'                => 'Go to the article!',
    'page_posts_category_new_placeholder'        => 'Category name...',
    'page_posts_category_new_button'             => 'Add new category',

    'page_post_create_label_published'             => 'Publish',
    'page_post_create_status_label'                => 'Status:',
    'page_post_create_label_edit_status'           => 'Edit',
    'page_post_create_label_pending_review'        => 'Pending Review',
    'page_post_create_label_draft'                 => 'Draft',
    'page_post_create_label_ok'                    => 'OK',
    'page_post_create_label_cancel'                => 'Cancel',
    'page_post_create_category_button'             => 'Add new category +',
    'page_post_create_date_label' => 'Date:',
    'page_post_create_date_now' => 'Now',
    'page_post_create_date_planned' => 'planned',
    'page_post_create_label_edit_date'           => 'Edit',
    'page_post_create_label_edit_date_at'           => 'at',
    'page_post_create_select_option_no_category' => 'Without category',
    'page_post_create_edit'                           => 'Edit Post',
    'page_post_create_edit_new_success_posted'         => 'Article edited successfully,',
    'page_post_create_edit_new_header'                 => 'Edit Post',

    //Commenti
    'page_comments_page_name'                    => 'Comments',
    'page_comments_header'                       => 'All comments',
    'page_comments_table_author'                 => 'Author',
    'page_comments_table_date'                   => 'Date',
    'page_comments_table_select'                 => 'Select',
    'page_comments_table_comment'                => 'Comment',
    'page_comments_delete_successfull'           => 'Message deleted successfully!',
    'page_comments_delete_empty_checklist'       => 'You haven\'t selected any comments!',
    'page_comments_table_approved'               => 'Approved Comment?',
    'page_comments_table_approved_no'            => 'No',
    'page_comments_table_approved_yes'           => 'Yes',
    'page_comments_check_delete'                 => 'Delete',
    'page_comments_check_approve'                => 'Approve',
    'page_comments_check_button'                 => 'Do',
    'page_comments_approve_successfull'          => 'Message approved successfully',

    //Categorie
    'page_category_name'                         => 'Categories',
    'page_category_header'                       => 'All Categories',
    'page_category_table_name'                   => 'Name',
    'page_category_table_description'            => 'Description',
    'page_category_table_post'                   => 'Post',
    'page_category_table_select'                 => 'Select',
    'page_category_check_delete'                 => 'Delete',
    'page_category_check_button'                 => 'Do',
    'page_category_delete_successfull'           => 'Did you delete the category and successfully!',
    'page_category_delete_empty_checklist'       => 'You did not select any category!',
    'page_category_add_new_category'             => 'Create new category',
    'page_category_add_new_category_name'        => 'Name',
    'page_category_add_new_category_description' => 'Description',
    'page_category_add_new_category_button'      => 'Create',
    'page_category_delete_empty_name'            => 'You must enter a name!',
    'page_category_added_successful'             => 'Category added successfully!',
    'page_category_error_category_in_use'        => 'Category in use!',
    'page_category_error_already_category_in_use'        => 'Category already in use!',
    'page_category_check_edit'                 => 'Edit',
    'page_category_edit_category_button' => 'Save',
    'page_category_edit_successful' => 'Category edited successfully',

    //Menu
    'page_menu_page_name'                        => 'Menu',
    'page_menu_header'                           => 'Main Menu',
    'page_menu_delete'                           => 'Delete',
    'page_menu_add_new_menu'                     => 'Add a page to the menu',
    'page_menu_add_new_menu_name'                => 'Name',
    'page_menu_add_new_menu_selectpage'          => 'Select a page',
    'page_menu_empty_page'                       => 'I want to use a custom link',
    'page_menu_add_button'                       => 'Add',
    'page_menu_personal_url'                     => 'Personal Link:',
    'page_menu_add_success'                      => 'Menu successfully added!',
    'page_menu_error_empty_personal_url'         => 'You can not not insert any link!',
    'page_menu_error_add_name'                   => 'You must enter the name',
    'page_menu_icon'                             => 'Icon',
    'page_menu_down'                             => 'Down',
    'page_menu_up'                               => 'Up',

    //RANK
    'page_menu_page_ranks'                       => 'User Permissions',
    'page_ranks_page_name'                       => 'User Permissions / Manage ranks',
    'page_ranks_header'                          => 'User Permissions / Manage ranks',
    'page_ranks_1'                               => 'User',
    'page_ranks_2'                               => 'Moderator / Blogger',
    'page_ranks_3'                               => 'Administrator',
    'page_ranks_table_user'                      => 'User',
    'page_ranks_table_rank'                      => 'Rank',
    'page_ranks_table_name_rank'                 => 'Role',
    'page_ranks_give_user_title'                 => 'Promote a user',
    'page_ranks_give_user_email'                 => 'User Email',
    'page_ranks_button_promote'                  => 'Promote',
    'page_ranks_error_1'                         => 'You have promoted the user successfully!',
    'page_ranks_error_2'                         => 'The user does not exist!',
    'page_ranks_error_3'                         => 'Enter the user\'s email',
    'page_ranks_error_4'                         => 'You are not allowed to promote a user',
    'page_ranks_table_mail'                      => 'Email',

    //XML COMMAND
    'page_settings_xml_command'                  => 'Xml command/s',
    'page_settings_xml_button'                   => 'Run',
    'page_settings_xml_command_header'           => 'Run XML Command/s',
    'page_settings_xml_command_text'             => 'Insert:',

    //USERS BAN
    'page_users_bans_page_name'                  => 'Banned users',
    'page_ban_user_title'                        => 'Ban a user',
    'page_ban_user_email'                        => 'User Email:',
    'page_ban_or_ip'                             => 'Or ip:',
    'page_ban_expire_date_select'                => 'Select Expire Date:',
    'page_ban_button_ban'                        => 'Ban',
    'page_ban_ip'                                => 'IP Banned:',
    'page_ban_expire_date'                       => 'Expire Date:',
    'page_ban_select_2_hours'                    => '2 Hours',
    'page_ban_select_1_day'                      => '1 Day',
    'page_ban_select_1_month'                    => '1 Month',
    'page_ban_select_1_year'                     => '1 Year',

    //USERS INFO
    'page_users_info_page_name'                  => 'Info Users',
    'page_info_users_title'                      => 'Info Users',
    'page_users_info_id'                         => 'ID',
    'page_users_info_name'                       => 'Name',
    'page_users_info_surname'                    => 'Surname',
    'page_users_info_mail'                       => 'Mail',
    'page_users_info_ip'                         => 'IP',
    'page_users_info_rank'                       => 'Rank',
    'page_users_info_last_access'                => 'Last Access',

    //NEW USER
    'page_users_new_page_name'                   => 'New Users',
    'page_users_new_title'                       => 'New Users',
    'page_new_user_name_label'                   => 'Name:',
    'page_new_user_name_placeholder'             => 'Name',
    'page_new_user_surname_label'                => 'Surname:',
    'page_new_user_surname_placeholder'          => 'Surname',
    'page_new_user_email_label'                  => 'Email:',
    'page_new_user_email_placeholder'            => 'Email',
    'page_new_user_password_label'               => 'Password:',
    'page_new_user_password_placeholder'         => 'Password',
    'page_new_user_add_new_button'               => 'Add user',
    'error_name'                                 => "Name is too short or too long",
    'error_surname'                              => "Surname too short, or too long",
    'error_email'                                => "Invalid Email",
    'error_password'                             => "Password too short",
    'error_email_in_use'                         => "This email address is already registered to another user",
    'page_new_user_created'                      => 'New user created successfully!',

    //MY PAGE
    'page_pages_page_name'                       => 'My Page',
    'page_pages_check_delete'                    => 'Delete',
    'page_pages_check_edit'                      => 'Edit',
    'page_pages_check_export'                    => 'Export',
    'page_pages_check_button'                    => 'Do',
    'page_pages_delete_successfully'             => 'Page/s deleted successfully',
    'page_pages_delete_empty_checklist'          => 'You haven\'t selected anything',
    'page_pages_table_page-id'                   => 'Page ID',
    'page_pages_table_page-title'                => 'Title',
    'page_pages_table_page-url'                  => 'Page url',
    'page_pages_table_page_id'                   => 'Menu ID',
    'page_pages_table_status'                    => 'Status',
    'page_pages_status_published'                => 'Published',
    'page_pages_status_publish'                  => 'Publish',
    'page_pages_status_draft'                    => 'Draft',
    'page_pages_new_label_edit_status'           => 'Edit',
    'page_pages_new_label_ok'                    => 'Ok',
    'page_pages_new_label_cancel'                => 'Cancel',
    'page_pages_new_status_label'                => 'Status:',
    'page_pages_table_select'                    => 'Select',
    'page_pages_header_create_new'               => 'Create New Page',

    //MY PAGE NEW
    'page_pages_new'                             => 'Add a new Page',
    'page_pages_new_header'                      => 'Create a New Page',
    'page_pages_new_title'                       => 'Title:',
    'page_pages_new_publish'                     => 'Publication',
    'page_pages_new_publish_button'              => 'Create',
    'page_pages_new_error_title'                 => 'Add a title',
    'page_pages_new_success_created'             => 'Page created successfully,',
    'page_pages_new_success_show'                => 'Go to the page!',
    'page_pages_new_info'                        => '<b>You can use tag like: <br> siteURL , siteNAME...  <br> all inside " {@ " and " @} example: {@example@}"</b>',
    'page_pages_header_import'                   => 'Import Page',

    //MY PAGE EDIT
    'page_pages_edit'                            => 'Modify Page',
    'page_pages_edit_header'                     => 'Modify Page',
    'page_pages_edit_title'                      => 'Title:',
    'page_pages_edit_publish'                    => 'Publication',
    'page_pages_edit_publish_button'             => 'Modify',
    'page_pages_edit_error_title'                => 'Add a title',
    'page_pages_edit_success_created'            => 'Page modified successfully,',
    'page_pages_edit_success_show'               => 'Go to the page!',
    'page_pages_edit_info'                       => '<b>You can use tag like: <br> siteURL , siteNAME...<br> all inside " {@ " and " @} example: {@example@}"</b>',
    'page_theme_manager'                         => 'Themes',
    'page_theme_manager_header'                  => 'Theme Manager',
    'page_theme_manager_add_new_theme'           => 'Add a new Theme',
    'page_theme_manager_labe_json_url'           => 'URL info.json',
    'page_theme_manager_add_button'              => 'Add',
    'page_theme_manager_button_info'             => 'Info',
    'page_theme_manager_button_remove'           => 'Remove',
    'page_theme_manager_version_label'           => 'Version',
    'page_theme_manager_author_label'            => 'Author',
    'page_theme_manager_set_button'              => 'Set',
    'page_theme_manager_theme_by'                => 'By',
    'page_theme_manager_edit_in_code_editor'     => 'Edit in Code Editor',
    'page_theme_manager_upload_new_theme'        => 'Upload new theme',
    'page_theme_manager_placeholder_upload'      => 'Choose the .zip file of the theme',
    'page_theme_manager_upload_button'           => 'Upload',
    'error_admin_permissions'                    => 'You not have permissions to complete this action!',
    'page_theme_manager_customizer'              => "Customize",
    //Page import
    'page_pages_import_title'                    => 'Import page',
    'page_pages_import_title_head'               => 'Insert the json code of the page:',
    'page_pages_import_button'                   => 'Import',

    //Update Page
    'page_update_page_name'                      => 'Update',
    'page_update_return_back'                    => 'Return Back',
    'page_update_changelog'                      => 'Changelog',
    'page_update_update_button'                  => 'Update',
    'page_update_alert'                          => 'Don\'t close the page, if you close you may have some errors!',
    'page_update_info_process'                   => 'Do not close the page you will be automatically redirected at the end of the process!',

    'page_settings_user'                             => 'User Settings',
    'page_settings_user_header'                      => 'User Settings',
    'page_settings_user_change_old_password'         => 'Current password:',
    'page_settings_user_change_new_password'         => 'New Password:',
    'page_settings_user_change_password_repeat'      => 'Repeat the new password:',
    'page_settings_user_placeholder_old_password'    => 'Old password',
    'page_settings_user_placeholder_new_password'    => 'New Password',
    'page_settings_user_placeholder_password_repeat' => 'Repeat password',
    'page_settings_user_save_button'                 => 'Update',
    'page_settings_user_change_admin_style'          => 'Admin color scheme',

    'page_admin_code_editor_page_name'          => 'Code editor',
    'page_admin_code_editor_theme_not_found'    => 'Theme not found! Default Theme Loaded!',
    'page_admin_code_editor_create_new_file'    => 'Create new file? If you do not save your current changes, they will be lost when you confirm!',
    'page_admin_code_editor_t_file_name'        => 'Please enter file name (with extension):',
    'page_admin_code_editor_t_file_created'     => 'File created',
    'page_admin_code_editor_t_file_not_created' => 'File not created!',
    'page_admin_code_editor_t_save_file'        => 'Save file?',
    'page_admin_code_editor_t_file_saved'       => 'File saved!',
    'page_admin_code_editor_t_line'             => 'Line',
    'page_admin_code_editor_t_column'           => 'Column',
    'page_admin_code_editor_t_if_you_confirm'   => 'If you confirm, you\'ll lose your changes!',
    'page_admin_code_editor_t_file_loaded'      => 'File Loaded!',
    'page_admin_code_editor_t_setup_complete'   => 'Setup Complete!',
    'page_admin_code_editor_t_are_you_sure'     => 'Are you sure?',
    'page_admin_code_editor_return_back'        => 'Return Back',
    'page_admin_code_editor_save_current_file'  => 'Save current file',
    'page_admin_code_editor_new_file'           => 'New file',


    'page_admin_theme_customize_page_name'                  => 'Theme Customize',
    'page_admin_theme_customize_you_are_customizing'        => 'You are customizing',
    'page_admin_theme_customize_save'                       => 'SAVE',
    'page_admin_theme_customize_go_back'                    => 'Go Back',
    'page_admin_theme_customize_customizing'                => 'Customizing',
    'page_admin_theme_customize_confirm_to_save'            => 'Confirm if you want save your current page changes',
    'page_admin_theme_customize_site_info_menu'             => 'Site info',
    'page_admin_theme_customize_site_info_menu_title'       => 'Title',
    'page_admin_theme_customize_site_info_menu_description' => 'Description',
    'page_admin_theme_customize_page_editor_menu'           => 'Page editor',
    'page_admin_theme_customize_page_editor_menu_save_page' => 'SAVE PAGE',
    'page_admin_theme_customize_t_exit_now'                 => 'Exit now? If you do not save your current changes, they will be lost when you confirm!',
    'page_admin_theme_customize_change_page'                => 'Change page? If you do not save your current changes, they will be lost when you confirm!',

    //upload
    'page_upload'                             => 'Media',
    'page_upload_library'                             => 'Library',
    'page_upload_add_new'                             => 'Add new',
    'page_upload_header'                             => 'Media library',
    'page_upload_new_header' => 'Add new media',
    'page_upload_new_drag_dropFile' => 'Drag & drop',
    'page_upload_new_or_select' => 'your files here, or',
    'page_upload_new_select_button' => 'select',
    'upload_permission_denied' => 'Permission denied',
    'upload_maximum_file_upload_size' => 'The file size exceeds the max size',
    'upload_error_file_not_supported' => 'For security reason this file cannot be uploaded',
    'upload_successful_error' => 'File uploaded successfully!',
    'upload_max_file_dimension_label' => 'Maximum file dimension size:',
    'upload_edit_label' => 'Edit',
    'page_upload_edit_media_header' => 'Edit media',
    'page_upload_edit_media_add_new_button' => 'Add new',
    'page_upload_edit_media_title_input' => 'Title',
    'page_upload_edit_media_save_panel' => 'Save',
    'page_upload_edit_media_mediaUrlLabel' => 'File URL',
    'page_upload_edit_media_mediaPostDate' => 'Published on:',
    'page_upload_edit_media_mediaFileName' => 'File name:',
    'page_upload_edit_media_mediaFileType' => 'File type:',
    'page_upload_edit_media_mediaFileSize' => 'File size:',
    'page_upload_edit_media_mediaDelete' => 'Delete media',
    'page_upload_edit_media_caption_input' => 'Caption',
    'page_upload_edit_media_description_input' => 'Description',
    'page_upload_edit_media_updateButton' => 'Update',
    'page_upload_media_add_new_button' => 'Add new',

    'page_upload_search_bar_option_all' => 'All media',
    'page_upload_search_bar_option_images' => 'Images',
    'page_upload_search_bar_option_videos' => 'Video',
    'page_upload_search_bar_option_audio' => 'Audio',
    'page_upload_search_bar_search_media' => 'Search media...',
    'page_upload_search_bar_search_not_found' => 'No media found',
    'page_upload_moreInfoEdit' => 'More info / Edit',
    'upload_addon_menu' => 'Add media',

    'administrator' => 'Administrator',
    'user' => 'User',
    'contributor' => 'Contributor',
    'author' => 'Author',
    'editor' => 'Editor',

    'page_plugins_header' => 'Plugins',
    'page_plugins_table_name' => 'Name',
    'page_plugins_table_description' => 'Description',
    'page_plugins_manage' => 'Enable/Disable',
    'page_plugins_menu_title' => 'Plugins',
    'page_plugins_disable' => 'Disable',
    'page_plugins_enable' => 'Enable',
    'page_users_info_edit' => 'Edit',
    'page_users_info_delete' => 'Delete',

    'page_users_info_edit_username' => 'Username',
    'page_users_info_edit_name' => 'Name',
    'page_users_info_edit_surname' => 'Surname',
    'page_users_info_email' => 'Email',
    'page_users_info_update_button' => 'Update',
    'page_users_info_page_name_update' => 'Edit User',
    'page_users_info_page_name_delete' => 'Delete User',
    'page_users_info_delete_info' => 'You have selected the following user to delete it:',
    'page_users_info_delete_info_contents' => 'What you want to do to the user\'s contents:',
    'page_users_info_delete_button' => 'Delete',
    'page_users_info_delete_contents_delete' => 'Delete contents',
    'page_users_info_delete_contents_transfer' => 'Transfer contents',
    'page_users_info_delete_contents_transfer_to' => ' to:',
    ];
?>
