=== FG PrestaShop to WooCommerce ===
Contributors: Kerfred
Plugin Uri: https://wordpress.org/plugins/fg-prestashop-to-woocommerce/
Tags: prestashop, woocommerce, wordpress, importer, convert prestashop to woocommerce, migrate prestashop to woocommerce, prestashop to woocommerce migration, migrator, converter, import, dropshipping
Requires at least: 4.5
Tested up to: 5.0.3
Stable tag: 3.47.3
Requires PHP: 5.3
License: GPLv2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=fred%2egilles%40free%2efr&lc=FR&item_name=fg-prestashop-to-woocommerce&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted

A plugin to migrate PrestaShop e-commerce solution to WooCommerce

== Description ==

This plugin migrates products, categories, tags, images and CMS from PrestaShop to WooCommerce/WordPress.

It has been tested with **PrestaShop versions 1.0 to 1.7** and **WordPress 5.0**. It is compatible with multisite installations.

Major features include:

* migrates PrestaShop products
* migrates PrestaShop product images
* migrates PrestaShop product categories
* migrates PrestaShop product tags
* migrates PrestaShop CMS (as posts or pages)

No need to subscribe to an external web site.

= Premium version =

The **Premium version** includes these extra features:

* migrates PrestaShop product features
* migrates PrestaShop product attributes
* migrates PrestaShop product attribute images
* migrates PrestaShop product accessories
* migrates PrestaShop product combinations
* migrates PrestaShop virtual products
* migrates PrestaShop downloadable products
* migrates PrestaShop employees
* migrates PrestaShop customers
* migrates PrestaShop orders
* migrates PrestaShop ratings and reviews
* migrates PrestaShop discounts/vouchers (cart rules)
* migrates PrestaShop menus
* SEO: Redirect the PrestaShop URLs to the new WordPress URLs
* SEO: Import meta data (browser title, description, keywords, robots) to WordPress SEO
* the employees and customers can authenticate to WordPress using their PrestaShop passwords
* ability to do a partial import
* ability to run the import automatically from the cron (for dropshipping for example)

