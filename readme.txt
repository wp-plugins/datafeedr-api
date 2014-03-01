=== Datafeedr API ===

Contributors: datafeedr.com
Tags: woocommerce, datafeedr, affiliate products
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: 0.9.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect to the Datafeedr API.

== Description ==

**NOTE:** The *Datafeedr API* plugin requires that you have Datafeedr API keys. API keys can be purchased here: [https://v4.datafeedr.com/](https://v4.datafeedr.com/)

The Datafeedr API provides access to our database of affiliate products. We have aggregated over 250,000,000 products from over 10,000 merchants and 32 affiliate networks. We have indexed and normalized the product data making it easy for you to search for and find products you want to promote on your website.

The *Datafeedr API* plugin currently integrates with the following plugins:

* [Datafeedr WooCommerce Importer](http://wordpress.org/plugins/datafeedr-woocommerce-importer/)
* [Datafeedr Product Sets](http://wordpress.org/plugins/datafeedr-product-sets/)

The *Datafeedr API* plugin provides the interface to connect to the Datafeedr API and perform the following tasks:

* Configure your API connection settings.
* Select affiliate networks you are a member of.
* Select merchants who have approved you.
* Add your affiliate network affiliate IDs.
* Import/export your selection of affiliate networks and/or merchants.
* View your API account usage.

The *Datafeedr API* plugin was built to be extended. The *Datafeedr API* plugin contains its own functions that third party developers can use to connect to the Datafeedr API, make search requests or display an 'advanced search' form. We encourage other developers to build on top of the *Datafeedr API* plugin.

Additionally, we have written plugins that integrate the *Datafeedr API* plugin with WooCommerce. More extensions are on the way...


== Installation ==

This section describes how to install and configure the plugin:

1. Upload the `datafeedr-api` folder to the `/wp-content/plugins/` directory.
1. Activate the *Datafeedr API* plugin through the 'Plugins' menu in WordPress.
1. Enter your Datafeedr API keys here: WordPress Admin Area > Datafeedr API > Configuration
1. Select the affiliate networks you are a member of here: WordPress Admin Area > Datafeedr API > Networks
1. Select the merchants who have approved you here: WordPress Admin Area > Datafeedr API > Merchants

== Frequently Asked Questions ==

= Where can I get help?  =

Our support area can be found here: [https://v4.datafeedr.com/support](https://v4.datafeedr.com/support). This support area is open to everyone.

== Screenshots ==

1. API key configuration
2. Network selection
3. Merchant selection
4. Account usage overview

== Changelog ==

= 0.9.9 =
* Updated "Contributors" and "Author" fields to match WP.org username.
* Added support for AUD, DKK, SEK, NOK, CHF, NZD & TRY currency codes.

= 0.9.8 =
* Added support for Zanox.
* Fixed undefined indexes.

= 0.9.7 =
* Fixed issed related to using Sort by Relevance. #8439
* Fixed "support" links in help tab.
* Updated plugin information.

= 0.9.6 =
* Added if(!class_exists('...')) checks to the Datafeedr Api Client Library.

= 0.9.5 =
* Fixed undefined indexes.
* Added "static" to static methods to meet Strict Standards.

= 0.9.4 =
* Removed letters and characters from 'tagged' version.
* Updated "Tested up to" to 3.8.1

= 0.9-beta-3 =
* Initial release.

== Upgrade Notice ==

*None*

