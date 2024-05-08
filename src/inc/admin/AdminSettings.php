<?php

namespace verifywoo\inc\admin;

use const verifywoo\PLUGIN_PREFIX;

use verifywoo\core\Template;
use verifywoo\core\Cron;
use verifywoo\core\Users;

defined('ABSPATH') || exit;

class AdminSettings {
    public static function admin_menu() {
        add_menu_page(__('Verify Woo', 'verifywoo'), __('Verify Woo', 'verifywoo'), 'activate_plugins', PLUGIN_PREFIX, [self::class, 'admin_settings_page_callback'], 'dashicons-shield', 55.5);
        add_submenu_page(PLUGIN_PREFIX,  __('Verify Woo Settings', 'verifywoo'), __('Settings', 'verifywoo'), "activate_plugins", PLUGIN_PREFIX);
        add_submenu_page(PLUGIN_PREFIX, __('Verify Woo User Status', 'verifywoo'), __('User Status', 'verifywoo'), 'activate_plugins', PLUGIN_PREFIX . '-users', [self::class, 'admin_users_page_callback']);
    }

    public static function add_settings_sections() {
        $settings_sections = self::get_settings();

        foreach ($settings_sections as $section) {
            add_settings_section($section['id'], $section['title'], $section['callback'] ?? [self::class, 'settings_section_callback'], 'verifywoo', $section['args']);
            foreach ($section['settings'] as $setting) {
                add_settings_field($setting['id'], $setting['title'], [self::class, 'add_settings_field_callback'], PLUGIN_PREFIX, $section['id'], $setting['template_data']);
            }
        }
    }

    public static function register_settings() {
        $settings_sections = self::get_settings();
        foreach ($settings_sections as $section) {
            foreach ($section['settings'] as $setting) {
                if (isset($setting['skip_register_setting'])) continue;
                if (isset($setting['on_change'])) {
                    add_filter('update_option_' . $setting['id'], ...$setting['on_change']);
                }

                register_setting(PLUGIN_PREFIX, $setting['id'], $setting['register_data']);
            }
        }
    }

    public static function admin_settings_page_callback() {
        Template::include('admin/page-settings');
    }

    public static function admin_users_page_callback() {
        Template::include('admin/page-users');
    }

    public static function settings_section_callback($data) {
        echo '<p>' . $data['description'] ?? null . '</p>';
    }

    public static function delete_settings_section_callback($data) {
        echo '<p style="font-weight: bold; color: #ff0000">Danger: users will be permenantly deleted. This cannot be undone without a backup.</p><p>' . $data['description'] ?? null . '</p>';
    }

    public static function add_settings_field_callback($data) {
        Template::include($data['template'], $data);
    }

    private static function get_deletable_roles() {
        $roles = Users::get_roles(true);
        unset($roles['administrator']);

        return $roles;
    }

    private static function get_default_deletable_roles() {
        $roles = self::get_deletable_roles();
        $defaults = array_filter($roles, function ($item) {
            return $item !== 'customer';
        });

        return $defaults;
    }

