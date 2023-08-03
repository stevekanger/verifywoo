<?php

namespace VerifyWoo\Overrides;

use WC_Email_Customer_New_Account;

use const VerifyWoo\PLUGIN_NAME;

class WC_Email_Customer_New_Account_Override extends WC_Email_Customer_New_Account {
	public function __construct() {
		parent::__construct();

		$this->title          = __('New account (Disabled By ' . PLUGIN_NAME . ' plugin)', 'woocommerce');
		$this->description    = __('(Disabled By  ' . PLUGIN_NAME . ' plugin) This setting has been disabled and all new account emails are handled by the' . PLUGIN_NAME . ' Plugin. Please check the ' . PLUGIN_NAME . ' section to for customization.', 'woocommerce');
		$this->enabled = 'no';
		unset($this->form_fields['enabled']);
	}
}

return new WC_Email_Customer_New_Account_Override();
