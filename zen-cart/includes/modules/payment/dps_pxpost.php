<?php
/*
 * DPS PxPost PAYMENT MODULE FOR zen-cart 1.3.x
 * --------------------------------------------
 * Copyright (c) 2006 mixedmatter Ltd
 * Copyright (c) 2004,2005 Attitude Group Ltd
 *
 * Adapted from dps_pxpost_0112
 * http://www.oscommerce.co.nz/contributions/dps_pxpost_0112.zip
 *
 * DPS PxPost Payment Module for zen-cart 1.3.x is released under
 * the terms of the General Public License. Please see the license.txt
 * for more details.
 *
 * $Id: dps_pxpost.php,v 1.3 2008/02/29 09:13:03 radebatz Exp $
 */
?>
<?php

class dps_pxpost {
    var $code, $title, $description, $enabled;

    // class constructor
    function dps_pxpost() {
    global $order;

        $this->code = 'dps_pxpost';
        $this->title = MODULE_PAYMENT_DPS_PXPOST_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_DPS_PXPOST_TEXT_DESCRIPTION;
        $this->enabled = ((MODULE_PAYMENT_DPS_PXPOST_STATUS == 'True') ? true : false);
        $this->sort_order = MODULE_PAYMENT_DPS_PXPOST_SORT_ORDER;

        if ((int)MODULE_PAYMENT_DPS_PXPOST_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_DPS_PXPOST_ORDER_STATUS_ID;
        }

        if (is_object($order)) $this->update_status();

        $this->dps_url = 'https://sec.paymentexpress.com/pxpost.aspx';
    }


