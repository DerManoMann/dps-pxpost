===================================================
 + THIS REPOSITORY IS NO LONGER MAINTAINED        =
===================================================


DPS PxPost payment module v${version} for zen-cart
===================================================
Copyright (c) 2006-2013 mixedmatter Ltd
Copyright (c) 2004,2005 Attitude Group Ltd

https://github.com/DerManoMann/dps-pxpost

Adapted from dps_pxpost_0112
http://www.oscommerce.co.nz/contributions/dps_pxpost_0112.zip

This DPS PxPost payment module is released under the terms of the General Public
License. Please see the license.txt file for more details.


1. Overview
-----------
This is a zen-cart 1.3.x/1.5.x payment module to use with the DPS PxPost payment
solution.
See: http://www.paymentexpress.com/technical_resources/ecommerce_hosted/pxpost.html
for more details.


1.1 Requirements
----------------
A PxPost account with DPS - http://www.paymentexpress.com
An SSL certificate for your site
CURL support for PHP - your host or a phpinfo page should be able to 
confirm this.


2. Disclaimer
-------------
This code has been only tested with zen-cart 1.3.x. BACKUP YOUR FILES 
BEFORE INSTALLING. Use this code at your own risk. No warranty offered. 


3. Installation Instructions
----------------------------
3.1. In the zip file are two files called dps_pxpost.php. 
The payment module file in the zen-cart/includes/modules/payment/ directory 
should be uploaded into the includes/modules/payment/ directory on your 
site.

3.2 The language file in the zen-cart/includes/languages/english/modules/payment/ 
directory should be uploaded to the includes/languages/english/modules/payment/ 
directory on your site.

3.3 The dps logo image in the catalog/images/ folder should be uploaded into the 
zen-cart/images/ directory on your site.

3.4 If using PHP with the suhosin extension it is required to have the setting 
'suhosin.get.max_value_length' set to also at least 2048 in order to allow PHP
to process long redirect urls.


4. Setup and Configuration
--------------------------
Go to the admin area in your site and navigate to admin->modules->payment.
You can then configure the various settings.


4.1 Enable DPS Module
---------------------
This should be set to true if you want to use the module. If you want the module
disabled without losing your settings (e.g. DPS Username) set this to false
rather than removing the module.


4.2 DPS Username
----------------
This is the PxPost Username provided by DPS.


4.3 DPS Password
----------------
This is the PxPost Password provided by DPS.


4.4 Transaction Method
----------------------
Choose between Purchase and Auth. Purchase transactions trigger immediate payment 
of funds. Auth transaction authorise the card for the required amount which can then
be paid later.


4.5 Email Logging 
-----------------
If you want to log all transactions with DPS (including declines and errors) enter
an email address here.


4.6 Payment Zone 
----------------
Set this to enable the module for a particular zone only.


4.7 Set Order Status
--------------------
Sets the status of orders made with this payment module to this value.


4.8 Sort Order of Display
-------------------------
If you are using other payment modules you can use this field to change the order in 
which they are displayed.
