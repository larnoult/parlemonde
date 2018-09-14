=== Leaflet Maps Marker (Google Maps, OpenStreetMap, Bing Maps) ===
Contributors:      harmr, tgelz, waseem_senjer
Plugin Name:       Leaflet Maps Marker
Plugin URI:        https://www.mapsmarker.com
Tags:              Google Maps, googlemaps, OpenStreetMap, bing, map, maps, mapping, gpx, route, location, store locator, placemarker
Author URI:        https://www.mapsmarker.com
Author:            MapsMarker.com e.U.
Requires at least: 3.3
Tested up to:      4.8
Stable tag:        3.12.1
License:           GPLv2

The most comprehensive & user-friendly mapping solution for WordPress

== Description ==

We are working hard on delivering the best mapping solution available – helping you to share your favorite spots and tracks.

Display locations and directions on your WordPress site. Organize customized icons in tidy layers on a variety of maps and even in augmented reality browsers. Leaflet Maps Marker is your individual Geo-CMS that features highest security standards and a moral code. 

With Leaflet Maps Marker, you can

* pin your favorites places with **markers**,
* search for locations by using keyless geocoding providers like MapZen Search, Algolia Places or Photon@MapsMarker (optional: use the geocoding providers MapQuest Geocoding or Google Places with mandatory API key registration),
* show **directions** for your locations,
* choose from over **1000 free, customizable icons** from [Maps Icons Collection](https://mapicons.mapsmarker.com),
* add **popup description text or images** for each marker,
* organize your markers in **layers**,
* choose an **individual basemap, size and zoom level** for each marker and layer map
* display your maps by just adding a **shortcode** - e.g. [mapsmarker marker="1"] - to posts, pages, widgets and templates
* on the basemap of your choice: **Open Street Map**, **Google Maps** (road, satellite, hybrid, terrain), **Bing** (aerial, aerials + labels, road), **Mapbox** (road, satellite) and any **custom WMTS-map**
* and even in **augmented-reality** browsers like Wikitude!

This plugin is built by a team with a vision. We follow a moral code and value the plugin’s security, your privacy and the documentation of our work. Read our [mission statement](https://www.mapsmarker.com/our-vision-and-mission-statement/) here. 

= FULL FEATURE LIST =

**Frontend**

* show directions for your locations using Google Maps, [yournavigation.org](http://www.yournavigation.org) or [openrouteservice.org](http://www.openrouteservice.org)
* configure up to 10 WMS servers to display additional information from [external geo data providers](https://www.mapsmarker.com/wms) (like the European Environment Agency) on your maps
* export your markers as KML file for displaying in Google Earth ([view screenshot](https://demo.mapsmarker.com/features/google-earth-integration/)) or Google Maps ([view screenshot](https://demo.mapsmarker.com/features/google-maps-import/))
* export your markers as GeoJSON for embedding in external websites or apps
* subscribe to your markers via GeoRSS or for embedding in external websites or apps
* export your markers as ARML for displaying in the augmented-reality browser from Wikitude ([view screenshots](https://demo.mapsmarker.com/features/sample-page/))
* export your markers and layers as XLSX, XLS, CSV or ODS-file
* show recent marker entries in a widget anywhere on your site
* unobtrusive scale control 
* keyboard navigation support for maps
* display a list of markers below layer maps
* show standalone maps in fullscreen mode
* automatically generated geo sitemap for all maps ([for submitting to Google](https://www.mapsmarker.com/docs/misc/how-to-submit-your-geo-sitemap-to-google/))
* automatically add microformat geo-markup to maps to make them machine-readable
* create QR codes for standalone maps
* automatically add meta-tags with location information to maps
* use maps that do not reflect the real world (e.g. game, indoor or photo maps) by adding basemaps created by [maptiler](https://www.mapsmarker.com/maptiler)

**Backend**

* support for multiple keyless geocoding providers like Mapzen Search, Algolia Places and Photon@Mapsmarker 
* support for geocoding providers MapQuest Geocoding and Google Geocoding with mandatory API keys
* organize your markers in layers or multi-layer-maps
* option to switch between simplified and advanced editor
* TinyMCE button for easily searching and adding maps on post/pages edit screen
* support for custom marker timestamps for more precise KML animations ([view example youtube video](https://demo.mapsmarker.com/features/animated-timeline-in-kml/))
* option to set WordPress roles (administrator, editor, author, contributor) which are allowed to add/edit/delete markers and layers
* option to [add marker directly](https://www.mapsmarker.com/docs/basic-usage/how-to-create-maps-directly-by-using-shortcodes-only/) to posts or pages without saving them to your database
* audit log for changes on markers & layers (saving first created by/on and last updated by/on info)
* mass actions for markers (assignment to layers, deletions, duplications)
* option to reset plugin settings to defaults
* dynamic preview of maps in backend (no need to reload)
* option to select plugin default language in settings for backend and frontend
* WordPress Admin Bar integration to quickly access plugins features
* admin dashboard widget showing latest markers 
* support for google maps and bing map localization (cultures)

= TECHNICAL DETAILS =

**Frontend**

* full support for responsive designs (automatic resizing of maps if viewport is wider than map width)
* full RTL (Right-to-Left) language support
* full UTF8-support for cyrillic, chinese or other alphabets on marker/layername and marker popup text
* support for 46 languages – please check out our [translations platform](https://translate.mapsmarker.com/projects/lmm) if you want to contribute
* support for Retina displays to display maps in a higher resolution
* GeoJSON feeds for every marker and layer with [JSONP support](https://www.mapsmarker.com/docs/how-to-use-jsonp-for-integration-of-markers-or-layers-on-external-sites/)
* option to select plugin default language in settings for backend and frontend

**Backend**

* WordPress MultiSite compatibility
* plugin was successfully tested to ÖNORM 7700 and OWASP TOP 10 for security issues
* integrated check for known incompatible plugins (and instructions on how to fix)
* use of WordPress settings API for storing options
* TinyMCE editor on backend for editing popup texts
* dynamic changelog to show all changes since your last plugin update
* Security Coding: best practice (use of prepared SQL statements to prevent SQL injections; use of WordPress nounces on forms to prevent attacks and input mistakes; use of custom function names and enqueue plugin scripts/css only on plugin pages to prevent conflicts with other plugins)
* smooth update functions
* complete uninstall: one-click removal with zero data residue (also on WordPress MultiSite installations)

= 46 TRANSLATIONS - AND COUNTING! =

We feature full RTL (right-to-left) language support as well as full support for cyrillic, chinese and other characters with UTF-8. 
Leaflet Maps Marker makes it easy to switch between languages. Thanks to over 100 translators around the world, more languages are added regularly. Please feel free to contribute (and earn a license key in return) at [https://translate.mapsmarker.com](https://translate.mapsmarker.com)

**Available translations**

* Afrikaans (af) thanks to Hans
* Arabic (ar) thanks to Abdelouali Benkheil, Aladdin Alhamda, Nedal Elghamry, yassin and Abdelouali Benkheil
* Bengali (ba_BD) thanks to Nur Hasan
* Bosnian (bs_BA) thanks to Kenan Dervišević
* Bulgarian (bg_BG) thanks to Andon Ivanov
* Catalan (ca) thanks to Vicent Cubells, Efraim Bayarri and Marta Espinalt
* Chinese (zh_CN) thanks to John Shen and ck
* Chinese (zh_TW) thanks to jamesho Ho
* Croatian (hr) thanks to Neven Pausic, Alan Benic and Marijan Rajic
* Czech (cs_CZ) thanks to Viktor Kleiner and Vlad Kuzba
* Danish (da_DK) thanks to Mads Dyrmann Larsen and Peter Erfurt
* Dutch (nl_NL) thanks to Marijke Metz, Patrick Ruers, Fokko van der Leest and Hans Temming
* English (en_US)
* Finnish (fi_FI) thanks to Jessi Bjoerk
* French (fr_FR) thanks to Vincèn Pujol and Rodolphe Quiedeville, Fx Benard, cazal cédric, Fabien Hurelle and Thomas Guignard
* Galician (gl_ES) thanks to Fernando Coello
* German (de_DE)
* Greek (el) thanks to Philios Sazeides, Evangelos Athanasiadis and Vardis Vavoulakis
* Hebrew (he_IL) thanks to Alon Gilad and kobi levi
* Hindi (hi_IN) thanks to by Outshine Solutions and Guntupalli Karunakar
* Hungarian (hu_HU) thanks to István Pintér and Csaba Orban
* Indonesian (id_ID) thanks to Andy Aditya Sastrawikarta, Emir Hartato and Phibu Reza
* Italian (it_IT) thanks to Luca Barbetti and Angelo Giammarresi
* Japanese (ja) thanks to Shu Higashi and Taisuke Shimamoto
* Korean (ko_KR) thanks to Andy Park
* Latvian (lv) thanks to Juris Orlovs and Eriks Remess
* Lithuanian (lt_LT) thanks to Donatas Liaudaitis
* Malawy (ms_MY) thanks to Mohd Zulkifli
* Norwegian/Bokmal (nb_NO) thanks to Inge Tang
* Polish (pl_PL) translation thanks to Pawel Wyszynski, Tomasz Rudnicki, Robert Pawlak and Daniel
* Portuguese (pt_BR) thanks to Fabio Bianchi, Andre Santos and Antonio Hammerl
* Portuguese (pt_PT) translation thanks to Joao Campos
* Romanian (ro_RO) translation thanks to Arian, Daniel Codrea and Flo Bejgu, http://www.inboxtranslation.com
* Russian (ru_RU) thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies) and Vyacheslav Strenadko
* Slovak (sk_SK) thanks to Zdenko Podobny
* Slovenian (sl_SL) thanks to Anna Dukan
* Spanish (es_ES) thanks to David Ramirez, Alvaro Lara, Ricardo Viteri, Juan Valdes and Marta Espinalt
* Spanish (es_MX) thanks to Victor Guevara and Eze Lazcano
* Swedish (sv_SE) thanks to Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson, Elger Lindgren, Anton Andreasson and Tony Lygnersjö
* Thai (th) thanks to Makarapong Chathamma and Panupong Siriwichayakul
* Turkish (tr_TR) thanks to Emre Erkan and Mahir Tosun
* Uighur (ug) thanks to Yidayet Begzad
* Ukrainian (uk_UK) thanks to Andrexj and Sergey Zhitnitsky and Mykhailo
* Vietnamese (vi) thanks to Hoai Thu
* Yiddish (yi) thanks to Raphael Finkel

For full credits of each translations please visit [https://www.mapsmarker.com/languages](https://www.mapsmarker.com/languages) (credit links had to be removed from readme-file as they were considered as spam by WordPress :-/ )

= Need more power? Try Maps Marker Pro =

* [latest [leaflet.js](http://www.leafletjs.com) version for higher performance & usability](https://www.mapsmarker.com/v3.0p)
* [significantly improved performance for Google basemaps by using the leaflet plugin GoogleMutant](https://www.mapsmarker.com/v3.0p)
* [pre-loading for tiles beyond the edge of the visible map to prevent showing background behind tiles images when panning a map](https://www.mapsmarker.com/v3.0p)
* animated [marker clustering](https://www.mapsmarker.com/pro-feature-clustering)
* [organize your markers in categories and to toggle their visibility on frontend](https://www.mapsmarker.com/v2.7p)
* [WPML/Polylang translation support for multilingual maps](https://www.mapsmarker.com/multilingual)
* [dynamic list of markers supporting paging, searching and sorting - including distance from current position](https://www.mapsmarker.com/v2.7p)
* [home button allowing your visitors to reset the map to its original state](https://www.mapsmarker.com/v2.7p)
* display [GPX tracks](https://www.mapsmarker.com/pro-feature-gpx)
* [mobile optimized maps] (no jQuery needed) (https://www.mapsmarker.com/pro-feature-nojquery)
* [mobile web app support and optimized mobile viewport](https://www.mapsmarker.com/pro-feature-webapp)
* [geolocation](https://www.mapsmarker.com/v1.9p) - show-and-follow with real-time updates
* support for [CSV/XLS/XLSX/ODS import and export](https://www.mapsmarker.com/pro-feature-import) for bulk additions and bulk updates of markers
* [HTML5 fullscreen maps](https://www.mapsmarker.com/pro-feature-html-fullscreen-maps)
* [minimaps](https://www.mapsmarker.com/pro-feature-minimaps)
* [custom Google Maps styling](https://www.mapsmarker.com/pro-feature-google-styling)
* [option to remove backlinks](https://www.mapsmarker.com/pro-feature-backlink-upload-button)
* [QR codes with custom backgrounds](https://www.mapsmarker.com/pro-feature-qrcode)
* [upload icon button & custom icon directory](https://www.mapsmarker.com/pro-feature-backlink-upload-button)
* [backup and restore of settings](https://www.mapsmarker.com/pro-feature-backup-restore)
* [advanced recent marker widget](https://www.mapsmarker.com/pro-feature-advanced-widget)
* [MapsMarker API and MMPAPI class access](https://www.mapsmarker.com/pro-feature-mapsmarker-api)
* [whitelabel backend admin pages](https://www.mapsmarker.com/pro-feature-whitelabel)
* [advanced permission settings](https://www.mapsmarker.com/pro-feature-advanced-permissions)
* [improved performance for layer maps with a huge number of markers (parsing of GeoJSON is up to 3 times faster)](https://www.mapsmarker.com/v1.2.1p)
* [improved performance for layer maps by asynchronous loading of markers via GeoJSON](https://www.mapsmarker.com/v1.6p)
* [support for dynamic switching between simplified and advanced editor (no more reloads needed)](https://www.mapsmarker.com/v1.5.7p)
* [support for filtering of marker icons on backend (based on filename)](https://www.mapsmarker.com/v1.5.7p)
* [support for changing marker IDs and layer IDs from the tools page](https://www.mapsmarker.com/v1.5.7p)
* [support for bulk updates of marker maps on the tools page for selected layers only](https://www.mapsmarker.com/v1.5.7p)
* [option to add markernames to popups automatically](https://www.mapsmarker.com/v1.5.8p)
* [map moves back to initial position after popup is closed](https://www.mapsmarker.com/v1.5.8p)
* [option to disable loading of Google Maps API for higher performance if alternative basemaps are used only](https://www.mapsmarker.com/v1.6p)
* [map parameters can be overwritten within shortcodes (e.g. [mapsmarker marker="1" height="100"])](https://www.mapsmarker.com/v1.6p)
* [tool for monitoring "active shortcodes for already deleted maps"](https://www.mapsmarker.com/v1.8p)
* [layer maps: center map on markers and open popups by clicking on list of marker entries](https://www.mapsmarker.com/v1.8p)
* [support for shortcodes in popup texts](https://www.mapsmarker.com/v1.3p)
* [support for setting global maximum zoom level to 21 (tiles from basemaps with lower native zoom levels will be upscaled automatically)](https://www.mapsmarker.com/v1.5p)
* [duplicate markers](https://www.mapsmarker.com/v1.5.1p)
* [improved accessibility/screen reader support by using proper alt texts](https://www.mapsmarker.com/v1.9.2p)
* [support for duplicating layer maps (without assigned markers)](https://www.mapsmarker.com/v1.9.3p)
* [bulk actions for layers (duplicate, delete layer only, delete & re-assign markers)](https://www.mapsmarker.com/v1.9.3p)
* Parsing of shortcodes in popuptexts
* [support for custom Mapbox basemaps](https://www.mapsmarker.com/v2.5p)
* [optimized editing workflow - no more reloads needed due to AJAX support](https://www.mapsmarker.com/v2.5p)
* [support for URL hashes for specific map views](https://www.mapsmarker.com/v2.6p)
* [highlight a marker on a layer map by opening its popup via shortcode attribute [mapsmarker layer="1" highlightmarker="2"] or by adding ?highlightmarker=2 to the URL where the map is embedded](https://www.mapsmarker.com/v2.6p)
* [XML sitemaps integration: improved local SEO value by automatically adding links to KML maps to your XML sitemaps (if plugin "Google XML Sitemaps" is active)](https://www.mapsmarker.com/v2.6p)
* [support for sorting list of markers below layer maps by distance from layer center](https://www.mapsmarker.com/v2.6p)
* [improved backend usability by listing all contents (posts, pages, CPTs, widgets) where each shortcode is used](https://www.mapsmarker.com/v2.6p)
* [Marker validity check for layer assignements](https://www.mapsmarker.com/v2.7p)
* [RESTful API allowing you to access some of the common core functionalities](https://www.mapsmarker.com/restapi)
* [Javascript Events API for LeafletJS to to attach events handlers to markers and layers](https://www.mapsmarker.com/jseventsapi)
* [support for filters and actions](https://www.mapsmarker.com/filters-actions)
* [new option to open popups on mouse hover instead of mouse click](https://www.mapsmarker.com/v3.0p)
* [Pretty permalinks with customizable slug for fullscreen maps and APIs (e.g. yourdomain.com/maps/fullscreen/marker/1)](https://www.mapsmarker.com/v3.0p)
* [support for tooltips to display the marker name as small text on top of marker icons](https://www.mapsmarker.com/v3.0p)
* [support for AMP optimized mobile pages](https://www.mapsmarker.com/v3.0p)
* [new widget "show latest marker map"](https://www.mapsmarker.com/v3.1p)
* [list all markers page enhancement: dropdown added to filter markers by layer](https://www.mapsmarker.com/v3.1p)
* [loading indicator for GeoJSON download and marker clustering](https://www.mapsmarker.com/v3.1p)
* [global basemap setting "nowrap": (if set to true, tiles will not load outside the world width instead of repeating, default: false)](https://www.mapsmarker.com/v3.1p)
* **frequent updates**
* **dedicated and priority support** from the plugin author
* non-expiring license keys

If you want to compare the free and pro version side by side, please visit [https://www.mapsmarker.com/comparison](https://www.mapsmarker.com/comparison).

Interested? Check out our [demo maps](https://demo.mapsmarker.com) (including admin area access) or start a [free trial](https://www.mapsmarker.com/docs/how-to-install-the-plugin/) of Maps Marker Pro.

Leaflet Maps Marker also includes a pro upgrader which allows you to start a free 30 day trial easily with a few clicks. 

= Plugin's Official Site =
https://www.mapsmarker.com

* [Affiliate program](https://affiliates.mapsmarker.com/) - [Reseller program](https://www.mapsmarker.com/reseller) - [FAQ](https://www.mapsmarker.com/faq/) - [Docs](https://www.mapsmarker.com/docs/) - [Support](https://www.mapsmarker.com/support/) - [Twitter](https://twitter.com/mapsmarker) - [Facebook](https://facebook.com/mapsmarker) - [Google+](https://www.mapsmarker.com/+) - [Translations](https://translate.mapsmarker.com/projects/lmm) - [Hackerone](https://www.mapsmarker.com/hackerone)

== Installation ==

= The Famous 3-Minute Installation =

1. Login on your WordPress site with your user account (needs to have admin rights!)
2. Select "Add New" from the "Plugins" menu
3. Search for **Leaflet Maps Marker**
4. Click on "Install now" below the entry "Leaflet Maps Marker (Google Maps, OpenStreetMap, Bing Maps)"
5. Click on "OK" on the popup "Are you sure you want to install this plugin?"
6. Click "Activate Plugin"

Done. You can create your first marker map.

After installation you will also find a 'Leaflet Maps Marker' menu in your WordPress admin panel and in WordPress Admin Bar.
For basic usage and tutorials, you can also have a look at [https://www.mapsmarker.com/docs](https://www.mapsmarker.com/docs "Docs").

= Detailed Installation Instructions =

If you do not want to use the built-in plugin installation procedure from WordPress, you can also install the plugin manually:

1. Navigate to [https://wordpress.org/plugins/leaflet-maps-marker/](https://wordpress.org/plugins/leaflet-maps-marker/)
2. Click on red button "Download Version X.X" and download the plugin as ZIP-file
3. Login on your WordPress site with your user account (needs to have admin rights!)
4. Select "Add New" from the "Plugins" menu
5. Select "Upload" from the "Install Plugins"-submenu
6. Click on the button and select the previously downloaded ZIP-file
7. Click "Install now"
8. Click "Activate Plugin"

Done. You can create your first marker map.

= Detailed Installation Instructions (via FTP) =

1. Navigate to [https://wordpress.org/plugins/leaflet-maps-marker/](https://wordpress.org/plugins/leaflet-maps-marker/)
2. Click on red button "Download Version X.X" and download the plugin as ZIP-file
3. Login on your WordPress site with your user account (needs to have admin rights!)
4. unzip and upload the leaflet-maps-marker folder to the `/wp-content/plugins/` directory
5. Login on your WordPress site with your user account (needs to have admin rights!)
6. Activate the plugin "Leaflet Maps Marker" through the "Plugins" menu in WordPress

Done. You can create your first marker map.

== Frequently Asked Questions ==
Do you have questions or issues with Leaflet Maps Marker? Please use the following support channels appropriately.
One personal request: before you post a new support ticket in the [Wordpress Support Forum](http://wordpress.org/support/plugin/leaflet-maps-marker), please follow the instructions from [https://www.mapsmarker.com/readme-first](https://www.mapsmarker.com/readme-first) which give you a guideline on how to deal with the most common issues.

1. [FAQ](https://www.mapsmarker.com/faq/)
2. [Docs](https://www.mapsmarker.com/docs/)
3. [Wordpress Support Forum](http://wordpress.org/support/plugin/leaflet-maps-marker) (free community support)
4. [Upgrade to Pro](https://www.mapsmarker.com/install) (paid developer support)

[More info on support](http://mapsmarker.com/support/)

== Screenshots ==
For demo maps please visit [https://www.mapsmarker.com/demo](https://www.mapsmarker.com/demo).

01. Frontend: marker map (with open popup and image, basemap: OGD Vienna satellite, overlay: OGD Vienna addresses, controlbox: expanded)
02. Frontend: layer map (5 marker, different icons, basemap: OpenStreetMap, controlbox: collapsed)
03. Frontend: map with WMS layer enabled and additional marker
04. Frontend: layer map in Google Earth (via KML export)
05. Frontend: layer map in Google Maps (via KML export)
06. Frontend: showing marker in Wikitude (via Augmented-Reality API)
07. Backend: simplified editor - create maps with an intuitive interface
08. Backend: advanced editor (optional) - allows you to set all available options
09. Backend: add/edit layer - allows you to fully customize the layer map (used basemap & overlays, set center, map size, zoom, controlbox status etc).
10. Backend: markerlist - for easy administration of all your markers
11. Backend: layerlist - for easy administration of all your layers
12. Backend: plugin settings page allows you to easily set all necessary settings & restore the defaults if you messed something up
13. Backend: csv-export of all markers - just copy and paste into your favorite spreadsheet application for use in other applications
14. Backend: tools section - allows mass-actions more markers (assignements, deletions)
15. Pro preview: marker clustering
16. Pro preview: geolocation support: show and follow your location when viewing maps
17. Pro preview: support for CSV/XLS/XLSX/ODS import and export for bulk additions and bulk updates
18. Pro preview: GPX tracks
19. Pro preview: HTML5 fullscreen maps
20. Pro preview: Minimaps
21. Pro preview: mobile web app support for fullscreen maps and optimized mobile viewport
22. Pro preview: custom Google Maps styling
23. Pro preview: QR codes with custom backgrounds
24. Pro preview: upload icon button & custom icon directory
25. Pro preview: backup and restore of settings
26. Pro preview: advanced recent marker widget
27. Pro preview: MapsMarker API
28. Pro preview: whitelabel backend admin pages
29. Pro preview: advanced permission settings
30. Pro preview: filter maps on frontend
31. Pro preview: dynamic list of markers

== Other Notes ==

= Licence =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, please consider upgrading to the pro version.
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License included with this plugin for more details.

= Licenses for used libraries, services and images =
* Leaflet.js, Copyright (c) 2010-2011: Cloudemade, Copyright (c) 2010-current year: Vladimir Agafonkin [http://www.leafletjs.com](http://www.leafletjs.com)
* OpenStreetMap - The Free Wiki World Map: [OpenStreetMap License](http://wiki.openstreetmap.org/wiki/OpenStreetMap_License)
* Map Icons Collection by Nicolas Mollet - [https://mapicons.mapsmarker.com](https://mapicons.mapsmarker.com)
* Datasource OGD Vienna maps: Stadt Wien, Creative Commons Attribution (by) [http://data.wien.gv.at](http://data.wien.gv.at)
* Jquery TimePicker, by Trent Richardson, [http://trentrichardson.com/examples/timepicker/](http://trentrichardson.com/examples/timepicker/), licence: GPL
* Adress autocompletion powered by [Google Places API](https://developers.google.com/places/documentation/autocomplete)
* Map center icon [Joseph Wain](http://glyphish.com/) - Creative Commons Attribution (by)
* Plus-, json-, layer-, language- & csv-export icon by icon by [Yusuke Kamiyamane](http://www.pinvoke.com/) - Creative Commons Attribution (by)
* Question Mark icon by [RandomJabber](http://www.randomjabber.com/)
* Home icon by [ Pro Theme Design](http://prothemedesign.com/) - Creative Commons Attribution (by)
* Images for changelog from [Firefox release notes](http://www.mozilla.org/en-US/firefox/11.0/releasenotes/), licence: Creative Commons Attribution ShareAlike (CC BY-SA 3.0)
* Home-Icon from [Pro Theme Design](http://prothemedesign.com), licence: Creative Commons Attribution (by)
* Editor-Switch-Icon by AMAZIGH Aneglus, license: GNU/GPL
* Submenu icons from [Bijou](http://bijou.im) and [Iconic](http://somerandomdude.com/work/iconic/) icon sets (GPL)
* PHPExcel for import/export [http://phpexcel.codeplex.com/](http://phpexcel.codeplex.com/) (LGPL)
= Special thanks to =
* Sindre Wimberger ([http://www.sindre.at](http://www.sindre.at)) for help with bugfixing & geo-consulting
* Julia Loew ([http://www.weiderand.net](http://www.weiderand.net)) for logo and corporate design
* Wordpress-Settings-API-Class by Aliso the geek ([http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/](http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/))
* [Hind](http://www.nanodesu.ru) who originally release a basic Leaflet plugin (not available anymore) which I used partly as a basis for Leaflet Maps Marker plugin
* [shramov](http://psha.org.ru/b/leaflet-plugins.html) for bing and google maps plugins for leaflet

Synonyms: Map Maker - Maps Maker - mapmaker - mapsmaker

= Trademark and copyright =
MapsMarker &reg;
Copyright 2011-2017, MapsMarker.com e.U., All Rights Reserved

== Upgrade Notice ==
= 3.12.1 =
No manual actions needed for updating to this version. If you experience any issues, please [create a new topic in the support forum](https://wordpress.org/support/plugin/leaflet-maps-marker).

== Changelog ==
compatibility & security fixes - see [https://www.mapsmarker.com/v3.12.1](https://www.mapsmarker.com/v3.12.1) for more details

[changelog for all versions](https://www.mapsmarker.com/changelog)