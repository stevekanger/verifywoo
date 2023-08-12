<?php

namespace VerifyWoo\Controllers\Admin;

use const VerifyWoo\PLUGIN_PREFIX;

use VerifyWoo\Core\DB;
use VerifyWoo\Core\Template;
use VerifyWoo\Core\Utils;

defined('ABSPATH') || exit;

class AdminSettings {
    public static function admin_menu() {
        add_menu_page(__('Verify Woo', 'verifywoo'), __('Verify Woo', 'verifywoo'), 'activate_plugins', PLUGIN_PREFIX, [self::class, 'admin_settings_page_callback'], 'dashicons-shield', 55.5);
        add_submenu_page(PLUGIN_PREFIX, __('Verify Woo Users', 'verifywoo'), __('Users', 'verifywoo'), 'activate_plugins', PLUGIN_PREFIX . '-users', [self::class, 'admin_users_page_callback']);
    }

    public static function add_settings_sections() {
        $settings_sections = self::get_settings();

        foreach ($settings_sections as $section) {
            add_settings_section($section['id'], $section['title'], [self::class, 'settings_section_callback'], 'verifywoo', ['description' => $section['description']]);
            foreach ($section['settings'] as $setting) {
                add_settings_field($setting['id'], $setting['title'], [self::class, 'add_settings_field_callback'], PLUGIN_PREFIX, $section['id'], $setting['template_data']);
            }
        }
    }

    public static function register_settings() {
        $settings_sections = self::get_settings();

        foreach ($settings_sections as $section) {
            foreach ($section['settings'] as $setting) {
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

    public static function add_settings_field_callback($data) {
        Template::include($data['template'], $data);
    }

    // public static function manage_users_columns($columns) {
    //     $columns['email_verified'] = __('Email Verified', 'verifywoo');
    //     return $columns;
    // }

    // public static function manage_users_custom_column($val, $column_name, $user_id) {
    //     if ($column_name !== 'email_verified') return $val;

    //     $email = get_user_by('id', $user_id)->user_email;
    //     $verified_query = DB::get_row('SELECT verified from ' . DB::prefix(PLUGIN_PREFIX) . ' where email = %s', $email);
    //     if (!$verified_query) return 0;
    //     return $verified_query->verified;
    // }

    // public static function show_user_profile($profile) {
    //     Utils::debug($profile);
    // }

    // public static function edit_user_profile($profile) {
    //     Utils::debug($profile);
    // }

    private static function get_settings() {
        return  [
            'registration_settings' => [
                'id' => PLUGIN_PREFIX . '_registration_settings',
                'title' => __('Registration Settings', 'verifywoo'),
                'description' => __('These settings are tied to the registration process. They are called when a new user is registered.', 'verifywoo'),
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
                'description' => __('General email settings. These settings are used to form the email content. The default woocommerce header and footer are used for email templates.', 'verifywoo'),
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
                            'default' => __('Please verify your email address by clicking the link below.', 'verifywoo'),
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
            ]
        ];
    }
}
