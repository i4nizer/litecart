<?php

  class pm_manual_btc_transfer {
    public $id = __CLASS__;
    public $name = 'Manual Bitcoin Transfer';
    public $description = '';
    public $author = 'Fork by Fame, forked from LiteCart Dev Team';
    public $version = '1.1';
    public $website = 'https://www.litecart.net';
    public $priority = 0;

    public function options($items, $subtotal, $tax, $currency_code, $customer) {

      if (empty($this->settings['status'])) return;

      if (!empty($this->settings['geo_zone_id'])) {
        if (!reference::country($customer['country_code'])->in_geo_zone($this->settings['geo_zone_id'], $customer)) return;
      }

      $method = [
        'title' => language::translate(__CLASS__.':title_module', 'Manual Bitcoin Transfer'),
        'options' => [
          [
            'id' => 'manual_btc_transfer',
            'icon' => $this->settings['icon'],
            'name' => language::translate(__CLASS__.':title_option_manual_btc_account', 'Bitcoin Wallet'),
            'description' => strtr(language::translate(__CLASS__.':text_instructions', 'Send payment to the following Bitcoin wallet: %manual_btc_wallet'), $this->_get_manual_btc_account($currency_code)),
            'fields' => '',
            'cost' => $this->settings['fee'],
            'tax_class_id' => $this->settings['tax_class_id'],
            'confirm' => language::translate(__CLASS__.':title_confirm_order', 'Confirm Order'),
          ],
        ]
      ];

      return $method;
    }

    public function verify($order) {

      $order->data['comments'][] = [
        'text' => strtr(language::translate(__CLASS__.':text_instructions', 'Send payment to the following Bitcoin wallet: %manual_btc_wallet'), $this->_get_manual_btc_account($order->data['currency_code'])),
        'notify' => true,
      ];

      return [
        'order_status_id' => $this->settings['order_status_id'],
        'payment_transaction_id' => '',
        'errors' => '',
      ];
    }

    private function _get_manual_btc_account($currency_code) {

      $rows = functions::csv_decode($this->settings['manual_btc_accounts'], ',');

      foreach ($rows as $row) {
        if ($currency_code == $row['currency_code']) {
          return [
            '%currency_code' => $row['currency_code'],
            '%manual_btc_wallet' => $row['manual_btc_wallet'],
          ];
        }
      }

      if ($currency_code != 'XXX') {
        if ($account = $this->_get_manual_btc_account('XXX')) {
          return $account;
        } else {
          trigger_error("Missing Bitcoin Wallet details for $currency_code or last destination XXX", E_USER_WARNING);
          return ['%currency_code' => '', '%manual_btc_wallet' => ''];
        }
      }
    }

    function settings() {
      return [
        [
          'key' => 'status',
          'default_value' => '1',
          'title' => language::translate(__CLASS__.':title_status', 'Status'),
          'description' => language::translate(__CLASS__.':description_status', 'Enables or disables the module.'),
          'function' => 'toggle("e/d")',
        ],
        [
          'key' => 'icon',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_icon', 'Icon'),
          'description' => language::translate(__CLASS__.':description_icon', 'Path to an image to be displayed.'),
          'function' => 'text()',
        ],
        [
          'key' => 'fee',
          'default_value' => '0',
          'title' => language::translate(__CLASS__.':title_payment_fee', 'Payment Fee'),
          'description' => language::translate(__CLASS__.':description_payment_fee', 'Adds a payment fee to the order.'),
          'function' => 'number()',
        ],
        [
          'key' => 'tax_class_id',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_tax_class', 'Tax Class'),
          'description' => language::translate(__CLASS__.':description_tax_class', 'The tax class for the shipping cost.'),
          'function' => 'tax_class()',
        ],
        [
          'key' => 'manual_btc_accounts',
          'default_value' => 'currency_code,manual_btc_wallet' . PHP_EOL
                           . 'XXX,BTC-WALLET-ADDRESS',
          'title' => language::translate(__CLASS__.':title_manual_btc_accounts', 'Manual Bitcoin Wallets'),
          'description' => language::translate(__CLASS__.':description_manual_btc_accounts', 'A comma separated list of Bitcoin Wallets to where the customer should transfer the payment.'),
          'function' => 'mediumtext()',
        ],
        [
          'key' => 'order_status_id',
          'default_value' => '0',
          'title' => language::translate('title_order_status', 'Order Status'),
          'description' => language::translate('modules:description_order_status', 'Give orders made with this payment method the following order status.'),
          'function' => 'order_status()',
        ],
        [
          'key' => 'geo_zone_id',
          'default_value' => '',
          'title' => language::translate(__CLASS__.':title_geo_zone', 'Geo Zone'),
          'description' => language::translate(__CLASS__.':description_geo_zone', 'Limit this module to the selected geo zone. Otherwise leave blank.'),
          'function' => 'geo_zone()',
        ],
        [
          'key' => 'priority',
          'default_value' => '0',
          'title' => language::translate('title_priority', 'Priority'),
          'description' => language::translate(__CLASS__.':description_priority', 'Displays this module by the given priority order value.'),
          'function' => 'number()',
        ],
      ];
    }

    public function install() {}

    public function uninstall() {}
  }
