# PAYSBUY extension for Magento eCommerce v2

## Magento Version Compatibility
- Magento (CE) 2.0.x

## Dependencies
- None

## Installation
By far the easiest way to install the extension is via composer. Simply add the following line to the main `composer.json` file in the Magento folder:

<pre>
...
"require": {
  ...
  <b>"paysbuy/module-magento2-gateway": "0.2.0"</b>
},
...
</pre>
Once this is done, simply run `composer update`, and the module should be automatically installed. Alternatively, you may use the "File Transfer" method detailed [here](https://www.quora.com/How-do-I-install-extensions-in-magento2).

When you have installed the module, go to the store's configuration page and locate the payment methods configuration - here you can set up the plugin ready to start accepting payments via the PAYSBUY gateway.