    private static function get_settings() {
        return  [
            'registration_settings' => [
                'id' => PLUGIN_PREFIX . '_registration_settings',
                'title' => __('Registration Settings', 'verifywoo'),
                'args' => [
                    'description' => __('These settings are tied to the registration process. They are called when a new user is registered.', 'verifywoo'),
                ],
                'settings' => [
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_min_password_strength'),
                        'title' => ($title =  __('Min password strength', 'veifywoo')),
                        'description' => ($description = __('Min password strength for new user signup. (1 - weakest, 4 - strongest)', 'verifywoo')),
                        'register_data' => [
                            'type' => 'number',
                            'default' => 3,
                            'description' => $description,
                            'show_in_rest' => true

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/select',
                            'label_for' => $id,
                            'options' => [1, 2, 3, 4],
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_verification_expiration_length'),
                        'title' => ($title =  __('Verification expiration length', 'veifywoo')),
                        'description' => ($description = __('Amount of time new users have to verify their email before the token expires. If not verified the user will have to reasend their verification link.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'string',
                            'default' => '1 hour',
                            'description' => $description,
                            'show_in_rest' => true

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/select',
                            'label_for' => $id,
                            'options' => ['5 minutes', '15 minutes', '30 minutes', '1 hour', '12 hours', '1 day', '7 days'],
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_include_retype_password'),
                        'title' => ($title =  __('Include retype password', 'veifywoo')),
                        'description' => ($description = __('Include the retype password field when a new user registers. This helps ensure the user types in the correct password when registering.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'boolean',
                            'default' => true,
                            'description' => $description,
                            'show_in_rest' => true

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/checkbox',
                            'label_for' => $id,
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ]
                ]
            ],
            'email_settings' => [
                'id' => PLUGIN_PREFIX . '_email_settings',
                'title' => __('Email Settings', 'verifywoo'),
                'args' => [
                    'description' => __('General email settings. These settings are used to form the email content. The default woocommerce header and footer are used for email templates.', 'verifywoo'),
                ],
                'settings' => [
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_use_plaintext_emails'),
                        'title' => ($title =  __('Use plaintext emails', 'veifywoo')),
                        'description' => ($description = __('Use plaintext emails instead of html emails.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => $description,
                            'show_in_rest' => true

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/checkbox',
                            'label_for' => $id,
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_verification_email_subject'),
                        'title' => ($title =  __('Verification email subject', 'veifywoo')),
                        'description' => ($description = __('The subject of the verifiction email.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'string',
                            'default' => __('Verify your email', 'verifywoo'),
                            'description' => $description,
                            'show_in_rest' => true
                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/input',
                            'label_for' => $id,
                            'type' => 'text',
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_verification_email_heading'),
                        'title' => ($title =  __('Verification email heading', 'veifywoo')),
                        'description' => ($description = __('The text to be displayed in the heading of the verification email.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'string',
                            'default' => get_bloginfo('title'),
                            'description' => $description,
                            'show_in_rest' => true
                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/input',
                            'label_for' => $id,
                            'type' => 'text',
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_verification_email_content'),
                        'title' => ($title =  __('Verification email content', 'veifywoo')),
                        'description' => ($description = __('The content of the verification email that will be displayed before the verifiction link.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'string',
                            'default' => __('Please verify your email at the link below.', 'verifywoo'),
                            'description' => $description,
                            'show_in_rest' => true
                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/textarea',
                            'label_for' => $id,
                            'type' => 'textarea',
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ]
                ]
            ],
            'delete_settings' => [
                'id' => PLUGIN_PREFIX . '_delete_settings',
                'title' => __('Delete Settings', 'verifywoo'),
                'callback' => [self::class, 'delete_settings_section_callback'],
                'args' => [
                    'description' => __('These settings are used to verify if and how often you would like to delete unverified users. Users will only be deleted if tokens are expired. Any users registered when this plugin not activated will not be deleted.', 'verifywoo'),
                ],
                'settings' => [
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_automatically_delete_unverified_users'),
                        'title' => ($title =  __('Automatically delete unverified users', 'veifywoo')),
                        'description' => ($description = __('Delete unverified users automatically from database.', 'verifywoo')),
                        'on_change' => [[Cron::class, 'update_option_automatically_delete_unverified_users'], 10, 3],
                        'register_data' => [
                            'type' => 'boolean',
                            'default' => false,
                            'description' => $description,
                            'show_in_rest' => true

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/checkbox',
                            'label_for' => $id,
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_automatically_delete_unverified_users_frequency'),
                        'title' => ($title =  __('Automatically delete frequency', 'veifywoo')),
                        'description' => ($description = __('If delete users automatically is selected this is the frequency to run the script to delete unverified users from database.', 'verifywoo')),
                        'on_change' => [[Cron::class, 'update_option_automatically_delete_unverified_users_frequency'], 10, 3],
                        'register_data' => [
                            'type' => 'string',
                            'default' => '1 week',
                            'description' => $description,
                            'show_in_rest' => true

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/select',
                            'label_for' => $id,
                            'options' => ['weekly', 'daily', 'hourly', 'minute'],
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'id' => ($id = PLUGIN_PREFIX . '_delete_users_exclude_roles'),
                        'title' => ($title =  __('Exclude roles when deleting unverified users.', 'veifywoo')),
                        'description' => ($description = __('User roles to exclude when deleting unverified users.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'array',
                            'default' => self::get_default_deletable_roles(),
                            'description' => $description,
                            'show_in_rest' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'string'
                                    ]
                                ]
                            ]

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/checklist',
                            'options' => self::get_deletable_roles(),
                            'name' => $id,
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                    [
                        'skip_register_setting' => true,
                        'id' => ($id = PLUGIN_PREFIX . '_delete_users_utility'),
                        'title' => ($title =  __('Delete users utility', 'veifywoo')),
                        'description' => ($description = __('This utility runs once and deletes unverified users from the database.', 'verifywoo')),
                        'register_data' => [
                            'type' => 'string',
                            'default' => 'weekly',
                            'description' => $description,
                            'show_in_rest' => false

                        ],
                        'template_data' => [
                            'id' => $id,
                            'template' => 'admin/partials/link-button-secondary',
                            'label_for' => $id,
                            'name' => $id,
                            'link_text' => __('Delete Users Utility', 'verifywoo'),
                            'link_href' => admin_url('admin.php?page=' . PLUGIN_PREFIX . '-users&view=delete-utility'),
                            'description' => $description,
                            'value' => get_option($id),
                        ]
                    ],
                ]
            ]
        ];
    }
}
