# Migrating to Omise

This document aims to provide advice for moving from the Paysbuy payment plugin to Omise's equivalent. The advice here is meant only as a simple guide - please [contact Omise](mailto:support@omise.co) if you require further assistance.

## Step 1: Sign up

If you have not already done so, you will need to sign up for an Omise account [here](https://dashboard.omise.co/signup).

## Step 2: Install plugin

Grab the plugin from GitHub [here](https://github.com/omise/omise-magento), and follow the provided installation instructions. Once installed and set up, test the plugin to make sure it works with your Omise account.

## Step 3 (optional): Use Omise plugin alongside the Paysbuy plugin

If you are unsure about removing the Paysbuy plugin immediately and making the switch to Omise, you can run the two plugins together until you are happy that everything is running smoothly with your Omise payments. At this point, or at the time when the Paysbuy services are shut down, you can simply deactivate and/or uninstall the Paysbuy plugin.

## Step 4: Disable the Paysbuy plugin

Run the `php bin/magento module:disable Paysbuy_PsbGateway --clear-static-content` command.

## Step 5 (optional): Remove Paysbuy plugin

Run the `php bin/magento module:uninstall Paysbuy_PsbGateway` command

## Migration Complete

Welcome to Omise!