    // update status
    function update_status() {
    global $order, $db;

        if ($this->enabled && ((int)MODULE_PAYMENT_DPS_PXPOST_ZONE > 0)) {
            $check_flag = false;
            $sql = "select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . "
                    where geo_zone_id = '" . MODULE_PAYMENT_DPS_PXPAY_ZONE . "'
                      and zone_country_id = :countryId
                    order by zone_id";
            $sql = $db->bindVars($sql, ':countryId', $order->billing['country']['id'], 'integer');

            $results = $db->Execute($sql);
            while (!$results->EOF) {
                if ($results->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($results->fields['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
                $results->MoveNext();
            }

            if (!$check_flag) {
                $this->enabled = false;
            }
        }
    }


    // javascript validation
    function javascript_validation() {
        $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var cc_owner = document.checkout_payment.dps_cc_owner.value;' . "\n" .
            '    var cc_number = document.checkout_payment.dps_cc_number.value;' . "\n";

        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            $js .= '    var dps_cvv = document.checkout_payment.dps_cvv.value;' . "\n";
        }

        $js .= '    if (cc_owner == "" || cc_owner.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
               '      error_message = error_message + "' . MODULE_PAYMENT_DPS_PXPOST_TEXT_JS_CC_OWNER . '";' . "\n" .
               '      error = 1;' . "\n" .
               '    }' . "\n" .
               '    if (cc_number == "" || cc_number.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
               '      error_message = error_message + "' . MODULE_PAYMENT_DPS_PXPOST_TEXT_JS_CC_NUMBER . '";' . "\n" .
               '      error = 1;' . "\n" .
               '    }' . "\n";

        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            $js .= '    if (dps_cvv == "" || dps_cvv.length < ' . CC_CVV_MIN_LENGTH . ') {' . "\n" .
                   '      error_message = error_message + "' . MODULE_PAYMENT_DPS_PXPOST_TEXT_JS_CC_CVV . '";' . "\n" .
                   '      error = 1;' . "\n" .
                   '    }' . "\n";
        }

        $js .= '  }' . "\n";

        return $js;
    }


    // selection
    function selection() {
    global $order;

        for ($ii=1; $ii<13; $ii++) {
            $expires_month[] = array('id' => sprintf('%02d', $ii), 'text' => strftime('%B',mktime(0,0,0,$ii,1,2000)));
        }

        $today = getdate();
        for ($ii=$today['year']; $ii < $today['year']+10; $ii++) {
            $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$ii)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$ii)));
        }

        $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => '',
                                                 'field' => MODULE_PAYMENT_DPS_PXPOST_SUPPORTED_CARDS),
                                           array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_OWNER,
                                                 'field' => zen_draw_input_field('dps_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_NUMBER,
                                                 'field' => zen_draw_input_field('dps_cc_number')),
                                           array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_EXPIRES,
                                                 'field' => zen_draw_pull_down_menu('dps_cc_expires_month', $expires_month) . '&nbsp;' . zen_draw_pull_down_menu('dps_cc_expires_year', $expires_year)),
                                           array('title' => MODULE_PAYMENT_DPS_PXPOST_LOGO,
                                                 'field' => MODULE_PAYMENT_DPS_PXPOST_LOGO_TEXT),
                                                 ));
        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            array_pop($selection['fields']);
            $selection['fields'][] = array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_CVV,
                                           'field' => zen_draw_input_field('dps_cvv', '', 'id="'.$this->code.'-dps-cvv"'),
                                           'tag' => $this->code.'-dps-cvv');
            $selection['fields'][] = array('title' => MODULE_PAYMENT_DPS_PXPOST_LOGO,
                                           'field' => MODULE_PAYMENT_DPS_PXPOST_LOGO_TEXT);
        }

        return $selection;
    }


    // pre confirmation check
    function pre_confirmation_check() {
    include(DIR_WS_CLASSES . 'cc_validation.php');

        $cc_validation = new cc_validation();
        $result = $cc_validation->validate($_POST['dps_cc_number'], $_POST['dps_cc_expires_month'], $_POST['dps_cc_expires_year']);
        $error = '';
        switch ($result) {
            case -1:
                $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
                break;
            case -2:
            case -3:
            case -4:
                $error = TEXT_CCVAL_ERROR_INVALID_DATE;
                break;
            case false:
                $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
                break;
        }

        if ( ($result == false) || ($result < 1) ) {
            $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&dps_cc_owner=' . urlencode($_POST['dps_cc_owner']) . '&dps_cc_expires_month=' . $_POST['dps_cc_expires_month'] . '&dps_cc_expires_year=' . $_POST['dps_cc_expires_year'];

            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
        }

        $this->cc_card_type = $cc_validation->cc_type;
        $this->cc_card_number = $cc_validation->cc_number;
        $this->cc_expiry_month = $cc_validation->cc_expiry_month;
        $this->cc_expiry_year = $cc_validation->cc_expiry_year;
    }


    // confirmation
    function confirmation() {
        $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                              'fields' => array(array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_OWNER,
                                                      'field' => $_POST['dps_cc_owner']),
                                                array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_NUMBER,
                                                      'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                                array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_EXPIRES,
                                                      'field' => strftime('%B, %Y', mktime(0,0,0,$_POST['dps_cc_expires_month'], 1, '20' . $_POST['dps_cc_expires_year'])))));

        return $confirmation;
    }


    // process button
    function process_button() {
    global $order, $customer_id;

        $process_button_string = zen_draw_hidden_field('ft', $order->info['total']) .
                               zen_draw_hidden_field('cc_owner', $_POST['dps_cc_owner']) .
                               zen_draw_hidden_field('cc_expires', $this->cc_expiry_month . substr($this->cc_expiry_year, -2)) .
                               zen_draw_hidden_field('cc_type', $this->cc_card_type) .
                               zen_draw_hidden_field('cc_number', $this->cc_card_number);

        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            $process_button_string .= zen_draw_hidden_field('cc_cvv', $_POST['dps_cvv']);
        }

        return $process_button_string;
    }


    // before process
    function before_process() {
    global $customer_id, $order, $ft_validation, $messageStack;

        // amount
        $amount = $order->info['total'];

        // construct xml to send to dps
        $query = "<Txn>\n".
                 "<PostUsername>".MODULE_PAYMENT_DPS_PXPOST_USERNAME."</PostUsername>\n".
                 "<PostPassword>".MODULE_PAYMENT_DPS_PXPOST_PASSWORD."</PostPassword>\n".
                 "<CardHolderName>".$_POST['cc_owner']."</CardHolderName>\n".
                 "<CardNumber>".$_POST['cc_number']."</CardNumber>\n";

        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            $query .= "<Cvc2>".$_POST['cc_cvv']."</Cvc2>\n";
        }

        $query .= "<Amount>".number_format($amount, 2,'.','')."</Amount>\n".
                  "<DateExpiry>".$_POST['cc_expires']."</DateExpiry>\n".
                  "<TxnType>".MODULE_PAYMENT_DPS_PXPOST_METHOD."</TxnType>\n".
                  "<MerchantReference>".$customer_id."-".date("YmdHis")."</MerchantReference>\n".
                  "</Txn>";

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->dps_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        if (defined('CURLOPT_POSTFIELDSIZE')) {
            curl_setopt($curl, CURLOPT_POSTFIELDSIZE, 0);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
//        curl_setopt($curl, CURLOPT_SSLVERSION, 3);

        if (strtoupper(substr(@php_uname('s'), 0, 3)) === 'WIN') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $dps_success = false;
        $dps_email_log = '';

        if ($result = curl_exec($curl)) {
            $dps_authorized = $this->_dps_attribute_value('Authorized',$result);
            if ($dps_authorized == 1) {
                //passed: return true
                $dps_success = true;
                $dps_email_subject = MODULE_PAYMENT_DPS_PXPOST_TEXT_SUCCESS_SUBJECT;
            } else {
                //failed: redirect to payment page with nice error
                $dps_email_subject = MODULE_PAYMENT_DPS_PXPOST_TEXT_DECLINED_SUBJECT;
                $dps_error_redirect = MODULE_PAYMENT_DPS_PXPOST_TEXT_DECLINED_MESSAGE;
            }
        } else {
            //no response from dps/curl failed: redirect to payment page with nice error
            $dps_email_subject = MODULE_PAYMENT_DPS_PXPOST_TEXT_FAILED_SUBJECT;
            $dps_error_redirect = MODULE_PAYMENT_DPS_PXPOST_TEXT_FAILED_MESSAGE;
        }

        curl_close($curl);

        // cardnumber comes back already obfuscated
        $dps_email_log = str_replace(array('<', '>'), array('[', ']'), $result);

        // if DPS email logging email valid - send report
        $block = array();
        $block['EMAIL_MESSAGE_HTML'] = $dps_email_log;
        if (zen_validate_email(MODULE_PAYMENT_DPS_PXPOST_EMAIL)) {
            zen_mail('', MODULE_PAYMENT_DPS_PXPOST_EMAIL, $dps_email_subject, $dps_email_log, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $block);
        }

        if (!$dps_success) {
            $messageStack->add_session('checkout_payment', $dps_error_redirect, 'error');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }

        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            $order->info['cc_cvv'] = $_POST['cc_cvv'];
        }
        $order->info['cc_number'] =  str_repeat('X', (strlen($_POST['cc_number']) - 4)) . substr($_POST['cc_number'], -4);
        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CARDINFO == 'True')  {
            $order->info['cc_expires'] = $_POST['cc_expires'];
            $order->info['cc_type'] = $_POST['cc_type'];
            $order->info['cc_owner'] = $_POST['cc_owner'];
        }
    }


    // after process
    function after_process() {
        return false;
    }


    // after order create
    function after_order_create($zf_order_id) {
    global $db, $order;

        if (MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV == 'True')  {
            $db->execute("update "  . TABLE_ORDERS . " set cc_cvv ='" . $order->info['cc_cvv'] . "'
                          where orders_id = '" . $zf_order_id ."'");
        }
    }


    // get error
    function get_error() {
        $error = array('title' => MODULE_PAYMENT_DPS_PXPOST_TEXT_ERROR,
                       'error' => stripslashes(urldecode($_GET['error'])));
        return $error;
    }


    // check
    function check() {
    global $db;

        if (!isset($this->_check)) {
            $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . "
                                         where configuration_key = 'MODULE_PAYMENT_DPS_PXPOST_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    // install
    function install() {
    global $db;

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable DPS PxPost Module', 'MODULE_PAYMENT_DPS_PXPOST_STATUS', 'True', 'Do you want to accept DPS PxPost payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('DPS PxPost Username', 'MODULE_PAYMENT_DPS_PXPOST_USERNAME', '', 'The username used for the DPS PxPost service', '6', '10', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('DPS PxPost Password', 'MODULE_PAYMENT_DPS_PXPOST_PASSWORD', '', 'The password used for the DPS PxPost service', '6', '15', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Method', 'MODULE_PAYMENT_DPS_PXPOST_METHOD', 'Purchase', 'Transaction method used for processing orders', '6', '20', 'zen_cfg_select_option(array(\'Purchase\', \'Auth\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Save the Card details (name, type)', 'MODULE_PAYMENT_DPS_PXPOST_COLLECT_CARDINFO', 'True', 'Do you want to store the card details (name, type). Note: If you do the details will be permanently stored in the database.', '6', '21', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Collect & store the CVV number', 'MODULE_PAYMENT_DPS_PXPOST_COLLECT_CVV', 'True', 'Do you want to collect the CVV number. Note: If you do the CVV number will be stored in the database in an encoded format.', '6', '22', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Email Logging', 'MODULE_PAYMENT_DPS_PXPOST_EMAIL', '', 'Enter an email address here if you would like to log all transactions with DPS to email.', '6', '25', '', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_DPS_PXPOST_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '30', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_DPS_PXPOST_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '35', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_DPS_PXPOST_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '40', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    }


    // remove
    function remove() {
    global $db;

        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }


    // keys
    function keys() {
    global $db;

        $results = $db->Execute("select configuration_key from " . TABLE_CONFIGURATION . "
                                 where configuration_key like 'MODULE_PAYMENT_DPS_PXPOST_%' " . "
                                 order by sort_order");
        $keys = array();
        while (!$results->EOF) {
            array_push($keys, $results->fields['configuration_key']);
            $results->MoveNext();
        }
        return $keys;
    }


    // internal function to parse response
    function _dps_attribute_value($attribute,$string) {
    	list(,$exploded_value) = explode('<'.$attribute.'>',$string);
    	return substr($exploded_value,0,strpos($exploded_value,'</'.$attribute.'>'));
    }
}

?>
