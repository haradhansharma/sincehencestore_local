=== WPSSO WP Sitemaps XML with News, Image, and Video Sitemaps ===
Plugin Name: WPSSO WP Sitemaps XML
Plugin Slug: wpsso-wp-sitemaps
Text Domain: wpsso-wp-sitemaps
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-wp-sitemaps/assets/
Tags: google news, image sitemap, news sitemap, video sitemap, woocommerce
Contributors: jsmoriss
Requires Plugins: wpsso
Requires PHP: 7.4.33
Requires At Least: 5.9
Tested Up To: 6.7.0
WC Tested Up To: 9.3.3
Stable Tag: 8.4.0

Extend the WordPress sitemaps XML with article modification times, alternate languages, news sitemaps, image sitemaps, and video sitemaps.

== Description ==

<!-- about -->

**Extends the WordPress Sitemaps XML:**

Improve the WordPress sitemaps XML with article modification times, alternate language URLs, news sitemaps, images sitemaps, video sitemaps (requires WPSSO Core Premium for video details) and more.

<!-- /about -->

**Include Alternate Languages:**

Include [localized pages for Google](https://developers.google.com/search/docs/advanced/crawling/localized-versions#sitemap) (ie. alternate language URLs) from PolyLang and WMPL.

**Include Images and Videos:**

Optionally include images and videos in the WordPress sitemaps XML. Note that video information requires the WPSSO Core Premium plugin for video details.

**Include Post Type Archives:**

Include missing post type archive pages in WordPress sitemaps (like the WooCommerce shop page and The Events Calendar events page).

**Select Post Types and Taxonomies:**

Optionally include or exclude post types and taxonomies from the WordPress sitemaps XML.

**Exclude Noindex and Redirected URLs:**

Exclude noindex and redirected posts, pages, custom post types, taxonomies (categories, tags, etc.), and user profiles pages from the WordPress sitemaps XML.

<h3>WPSSO Core Required</h3>

WPSSO WP Sitemaps XML (WPSSO WPSM) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which creates extensive and complete structured data to present your content at its best for social sites and search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO WP Sitemaps XML add-on](https://wpsso.com/docs/plugins/wpsso-wp-sitemaps/installation/install-the-plugin/).
* [Uninstall the WPSSO WP Sitemaps XML add-on](https://wpsso.com/docs/plugins/wpsso-wp-sitemaps/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

<h3 class="top">Version Numbering</h3>

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes and/or incompatible API changes (ie. breaking changes).
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev &lt; a (alpha) &lt; b (beta) &lt; rc (release candidate).

<h3>Standard Edition Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-wp-sitemaps/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-wp-sitemaps/)

<h3>Development Version Updates</h3>

<p><strong>WPSSO Core Premium edition customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<p><strong>WPSSO Core Standard edition users (ie. the plugin hosted on WordPress.org) have access to <a href="https://wordpress.org/plugins/wpsso-wp-sitemaps/advanced/">the latest development version under the Advanced Options section</a>.</strong></p>

<h3>Changelog / Release Notes</h3>

**Version 8.4.0 (2024/08/25)**

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

== Upgrade Notice ==

= 8.4.0 =

(2024/08/25) Changed the main instantiation action hook from 'init_objects' to 'init_objects_preloader'.

