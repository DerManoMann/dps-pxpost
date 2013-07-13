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
 * $Id: dps_pxpost.php,v 1.1 2008/02/28 09:16:27 radebatz Exp $
 */
?>
<?php

    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_TITLE', 'DPS PxPost Credit Card');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_DESCRIPTION', 'DPS PxPost Method for Credit Card Processing - mixedmatter Ltd. ver. ${version}');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_TYPE', 'Type:');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_CVV', 'CVV Number (<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_CVV_HELP) . '\')">' . 'More Info' . '</a>)');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_ERROR_MESSAGE', 'There has been an error processing your credit card. Please try again.');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_JS_CC_CVV', '* The CVV number must be at least ' . CC_CVV_MIN_LENGTH . ' characters.\n');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_DECLINED_MESSAGE', 'Your credit card was declined. Please try another card or contact your bank for more info.');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_FAILED_MESSAGE', 'We are currently unable to process your card. Please try again.');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_ERROR', 'Credit Card Error!');

    define('MODULE_PAYMENT_DPS_PXPOST_LOGO', '<br><a href="http://www.paymentexpress.co/" target="_blank"><img src="http://www.paymentexpress.com/images/logos_white/paymentexpress.gif" alt="Payment Processor" width="276" height="42" /></a>');
    define('MODULE_PAYMENT_DPS_PXPOST_LOGO_TEXT', '<br>Real-time 128Bit SSL Secure Credit Card Transaction processing via Direct Payment Solutions (DPS) NZ.<br><br><a href="http://www.paymentexpress.com/PrivacyPolicy.htm" target="_blank">Click here to read DPS\'s Privacy Policy</a>');

    define('MODULE_PAYMENT_DPS_PXPOST_SUPPORTED_CARDS', '');
    //... to show images or text for supported cards - this could be replaced with ...
    //define('MODULE_PAYMENT_DPS_PXPOST_SUPPORTED_CARDS', '<img src="images/icons/visa_small.gif"><img src="images/icons/mastercard_small.gif">');

    //email logging subjects
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_SUCCESS_SUBJECT', 'DPS PxPost Transaction Logging: Approved');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_DECLINED_SUBJECT', 'DPS PxPost Transaction Logging: Not Approved');
    define('MODULE_PAYMENT_DPS_PXPOST_TEXT_FAILED_SUBJECT', 'DPS PxPost Transaction Logging: No Reponse from DPS');

?>
