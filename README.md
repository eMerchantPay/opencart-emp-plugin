Genesis client for OpenCart
=============================

This is a Payment Module for eMerchantPay that gives you the ability to process payments through eMerchantPay's Payment Gateway - Genesis.

Requirements
------------

* OpenCart 2.x (previous version are supported, but not tested)
* GenesisPHP 1.0.2
* PHP  > 5.3 with libxml
* PHP Extension - cURL (or you’ll manually have to adjust the library to use StreamContext)

Setup
------------

1. Copy the files to the root folder of your OpenCart installation
2. Login inside the Admin Panel
3. Navigate to `Extensions -> Payments` and Enable one of the following: “eMerchantPay Direct” or “eMerchantPay Checkout”
4. Set the login credentials (Username, Password, Token) and adjust the configuration to your needs.
5. You're now ready to process payments through our gateway.
