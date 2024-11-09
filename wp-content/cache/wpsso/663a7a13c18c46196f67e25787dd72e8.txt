=== WPSSO Product Metadata (aka Custom Fields) for WooCommerce SEO - MPN, ISBN, GTIN, UPC, EAN, Global Identifiers ===
Plugin Name: WPSSO Product Metadata for WooCommerce SEO
Plugin Slug: wpsso-wc-metadata
Text Domain: wpsso-wc-metadata
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-wc-metadata/assets/
Tags: gtin, upc, ean, isbn, mpn
Contributors: jsmoriss
Requires Plugins: wpsso, woocommerce
Requires PHP: 7.4.33
Requires At Least: 5.9
Tested Up To: 6.7.0
WC Tested Up To: 9.3.3
Stable Tag: 4.3.0

MPN, ISBN, GTIN, GTIN-8, UPC, EAN, GTIN-14, net dimensions, and fluid volume for WooCommerce products and variations.

== Description ==

<!-- about -->

Provides additional fields in the WooCommerce "Product data" metabox and under the product webpage "Additional information" section:

* MPN (Manufacturer Part Number)
* ISBN
* GTIN-14
* GTIN-13 (EAN)
* GTIN-12 (UPC)
* GTIN-8
* GTIN
* Net Length / Depth
* Net Width
* Net Height
* Net Weight
* Fluid Volume

<!-- /about -->

The *SSO &gt; WooCommerce Metadata* settings page allows you to enable or disable each product metadata (aka custom field), along with customizing the label and placeholder values for the available languages (aka WordPress locales).

The product global identifier values (ie. MPN, ISBN, GTIN-14, GTIN-13, GTIN-12, GTIN-8, and GTIN) are searchable from both the front-end webpage, and the admin WooCommerce Products page.

The WPSSO Product Metadata for WooCommerce SEO add-on provides Schema (aka Schema.org) mpn, gtin14, gtin13, gtin12, gtin8, gtin, productID isbn, depth (aka length), width, height, weight, and additionalProperty fluid_volume values to the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/) for Google Rich Results, Rich Snippets, and Structured Data.

<h3>Includes WooCommerce Fluid Volume Units</h3>

Includes a **Fluid volume unit** option in the *WooCommerce &gt; Settings &gt; Products* settings page:

* ml
* cl
* l
* US tsp
* US tbsp
* US fl oz
* US cup
* US pt
* US qt
* US gal

<h3>Additonal Meta Tags and Schema Markup</h3>

Automatically provides additional Open Graph product meta tags for enabled product metadata:

* product:ean
* product:isbn
* product:mfr_part_no
* product:upc
* product:weight:value
* product:weight:units

Automatically provides additional Schema Product and Offer properties for enabled product metadata:

* mpn
* gtin14
* gtin13
* gtin12
* gtin8
* gtin
* productID isbn
* depth (aka length)
* width
* height
* weight
* additionalProperty fluid_volume

<h3>WPSSO Core Required</h3>

WPSSO Product Metadata for WooCommerce SEO (WPSSO WCMD) is an add-on for [WooCommerce](https://wordpress.org/plugins/woocommerce/) and the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which creates extensive and complete structured data to present your content at its best for social sites and search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Product Metadata for WooCommerce SEO add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/install-the-plugin/).
* [Uninstall the WPSSO Product Metadata for WooCommerce SEO add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

<h3 class="top">Frequently Asked Questions</h3>

* None.

== Screenshots ==

01. Information shown under the "Additional information" section changes as different variations are selected.
02. Enabled product metadata fields are added seamlessly under the product inventory tab.
03. Enabled depth and volume metadata fields are added seamlessly under the product shipping tab.
04. Enabled product metadata fields are added seamlessly under the product variations tab.

== Changelog ==

<h3 class="top">Version Numbering</h3>

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes and/or incompatible API changes (ie. breaking changes).
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).

<h3>Standard Edition Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-wc-metadata/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-wc-metadata/)

<h3>Development Version Updates</h3>

<p><strong>WPSSO Core Premium edition customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<p><strong>WPSSO Core Standard edition users (ie. the plugin hosted on WordPress.org) have access to <a href="https://wordpress.org/plugins/wpsso-wc-metadata/advanced/">the latest development version under the Advanced Options section</a>.</strong></p>

<h3>Changelog / Release Notes</h3>

**Version 4.3.0 (2024/08/25)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Changed the main instantiation action hook from 'init_objects' to 'init_objects_preloader'.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v18.10.0.
	* WooCommerce v6.0.0.

== Upgrade Notice ==

= 4.3.0 =

(2024/08/25) Changed the main instantiation action hook from 'init_objects' to 'init_objects_preloader'.

