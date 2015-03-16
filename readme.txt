=== Datafeedr API ===

Contributors: datafeedr.com
Tags: woocommerce, datafeedr, affiliate products, dfrapi, adrecord, adtraction, affiliate window, affiliate4you, affilinet, amazon local, avangate, avantlink, belboon, betty mills, cj, clickbank, clixgalore, commission factory, commission junction, daisycon, dgm, flipkart, impact radius, linkconnector, linkshare, onenetworkdirect, paid on results, partner-ads, pepperjam, mycommerce, revresponse, shareasale, superclix, tradedoubler, tradetracker, webgains, zanox
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.8
Tested up to: 4.1.1
Stable tag: 1.0.23

Connect to the Datafeedr API.

== Description ==

**NOTE:** The *Datafeedr API* plugin requires that you have Datafeedr API keys. API keys can be accessed here: [https://v4.datafeedr.com/pricing](https://v4.datafeedr.com/pricing?p=1&utm_campaign=dfrapiplugin&utm_medium=referral&utm_source=wporg)

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

**Requirements**

* PHP's `CURL` support must be enabled.

== Installation ==

This section describes how to install and configure the plugin:

1. Upload the `datafeedr-api` folder to the `/wp-content/plugins/` directory.
1. Activate the *Datafeedr API* plugin through the 'Plugins' menu in WordPress.
1. Enter your Datafeedr API keys here: WordPress Admin Area > Datafeedr API > Configuration
1. Select the affiliate networks you are a member of here: WordPress Admin Area > Datafeedr API > Networks
1. Select the merchants who have approved you here: WordPress Admin Area > Datafeedr API > Merchants

== Frequently Asked Questions ==

= Where can I get help?  =

Our support area can be found here: [https://v4.datafeedr.com/support](https://v4.datafeedr.com/support?p=1&utm_campaign=dfrapiplugin&utm_medium=referral&utm_source=wporg). This support area is open to everyone.

== Screenshots ==

1. API key configuration
2. Network selection
3. Merchant selection
4. Account usage overview

== Changelog ==

= 1.0.23 =
* Fixed wrong error message when the merchant filter is added to the search form but no merchants are selected.

= 1.0.22 =
* Added support for Brazilian Real, Indian Rupee & Polish Złoty currency symbols.

= 1.0.21 =
* Updated Datafeedr API file.

= 1.0.20 =
* Just updating readme.

= 1.0.19 =
* Added FlipKart support.
* Added Amazon Local US support.

= 1.0.18 =
* Added MyCommerce to css file.
* Added link to docs for MyCommerce.

= 1.0.17 =
* Removed M4N from tags in readme.txt file.
* Changed RegNow to MyCommerce in readme file.
* Added MyCommerce icons.
* Added plugin icon for WordPress 4.0+.

= 1.0.16 =
* Fixed undefined 'tid' index.
* Changed the 'delete cached api data' tool from checkbox to ajax button.
* Display notice if a user has selected a Zanox merchant which has not approved their account. This prevents many extra API requests from being generated. (#9474)
* Removed p tags for nags.
* Added ajax.php file to handle... um... AJAX stuff.
* Add "___MISSING___" to Zanox URLs if affiliate ID is missing.

= 1.0.15 =
* Removed Commission Monster from list of supported affiliate networks.

= 1.0.14 =
* Fixed bug introduced by removing dfrapi_filter_affiliate_id filter in v1.0.12 related to Zanox.

= 1.0.13 =
* Removed BOL from list of supported affiliate networks.
* Changed WP header image.

= 1.0.12 =
* Removed dfrapi_filter_affiliate_id filter.
* Added ability to add tracking ID to outgoing affiliate links.

= 1.0.11 =
* Changed add_option to update_option in upgrade.php file.
* Updated the datafeedr.php API library to deal with 32-bit systems and product IDs.

= 1.0.10 =
* Added upgrade.php file to track version changes.
* Added dfrapi_get_total_products_in_db() function.

= 1.0.9 =
* Fixed issue where searches with duplicates excluded returned a higher 'found' count than really was there. (#8672)

= 1.0.8 =
* Added css and mapper link for docs for Double.net.

= 1.0.7 =
* Added logos for Double.net.

= 1.0.6 =
* Forgot to update version numbers in plugin file.

= 1.0.5 =
* Tweaked search form css.
* Added message for database rotation time between 8:00am and 8:20am GMT.
* Added search form help text.
* Changed some labels in the search form.
* Added support for Affiliator affiliate network.
* Updated Datafeedr API library.

= 1.0.4 =
* Tweaked search form css.
* Changes to a lot of help text on all pages.

= 1.0.3 =
* Changed <title> of Tools page.

= 1.0.2 =
* Edited nag boxes when API requests are 90% of max.
* Removed the 80% API usage email notice.
* Changed the text in the API usage emails.
* Converted emails sent from plain text to HTML.
* Fixed undefined indexes.
* Added "Free" plan to list of available plans.

= 1.0.1 =
* Added utm_campaign parameters to help tab links.

= 1.0.0 =
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

