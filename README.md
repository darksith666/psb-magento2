# PAYSBUY extension for Magento eCommerce v2

## * _IMPORTANT Announcement_ *

PAYSBUY has been acquired by [Omise](http://omise.co). As a result of this, PAYSBUY's online payment services will eventually be shutting down. Merchants should be contacted regarding migration to Omise's online payment services.

An Omise plugin for Magento v2 can be found [here](https://github.com/omise/omise-magento).

Please direct further questions to the Omise [forum](http://forum.omise.com) or [support@omise.co](mailto:support@omise.co)

[https://www.paysbuy.com/news-226.aspx](https://www.paysbuy.com/news-226.aspx)



## Magento Version Compatibility
- Magento (CE) 2.0.x - 2.1.x
- PHP 5.5.22 or above

## Dependencies
- None

## Installation
By far the easiest way to install the extension is via composer. Simply add the following line to the main `composer.json` file in the Magento folder:

<pre>
...
"require": {
  ...<b>,</b>
  <b>"paysbuy/module-magento2-gateway": "@stable"</b>
},
...
</pre>
Once this is done, simply run `composer update`, and the module should be automatically installed. If you are installing the module to an existing Magento installation, you will probably need to run the following commands after running composer:

<pre>
php bin/magento module:enable Paysbuy_PsbGateway
php bin/magento setup:upgrade
php bin/magento setup:di:compile
</pre>

Alternatively, you may use the "File Transfer" method detailed [here](https://www.quora.com/How-do-I-install-extensions-in-magento2).

When you have installed the module, go to the store's configuration page and locate the payment methods configuration - here you can set up the plugin ready to start accepting payments via the PAYSBUY gateway.

