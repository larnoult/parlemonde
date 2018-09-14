<?php /*
+----+-----+-----+-----+-----+----+-----+-----+-----+-----+-----+-----+
|          . _..::__:  ,-"-"._       |7       ,     _,.__             |
|  _.___ _ _<_>`!(._`.`-.    /        _._     `_ ,_/  '  '-._.---.-.__|
|.{     " " `-==,',._\{  \  / {)     / _ ">_,-' `                mt-2_|
+ \_.:--.       `._ )`^-. "'      , [_/(                       __,/-' +
|'"'     \         "    _L       oD_,--'                )     /. (|   |
|         |           ,'         _)_.\\._<> 6              _,' /  '   |
|         `.         /          [_/_'` `"(                <'}  )      |
+          \\    .-. )          /   `-'"..' `:._          _)  '       +
|   `        \  (  `(          /         `:\  > \  ,-^.  /' '         |
|             `._,   ""        |           \`'   \|   ?_)  {\         |
|                `=.---.       `._._       ,'     "`  |' ,- '.        |
+                  |    `-._        |     /          `:`<_|h--._      +
|                  (        >       .     | ,          `=.__.`-'\     |
|                   `.     /        |     |{|              ,-.,\     .|
|                    |   ,'          \   / `'            ,"     \     |
+                    |  /             |_'                |  __  /     +
|                    | |                                 '-'  `-'   \.|
|                    |/                Maps Marker Pro              / |
|                    \.    The most comprehensive & user-friendly   ' |
+                              mapping solution for WordPress         +
|                     ,/           ______._.--._ _..---.---------._   |
|    ,-----"-..?----_/ )      _,-'"             "                  (  |
|.._(                  `-----'                                      `-|
+----+-----+-----+-----+-----+----+-----+-----+-----+-----+-----+-----+
ASCII Map (C) 1998 Matthew Thomas (freely usable as long as this line is included)
Plugin Name: Maps Marker Pro &reg;
Plugin URI: https://www.mapsmarker.com
Description: The most comprehensive & user-friendly mapping solution for WordPress
Tags: map, maps, Leaflet, OpenStreetMap, geoJSON, json, jsonp, OSM, travelblog, opendata, open data, opengov, open government, ogdwien, WMTS, geoRSS, location, geo, geo-mashup, geocoding, geolocation, travel, mapnick, osmarender, mapquest, geotag, geocaching, gpx, OpenLayers, mapping, bikemap, coordinates, geocode, geocoding, geotagging, latitude, longitude, position, route, tracks, google maps, googlemaps, gmaps, google map, google map short code, google map widget, google maps v3, google earth, gmaps, ar, augmented-reality, wikitude, wms, web map service, geocache, geocaching, qr, qr code, fullscreen, marker, marker icons, layer, multiple markers, karte, blogmap, geocms, geographic, routes, tracks, directions, navigation, routing, location plan, YOURS, yournavigation, ORS, openrouteservice, widget, bing, bing maps, microsoft, map short code, map widget, kml, cross-browser, fully documented, traffic, bike lanes, map short code, custom marker text, custom marker icons and text, sd
Version: 3.1
Requires at least: 3.3
Tested up to: 4.8
Author: MapsMarker.com e.U.
Author URI: https://www.mapsmarker.com
Customer Area: https://www.mapsmarker.com/login
Terms of Service: https://www.mapsmarker.com/tos
Privacy Policy: https://www.mapsmarker.com/privacy
Copyright 2011-2017 - MapsMarker.com e.U. - All rights reserved
MapsMarker &reg;
*/

if (!defined('WPINC')) { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

require_once 'leaflet-core.php';

if (class_exists('LeafletmapsmarkerPro')) {
	register_activation_hook(__FILE__, array('LeafletmapsmarkerPro', 'lmm_activate'));
	register_deactivation_hook(__FILE__, array('LeafletmapsmarkerPro', 'lmm_deactivate'));
}