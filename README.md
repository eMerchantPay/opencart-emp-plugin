emerchantpay Gateway Module for OpenCart
========================================

This is a Payment Module for OpenCart, that gives you the ability to process payments through emerchantpay's Payment Gateway - Genesis.

Requirements
------------

* OpenCart 3.0.X - 3.0.3.X (due to architectural changes, this module is __incompatible__ with OpenCart 1.X and 2.0.X)
* [GenesisPHP v1.21.11](https://github.com/GenesisGateway/genesis_php/tree/1.21.11) - (Integrated in Module)

GenesisPHP Requirements
------------

* PHP version 5.5.9 or newer
* PHP Extensions:
    * [BCMath](https://php.net/bcmath)
    * [CURL](https://php.net/curl) (required, only if you use the curl network interface)
    * [Filter](https://php.net/filter)
    * [Hash](https://php.net/hash)
    * [XMLReader](https://php.net/xmlreader)
    * [XMLWriter](https://php.net/xmlwriter)

Installation via Extension Installer
------------
1.	Download the __emerchantpay Payment Gateway__, extract the contents of the folder
2.	Create a compressed ```zip``` file of the folder ```upload``` with name ```emerchantpay.ocmod.zip``` (excluding ```README.md```)
3.	Login inside the __OpenCart Admin Panel__
4.	Navigate to ```Extensions -> Installer``` and click on button ```Upload``` and choose the ```zip``` file ```emerchantpay.ocmod.zip```
5.	Navigate to ```Extensions -> Payments``` and click install on ```emerchantpay Checkout```
6.	Set the login credentials (```Username```, ```Password```) and adjust the configuration to your needs.

Installation (Manual)
------------

1.  Upload the contents of the folder ```upload``` (excluding ```README.md```) to the ```<root>``` folder of your OpenCart installation
2.  Login inside the __OpenCart Admin Panel__
3.  Navigate to ```Extensions -> Payments``` and click install on  ```emerchantpay Checkout```
4.  Set the login credentials (```Username```, ```Password```) and adjust the configuration to your needs.

Recurring Payments
------------
OpenCart has an integrated functionality for processing recurring transactions.
In order to simplify the process of handling recurring payments, the recurring products cannot be ordered along with other products (recurring or non-recurring) and must be ordered separately, one per order.

If you are interested in, you could read more about:

* Total Order Calculation
* Payment Method Configuration
* Recurring Profile Creation
* Setting up Scheduled Tasks & Cron Jobs Configurations
* Cron Jobs IP Restrictions
* Handling Recurring Payments with Payment Module

in [wiki for Recurring Payments](https://github.com/emerchantpay/opencart-emp-plugin/wiki/OpenCart-Recurring-Module-Configurations)

Supported Transactions & Payment Methods
---------------------
* ```emerchantpay Checkout``` Payment Method
  * __Apple Pay__ 
  * __Argencard__
  * __Aura__
  * __Authorize__
  * __Authorize (3D-Secure)__
  * __Baloto__
  * __Bancomer__
  * __Bancontact__
  * __Banco de Occidente__
  * __Banco do Brasil__
  * __BitPay__
  * __Boleto__
  * __Bradesco__
  * __Cabal__
  * __CashU__
  * __Cencosud__
  * __Davivienda__
  * __Efecty__
  * __Elo__
  * __eps__
  * __eZeeWallet__
  * __Fashioncheque__
  * __GiroPay__
  * __Google Pay__
  * __iDeal__
  * __iDebit__
  * __InstaDebit__
  * __InstantTransfer__
  * __InitRecurringSale__
  * __InitRecurringSale (3D-Secure)__
  * __Intersolve__
  * __Itau__
  * __Klarna__
  * __Multibanco__
  * __MyBank__
  * __Naranja__
  * __Nativa__
  * __Neosurf__
  * __Neteller__
  * __Online Banking__
    * __Interac Combined Pay-in (CPI)__ 
    * __Bancontact__ 
  * __OXXO__
  * __P24__
  * __Pago Facil__
  * __PayPal__
  * __PaySafeCard__
  * __PayU__
  * __Pix__
  * __POLi__
  * __Post Finance__
  * __PPRO__
    * __eps__
    * __GiroPay__
    * __Ideal__
    * __Przelewy24__
    * __SafetyPay__
    * __TrustPay__
    * __BCMC__
    * __MyBank__
  * __PSE__
  * __RapiPago__
  * __Redpagos__
  * __SafetyPay__
  * __Sale__
  * __Sale (3D-Secure)__
  * __Santander__
  * __Sepa Direct Debit__
  * __SOFORT__
  * __Tarjeta Shopping__
  * __TCS__
  * __Trustly__
  * __TrustPay__
  * __UPI__
  * __WebMoney__
  * __WebPay__
  * __WeChat__

_Note_: If you have trouble with your credentials or terminal configuration, get in touch with our [support] team

You're now ready to process payments through our gateway.

[support]: mailto:tech-support@emerchantpay.net