The Premium version can be purchased on: [https://www.fredericgilles.net/fg-prestashop-to-woocommerce/](https://www.fredericgilles.net/fg-prestashop-to-woocommerce/)

= Add-ons =

The Premium version allows the use of add-ons that enhance functionality:

* Brands: imports the manufacturers
* WPML: imports the translations
* Customer groups: imports the customer groups
* Cost of goods: import the products cost

== Installation ==

= Requirements =
WooCommerce must be installed and activated before running the migration.

= Installation =
1.  Install the plugin in the Admin => Plugins menu => Add New => Upload => Select the zip file => Install Now
2.  Activate the plugin in the Admin => Plugins menu
3.  Run the importer in Tools > Import > PrestaShop
4.  Configure the plugin settings. You can find the PrestaShop database parameters in the PrestaShop file settings.inc.php (PrestaShop 1.5+) or in the PrestaShop Preferences > Database tab (PrestaShop 1.4 and less)
5.  Test the database connection
6.  Click on the import button

== Frequently Asked Questions ==

= I get the message: "[fgj2wp] Couldn't connect to the PrestaShop database. Please check your parameters. And be sure the WordPress server can access the PrestaShop database. SQLSTATE[28000] [1045] Access denied for user 'xxx'@'localhost' (using password: YES)" =

* First verify your login and password to your PrestaShop database.
* If PrestaShop and WordPress are not installed on the same host, you can do this:
- export the PrestaShop database to a SQL file (with phpMyAdmin for example)
- import this SQL file on the same database as WordPress
- run the migration by using WordPress database credentials (host, user, password, database) instead of the PrestaShop ones in the plugin settings.

= The import is not complete =

* You can run the migration again and it will continue where it left off.
* You can add: `define('WP_MEMORY_LIMIT', '512M');` in your wp-config.php file to increase the memory allowed by WordPress
* You can also increase the memory limit in php.ini if you have write access to this file (ie: memory_limit = 1G).

= The images aren't being imported =

* Please check the URL field. It must contain the URL of the PrestaShop home page
* Check that the maintenance mode is disabled in PrestaShop
* Use http instead of https in the URL field

= Are the product combinations/attributes imported? =

* This is a Premium feature available on: https://www.fredericgilles.net/fg-prestashop-to-woocommerce/

= Is there a log file to show the information from the import? =

* Yes since version 1.10.0. First you must put these lines in wp-config.php:<br />
define('WP_DEBUG', true);<br />
define('WP_DEBUG_LOG', true);<br />
And the messages will be logged to wp-content/debug.log.

Don't hesitate to let a comment on the [forum](https://wordpress.org/support/plugin/fg-prestashop-to-woocommerce) or to report bugs if you found some.

== Screenshots ==

1. Parameters screen

== Translations ==
* English (default)
* French (fr_FR)
* Hungarian (hu_HU)
* other can be translated

== Changelog ==

= 3.47.3 =
* Fixed: The default language can be wrong if several shops are defined with a different default language
* Tested with WordPress 5.0.3

= 3.47.1 =
* Fixed: Some NGINX servers were blocking the images downloads
* Tested with WordPress 5.0.2

= 3.47.0 =
* Tested with WordPress 5.0.1

= 3.45.0 =
* Tested with WordPress 5.0

= 3.43.1 =
* Fixed: Some NGINX servers were blocking the images downloads

= 3.43.0 =
* New: Generate the audio and video meta data (ID3 tag, featured image)

= 3.42.0 =
* New: Support the Bengali alphabet
* Fixed: Wrong products pagination with out of stock products

= 3.41.2 =
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'p.reduction_tax' in 'field list'

= 3.41.0 =
* New: Import the images contained in the product short description
* Tested with WordPress 4.9.8

= 3.39.1 =
* Fixed: WordPress database error: [Cannot truncate a table referenced in a foreign key constraint (`wp_wc_download_log`, CONSTRAINT `fk_wc_download_log_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `wp_woocommerce_downloadable_product_permission)]
* Tested with WordPress 4.9.7

= 3.38.1 =
* Fixed: [Cannot truncate a table referenced in a foreign key constraint (`wp_wc_download_log`, CONSTRAINT `fk_wc_download_log_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `wp_woocommerce_downloadable_product_permission)]
* Change: Wording of the label "Remove only previously imported data"

= 3.36.0 =
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'p.id_specific_price_rule' in 'where clause' for PrestaShop 1.4
* Tested with WordPress 4.9.6

= 3.35.3 =
* Fixed: Wrong sale prices if many specific price rules are used

= 3.35.0 =
* New: Support the Arabic language
* Tweak: Delete the wc_var_prices transient when cleaning the imported data
* Tested with WordPress 4.9.5

= 3.32.0 =
* Fixed: Notice: Undefined index: id_category_default
* Tweak: Use WP_IMPORTING

= 3.29.0 =
* New: Display the number of imported media
* Tested with WordPress 4.9.1

= 3.27.3 =
* Tested with WordPress 4.9

= 3.27.0 =
* New: Import the products visibility

= 3.26.0 =
* Fixed: Wrong sale price if the reductions were applied after the tax (PrestaShop 1.6+)
* Tested with WordPress 4.8.2

= 3.23.0 =
* New: Allow HTML in term descriptions

= 3.22.1 =
* Fixed: Sales prices were not imported for multi countries stores with sales prices defined for all countries

= 3.22.0 =
* New: Import the barcode (compatible with the WooCommerce Barcode ISBN plugin)
* New: Set the products as draft if they are not available for order

= 3.21.1 =
* Tweak: code refactoring

= 3.21.0 =
* New: Check if we need the Customer Groups add-on

= 3.20.1 =
* Fixed: Wrong sale price for products with reduction prices defined for several countries

= 3.20.0 =
* Fixed: Security cross-site scripting (XSS) vulnerability in the Ajax importer

= 3.19.1 =
* Fixed: Wrong number of customers and employees displayed
* Tested with WordPress 4.8.1

= 3.19.0 =
* New: Import the image caption in the media attachment page

= 3.16.0 =
* New: Block the import if the URL field is empty and if the media are not skipped
* New: Add error messages and information

= 3.15.1 =
* Fixed [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'p.id_product_attribute' in 'field list'

= 3.15.0 =
* New: Add the percentage in the progress bar
* New: Display the progress and the log when returning to the import page
* Change: Restyling the progress bar
* Fixed: Typo - replace "complete" by "completed"
* Tested with WordPress 4.8

= 3.14.0 =
* New: Compatibility with PrestaShop 1.0

= 3.13.0 =
* Tested with WordPress 4.7.5

= 3.12.0 =
* New: Add a choice to import either the thumbnail product images or the full size product images

= 3.11.5 =
* Tested with WordPress 4.7.4

= 3.11.3 =
* Fixed: the prices were all on sale when importing the prices with tax

= 3.11.0 =
* New: Remove accents in the file names
* New: Import the specific prices for PrestaShop versions 1.4 and more

= 3.10.2 =
* Fixed: Import hangs if some CMS articles have no content

= 3.10.0 =
* Tweak: Clear WooCommerce transients when emptying WordPress content

= 3.9.0 =
* New: Test if we need the Premium version
* New: Test if we need the Brands add-on
* New: Test if we need the WPML add-on
* Tested with WordPress 4.7.3

= 3.8.2 =
* Fixed: Stock not imported when using multishops

= 3.8.1 =
* Fixed: Term meta data not deleted when we delete the imported data only

= 3.8.0 =
* New: Display the number of products categories and CMS categories found in PrestaShop
* Fixed: The categories with duplicate names were not imported

= 3.7.4 =
* Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'p.reduction_price' in 'field list'

= 3.7.3 =
* Fixed: Images with Hebraic characters or encoded characters were not imported

= 3.7.2 =
* Fixed: Rounding error when importing with tax included

= 3.7.1 =
* Fixed: PrestaShop 1.4 products not imported

= 3.7.0 =
* Tested with WordPress 4.7.2

= 3.6.3 =
* Fixed: Progress bar doesn't reach 100%
* Tweak: Code refactoring

= 3.6.2 =
* Fixed: Existing images attached to imported products were removed when deleting the imported data
* Tested with WordPress 4.7

= 3.6.1 =
* Fixed: Some images with Greek characters were not imported

= 3.5.0 =
* New: Compatibility with PrestaShop 1.7

= 3.4.0 =
* New: Import the reduced prices from PrestaShop 1.1, 1.2 and 1.3
* Fixed: "Notice: Object of class WP_Error could not be converted to int" when WooCommerce is not activated
* Fixed: Wrong progress bar color

= 3.3.1 =
* Fixed: The progress bar didn't move during the first import
* Fixed: The log window was empty during the first import

= 3.3.0 =
* New: Optimization: don't reimport the images that were already imported

= 3.2.2 =
* Fixed: The "IMPORT COMPLETE" message was still displayed when the import was run again

= 3.2.1 =
* Fixed: Database passwords containing "<" were not accepted

= 3.2.0 =
* Tweak: Add a hook to enable the manufacturers translations

= 3.1.1 =
* Fixed: PrestaShop 1.4 compatibility issue: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'cl.id_shop' in 'on clause'

= 3.1.0 =
* New: Authorize the connections to Web sites that use invalid SSL certificates
* Fixed: Duplicated products when PrestaShop contains more than one shop
* Tweak: If the import is blocked, stop sending AJAX requests

= 3.0.0 =
* New: Run the import in AJAX
* New: Add a progress bar
* New: Add a logger frame to see the logs in real time
* New: Ability to stop the import
* New: Compatible with PHP 7

= 2.8.1 =
* Tweak: Remove the accents from the image filenames because that could generate problems on some hosts

= 2.8.0 =
* New: Option to delete only the new imported data
* Fixed: Review link broken

= 2.7.3 =
* Fixed: Notice: Undefined offset
* Fixed: Wrong number of comments displayed
* Tested with WordPress 4.6.1

= 2.7.2 =
* Tested with WordPress 4.6

= 2.7.0 =
* Tweak: Empty the woocommerce_downloadable_product_permissions when emptying the WordPress content

= 2.6.2 =
* Tweak: Increase the speed of counting the terms

= 2.6.1 =
* Tweak: Replace spaces by dashes because images with spaces are not displayed on iPhones
* Tested with WordPress 4.5.3

= 2.6.0 =
* New: Compatible with WooCommerce 2.6.0

= 2.5.2 =
* Fixed: Regression bug: testing the database connection only refreshes the screen

= 2.5.1 =
* Fixed: Invalid characters in the images filenames prevent these images to upload

= 2.5.0 =
* New: Accept the Hebrew characters in the file names
* Tested with WordPress 4.5.2

= 2.4.2 =
* Fixed: Add total_sales, _downloadable and _virtual postmetas to be compatible with the Avada theme

= 2.4.1 =
* Fixed: Notice: Undefined index: id_cms
* Tested with WordPress 4.5

= 2.4.0 =
* New: Don't import the Root category

= 2.3.1 =
* Fixed: Column 'post_content' cannot be null

= 2.3.0 =
* Tweak: Code refactoring

= 2.2.1 =
* Tested with WordPress 4.4.2

= 2.2.0 =
* New: Compatibility with the WooCommerce Layered Nav widget

= 2.1.1 =
* Tested with WordPress 4.4.1

= 2.0.0 =
* Tweak: Restructure the whole code using the BoilerPlate foundation
* New: Make the platform more accessible to more languages

= 1.24.2 =
* Fixed: Fatal error: Call to undefined function add_term_meta()

= 1.24.1 =
* Fixed: Wrong parent categories assigned
* Fixed: Categories with null description were not imported

= 1.24.0 =
* Tweak: Use the WordPress 4.4 term metas

= 1.23.4 =
* Tested with WordPress 4.4

= 1.23.0 =
* New: Option to enable/disable the stock management

= 1.22.0 =
* New: Option to import the EAN13 as the SKU

= 1.21.0 =
* New: Import filenames with Greek characters
* New: Add a link to the FAQ in the connection error message

= 1.20.0 =
* New: Add an Import link on the plugins list page

= 1.18.0 =
* Tweak: code optimization

= 1.17.5 =
* Tested with PrestaShop 1.2

= 1.17.3 =
* Tested with WordPress 4.3.1

= 1.17.1 =
* Fixed: Cache issue with the product categories

= 1.17.0 =
* Fixed: Some medias with accents were not imported
* Tested with WordPress 4.3

= 1.16.6 =
* Tested with WordPress 4.2.4

= 1.16.4 =
* Tested with WordPress 4.2.3

= 1.16.3 =
* Fixed: Hook at the wrong position

= 1.16.1 =
* Fixed: Accept the filenames with Cyrillic characters

= 1.16.0 =
* New: Compatible with PrestaShop 1.1
* Tested with WordPress 4.2.2

= 1.14.0 =
* New: Import the images at the thickbox size instead of the original size
* Tested with WordPress 4.2

= 1.13.0 =
* Fixed: Don't import twice the same medias

= 1.12.0 =
* Fixed: Wrong hook used after product insert (fgp2wc_post_insert_product and not fgp2wc_post_insert_post)

= 1.11.0 =
* Fixed: Change the default database prefix to ps_
* FAQ updated

= 1.10.1 =
* Fixed: Wrong images imported when the image legends are not unique

= 1.10.0 =
* New: Log the messages to wp-content/debug.log
* FAQ updated

= 1.9.1 =
* New: Test the presence of WooCommerce before importing
* Tested with WordPress 4.1.1

= 1.9.0 =
* Fixed: Duplicate products when using more than one shop (PrestaShop 1.5+)
* Fixed: Wrong categories assigned to products when there are category slugs duplicates
* Fixed: the prestashop_query() function was returning only one row

= 1.8.2 =
* Fixed: Some images were imported as question marks
* Fixed: Wrong storage directory for the images without a date

= 1.8.1 =
* Tweak: Optimize the speed of images transfer. Don't try to guess the images location for each image.
* Fixed: The products count didn't include the inactive products

= 1.8.0 =
* New: Compatible with PrestaShop 1.3

= 1.7.0 =
* Tested with WordPress 4.1

= 1.6.0 =
* Tweak: Don't display the timeout field if the medias are skipped

= 1.5.0 =
* FAQ updated
* Tested with WordPress 4.0.1

= 1.4.0 =
* Fixed: WordPress database error: [Duplicate entry 'xxx-yyy' for key 'PRIMARY']

= 1.3.1 =
* Fixed: Some images were not imported on PrestaShop 1.4

= 1.3.0 =
* Fixed: Set the products with a null quantity as "Out of stock"
* New: Import the product supplier reference as SKU if the product reference is empty

= 1.2.0 =
* Update the FAQ

= 1.1.1 =
* Fixed: Some images were not imported

= 1.1.0 =
* Compatible with WooCommerce 2.2
* Fixed: Remove the shop_order_status taxonomy according to WooCommerce 2.2
* Fixed: The cover image was not imported as featured image if it was not the first image
* Fixed: Category image path fixed
* Fixed: The product category images were imported even when the "Skip media" option was checked
* Tweak: Simplify the posts count function

= 1.0.0 =
* Initial version: Import PrestaShop products, categories, tags, images and CMS

== Upgrade Notice ==

= 3.47.3 =
Fixed: The default language can be wrong if several shops are defined with a different default language
Tested with WordPress 5.0.3

= 3.47.1 =
Fixed: Some NGINX servers were blocking the images downloads
Tested with WordPress 5.0.2

= 3.47.0 =
Tested with WordPress 5.0.1
