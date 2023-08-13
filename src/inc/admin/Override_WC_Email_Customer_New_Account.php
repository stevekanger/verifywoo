<?php

namespace VerifyWoo\Inc\Admin;

use const VerifyWoo\PLUGIN_NAME;
use WC_Email_Customer_New_Account;

class Override_WC_Email_Customer_New_Account extends WC_Email_Customer_New_Account {
    public function __construct() {
        parent::__construct();

        $this->title          = __('New account (Disabled By ' . PLUGIN_NAME . ' plugin)', 'woocommerce');
        $this->description    = __('(Disabled By  ' . PLUGIN_NAME . ' plugin) This setting has been disabled and all new account emails are handled by the' . PLUGIN_NAME . ' Plugin. Please check the ' . PLUGIN_NAME . ' section to for customization.', 'woocommerce');
        $this->enabled = 'no';
        unset($this->form_fields['enabled']);
    }
}

return new Override_WC_Email_Customer_New_Account();
