eMerchantPay Gateway Module for OpenCart
========================================

This is a Payment Module for OpenCart, that gives you the ability to process payments through eMerchantPay's Payment Gateway - Genesis.

Requirements
------------

* OpenCart 2.0.X - 2.2.X (due to architectural changes, this module is __incompatible__ with aOpenCart 1.X)
* [GenesisPHP v1.4](https://github.com/GenesisGateway/genesis_php) - (Integrated in Module)
* PCI-certified server in order to use ```eMerchantPay Direct```

GenesisPHP Requirements
------------

* PHP version 5.3.2 or newer
* PHP Extensions:
    * [BCMath](https://php.net/bcmath)
    * [CURL](https://php.net/curl) (required, only if you use the curl network interface)
    * [Filter](https://php.net/filter)
    * [Hash](https://php.net/hash)
    * [XMLReader](https://php.net/xmlreader)
    * [XMLWriter](https://php.net/xmlwriter)

Installation (via Extension Installer)
------------
1.	Download the __eMerchantPay Payment Gateway__, extract the contents of the folder (excluding ```README.md```) into another empty folder ```upload``` on your local computer.
2.	Create a compressed ```zip``` file of the folder ```upload``` with name ```emerchantpay.ocmod.zip```
3.  Login inside the __OpenCart Admin Panel__
4.  Navigate to ```Extensions -> Extension Installer``` and click on button ```Upload``` and choose the ```zip``` file ```emerchantpay.ocmod.zip``` to install the __eMerchantPay Payment Gateway__.
5.	If you receive an error message __FTP needs to be enabled in the settings__, go to ```System -> Settings -> Your Store -> Edit -> FTP``` and configure your FTP account settings and repeat __Step 4__.
5.  Navigate to ```Extensions -> Payments``` and click install on ```eMerchantPay Direct``` and/or ```eMerchantPay Checkout```
6.  Set the login credentials (```Username```, ```Password```, ```Token```) and adjust the configuration to your needs.

Installation (Manual)
------------

1.  Upload the contents of the folder (excluding ```README.md```) to the ```<root>``` folder of your OpenCart installation
2.  Login inside the __OpenCart Admin Panel__
3.  Navigate to ```Extensions -> Payments``` and click install on ```eMerchantPay Direct``` and/or ```eMerchantPay Checkout```
4.  Set the login credentials (```Username```, ```Password```, ```Token```) and adjust the configuration to your needs.


_Note_: If you have trouble with your credentials or terminal configuration, get in touch with our [support] team

You're now ready to process payments through our gateway.

[support]: mailto:tech-support@emerchantpay.net
