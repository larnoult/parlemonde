<?php
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'changelog.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

if (get_option('leafletmapsmarker_update_info') == 'show') {
$lmm_version_old = get_option( 'leafletmapsmarker_version_pro_before_update' );
$lmm_version_new = get_option( 'leafletmapsmarker_version_pro' );

$text_a = __('Changelog for version %s','lmm');
$text_b = __('released on','lmm');
$text_c = __('blog post with more details about this release','lmm');
$text_d = __('Translation updates','lmm');
$text_e = __('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm');
$text_f = __('Known issues','lmm');
$new = '<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">';
$changed = '<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">';
$fixed = '<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">';
$security_fixed = '<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-security-fixed.png">';
$transl = '<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">';
$issue = '<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-know-issues.png">';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
<title>Changelog for Maps Marker Pro</title>
<style type="text/css">
<?php
if ( function_exists( 'is_rtl' ) && is_rtl() ) {
	echo 'body{font-family:sans-serif;font-size:12px;line-height:1.4em;margin:0;padding:0 0 0 5px;direction: rtl;unicode-bidi: embed;}'.PHP_EOL;
} else {
	echo 'body{font-family:sans-serif;font-size:12px;line-height:1.4em;margin:0;padding:0 0 0 5px;}'.PHP_EOL;
} ?>
body {background-color:#ffffe0 !important;}
table{line-height:.7em !important;font-size:12px !important;font-family:sans-serif;}
td{line-height:1.1em !important;padding:0px !important;}
.updated{background-color:#FFFFE0 !important;padding:10px;}
a{color:#21759B;text-decoration:none !important;}
a:hover,a:active,a:focus{color:#D54E21;}
hr{color:#E6DB55;}
p{margin:0px !important;color:#000 !important;}
</style></head><body>
<?php
/*****************************************************************************************/

echo '<p style="margin:0.5em 0 0 0;clear:both;"><strong>' . sprintf($text_a, '3.1') . '</strong> - ' . $text_b . ' 08.07.2017 (<a href="https://www.mapsmarker.com/v3.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
optimized performance for Google basemaps by enabling GoogleMutant Javascript library for all users
</td></tr>
<tr><td>' . $new . '</td><td>
new widget "show latest marker map" (thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
Bounty Hunters wanted! Find security bugs to earn cash and licenses - <a href="https://www.mapsmarker.com/hackerone" target="_blank">click here for more details</a>
</td></tr>
<tr><td>' . $new . '</td><td>
global basemap setting "nowrap": (if set to true, tiles will not load outside the world width instead of repeating, default: false)
</td></tr>
<tr><td>' . $new . '</td><td>
list all markers page enhancement: dropdown added to filter markers by layer (thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
loading animation to popups with images to help with DOM creation (thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for "WP Super Cache" debug output which can cause layer maps to break
</td></tr>
<tr><td>' . $new . '</td><td>
loading indicator when clearing the list of markers search field (thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for Admin Custom Login which causes the navigation on the settings page to break
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for Fast Velocity Minify plugin
</td></tr>
<tr><td>' . $new . '</td><td>
email notification to free trial users 3 days before the free trial license key expires
</td></tr>
<tr><td>' . $new . '</td><td>
option "HTML filter for popuptexts" to prevent injection of malicious code - enabled by default (thx jackl via hackerone)
</td></tr>
<tr><td>' . $new . '</td><td>
Looking for developers to recommend to our clients for customizations - more details at <a href="https://www.mapsmarker.com/network" target="_blank">mapsmarker.com/network</a>
</td></tr>
<tr><td>' . $new . '</td><td>
loading indicator for GeoJSON download and marker clustering (thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for theme Divi 3+ which can cause maps to break if option "Where to include Javascript files?" is set to footer
</td></tr>
<tr><td>' . $changed . '</td><td>
enhanced permalink base URL compatibility check to suggest URL if site url ends with /wp/
</td></tr>
<tr><td>' . $changed . '</td><td>
increased timeout for license API fallback calls to prevent issues with registering free trial license keys
</td></tr>
<tr><td>' . $changed . '</td><td>
Autoptimize plugin compatibility check: also verify if option "Also aggregate inline JS?" is set (which is causing maps to break)
</td></tr>
<tr><td>' . $changed . '</td><td>
finished migration to PHP 7.1 on www.mapsmarker.com for higher performance
</td></tr>
<tr><td>' . $changed . '</td><td>
updated EdgeBuffer plugin for pre-loading tiles beyond the edge of the visible map to v1.0.5
</td></tr>
<tr><td>' . $changed . '</td><td>
updated es6-promise for IE11/Google Mutant to to v4.1.0 (fixing memory leak)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated Leaflet.fullscreen markercluster codebase to v1.0.6 (thx jfirebaugh!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated PUC (plugin update checker) to v4.1 including optimizations & compatibility fixes (thx Yahnis!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated PUS (plugin update server) to v1.2 including optimizations & compatibility fixes (thx Yahnis!)
</td></tr>
<tr><td>' . $changed . '</td><td>
code refactoring for improved structure, re-usability and sustainability (thx Thorsten!)
</td></tr>
<tr><td>' . $changed . '</td><td>
change GPX files mimetype from text/gpx to application/gpx+xml to prevent upload/display issues since WordPress 4.7.1 (thx Thorsten!)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized GPX URL error handling if URL is not found (show warnings on backend & console output on frontend, disallow GPX URL download)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated HTML5 fullscreen and fullscreen-exit icon (thx P.J. Onori, <a href="http://somerandomdude.com/" target="_blank">http://somerandomdude.com/</a>!)
</td></tr>
<tr><td>' . $changed . '</td><td>
multisite/license settings page: show "domain to activate" feature on multisite subdomain installations only
</td></tr>
<tr><td>' . $changed . '</td><td>
XLS(X) importer: increase compatibility by also supporting lat+lon values defined as text and with . or , as separator (thx Marius!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
only dequeue Google Maps API scripts added by other plugins instead of deregistering them if related option is enabled (as this could break dependend scripts & plugins like WP GPX maps)
</td></tr>
<tr><td>' . $fixed . '</td><td>
compatibility check for "Permalink base URL" did not consider active multilingual plugins (thx Jan-Willelm!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
home control button on fullscreen layer maps with clustering was broken (thx Sven!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
validity of export files could be broken by warning "cannot modify header information" if Stiphle based on wp-session is used
</td></tr>
<tr><td>' . $fixed . '</td><td>
paging on list all markers page on backend was broken if search was used (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
prevent duplicate markers when exporting markers from multi-layer-maps to KML, GeoRSS & Wikitude (thx Eric & Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix infinite loading when requesting free trial key on specific browsers (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
XLS export for marker and layer maps was broken if PHP 7.1+ is used
</td></tr>
<tr><td>' . $fixed . '</td><td>
added more specific JS selector for marker filter to prevent markers from being added to the wrong map, if multiple maps are displayed on the same page (thx Tino!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker tooltips were not displayed if popuptext was empty (thx Oleg!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker tooltips were not displayed for markers added directly via shortcode only
</td></tr>
<tr><td>' . $fixed . '</td><td>
incorrect paging on list all markers-page for search results
</td></tr>
<tr><td>' . $fixed . '</td><td>
duplicate layer functions did not duplicate filter settings (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix missing entries in layer filter with marker clustering disabled (thx Ole & Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
markers and layers could not be saved on iOS devices due to a bug in Safari´s datetime-local implementation (thx Natalia!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
?highlightmarker= feature was broken on fullscreen view for multi-layer-maps (thx Ole!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
window width on marker and layer edit pages could not be fully utilized on iOS devices (thx Natalia!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker clusters were always disabled on zoom level 0 even if related setting was empty (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
list of markers sort order was reversed after successful geolocation (thx Chris & Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker edit page: prevent javascript error on markername change if popuptext is empty
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix wrong distances on list of markers when geolocating failed
</td></tr>
<tr><td>' . $fixed . '</td><td>
list of markers was not fully responsive if images larger than 440px in popuptexts were used (thx Georges!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
paging for "list all layer"-search results on backend was broken
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Medium impact: XSS vulnerability for GPX download URL (thx to kiranreddy via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Medium impact: underprivileged backend users could add markers even if permission settings were set not to allow this (not exploitable with default permission settings - thx w31ha0 via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: XSS vulnerabilities on marker & layer edit pages (thx to victemz via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: XSS vulnerabilities on marker & layer import log if malicious input file would be used (thx to kiranreddy via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: missing CSRF protection for free trial registration forms (thx to arall via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: CSRF and XSS vulnerabilities on tools page for change marker and layer ID functions (thx to r4s_team via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: command injection vulnerability in marker & layer export files (thx to kiranreddy via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: added brute-force-login protection for customer area on mapsmarker.com (thx to nooboy via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: improper "URL to GPX track" verification could lead to stored XSS (thx to pahan123 via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: stored XSS vulnerability on tools page only if Webapi is enabled (thx whitesector via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: stored XSS vulnerability for createdby and updatedby fields on backend
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: stored XSS vulnerability for custom default marker icon (thx whitesector via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact: stored XSS vulnerability for QR code image size (only if Google is set as default QR code provider - thx whitesector via hackerone)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri, Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a> and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a> and Phibu Reza, <a href="http://www.dedoho.pw/" target="_blank">http://www.dedoho.pw/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a> and Taisuke Shimamoto
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Lithuanian translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>, Juan Valdes and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a>, Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a> and Tony Lygnersjö - <a href="https://www.dumsnal.se/" target="_blank">https://www.dumsnal.se/</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+, iOS10+ and Firefox 55+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;

if ( (version_compare($lmm_version_old,"3.0.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;clear:both;"><strong>' . sprintf($text_a, '3.0.1') . '</strong> - ' . $text_b . ' 26.03.2017 (<a href="https://www.mapsmarker.com/v3.0.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
permalink compatibility check and base URL option to support unusual WordPress setups and to correct potential configuration errors
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation thanks to Thorsten Gelz
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ and iOS10+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"3.0","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0 !important;"><strong>' . sprintf($text_a, '3.0') . '</strong> - ' . $text_b . ' 25.03.2017 (<a href="https://www.mapsmarker.com/v3.0p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
We are happy to welcome globetrotting engineer Thorsten who joins the <a href="https://www.mapsmarker.com/about-us/" target="_blank">Maps Marker Pro team</a>!
</td></tr>
<tr><td>' . $new . '</td><td>
upgraded leaflet.js ("the engine of Maps Marker Pro") from v0.7.7 to v1.0.3 for higher performance & usability - please see <a href="http://leafletjs.com/2016/09/27/leaflet-1.0-final.html" target="_blank">blog post on leafletjs.com</a> and <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md" target="_blank">full changelog</a> for more details
</td></tr>
<tr><td>' . $new . '</td><td>
Beta (opt-in): significantly improved performance for Google basemaps by using the leaflet plugin <a href="https://gitlab.com/IvanSanchez/Leaflet.GridLayer.GoogleMutant" target="_blank">GoogleMutant</a> (thx Ivan!)
</td></tr>
<tr><td>' . $new . '</td><td>
add pre-loading for map tiles beyond the edge of the visible map to prevent showing background behind tile images when panning a map
</td></tr>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/multilingual" target="_blank">Polylang translation support for multilingual maps</a> (thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
support for tooltips to display the marker name as small text on top of marker icons
</td></tr>
<tr><td>' . $new . '</td><td>
new option to open popups on mouse hover instead of mouse click (disabled by default)
</td></tr>
<tr><td>' . $new . '</td><td>
Pretty permalinks with customizable slug for fullscreen maps and APIs (e.g. ' . MMP_Rewrite::get_base_url() . '<strong>maps</strong>/fullscreen/marker/1/ - thx Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
new functions for MMPAPI: list_markers(), list_layers(), get_layers($layer_ids) - <a href="https://www.mapsmarker.com/mmpapi" target="_blank">full docs</a> (thx a lot Thorsten!)
</td></tr>
<tr><td>' . $new . '</td><td>
new option for disabling WPML/Polylang integration
</td></tr>
<tr><td>' . $new . '</td><td>
enhanced compatibility check for WP Rocket (which can cause maps to break if Maps Marker Pro Javascripts are not excluded)
</td></tr>
<tr><td>' . $new . '</td><td>
add support for PHP APCu caching for sessions used in MMP_Geocoding class
</td></tr>
<tr><td>' . $new . '</td><td>
possibility to sort "list all markers" and "list all layers" tables by location (thx Paul!)
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for "Async Javascript" plugin (thx Adam!)
</td></tr>
<tr><td>' . $new . '</td><td>
AMP support: show placeholder image for map with link to fullscreen view on AMP enabled pages (thx Sebastian!)
</td></tr>
<tr><td>' . $changed . '</td><td>
automatically switched to Algolia Places as default geocoding provider if Mapzen Search without API key is used (API keys get obligatory by April 2017 - free registration is still recommended)
</td></tr>
<tr><td>' . $changed . '</td><td>
~15% performance improvement for API calls by eliminating unneeded WordPress initializations via wp-load.php (thx Thorsten!)
</td></tr>
<tr><td>' . $changed . '</td><td>
create user sessions for geocoding only if MMP_Geocoding class is used
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized SQL for loading markers on (single) layer edit pages (thx Thorsten!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated Leaflet.fullscreen markercluster codebase to v1.0.4 (thx jfirebaugh!)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance on marker & layer edit pages by using HTML5 datetime instead of timepicker.js library+dependencies (thx Thorsten!)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved option "Deregister Google Maps API scripts enqueued by third parties" to prevent re-enqueuing of scripts by also deregistering them
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance for plugin updater (run backend check for access to plugin updates only if an update is available - thx Thorsten!)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed integrated WPML installer to improve backend performance and as issues with NextGen Gallery have been reported
</td></tr>
<tr><td>' . $changed . '</td><td>
increased max chars for filter controlbox from 4000 to 65535 to prevent broken controlboxes (thx Michelle!)
</td></tr>
<tr><td>' . $changed . '</td><td>
always use https for loading bing maps tiles
</td></tr>
<tr><td>' . $changed . '</td><td>
importer: do not show invalid value-warnings for createdon & updatedon rows if audit option is off & related source columns are empty
</td></tr>
<tr><td>' . $changed . '</td><td>
use demo map image instead of Maps Marker Pro logo as placeholder image for maps in RSS feeds
</td></tr>
<tr><td>' . $changed . '</td><td>
changed KML query var name to markername to avoid WP conflicts
</td></tr>
<tr><td>' . $fixed . '</td><td>
WPML performance issues on sites with 1000+ translated map strings (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken settings navigation due to enqueued bootstrap files from 3rd party plugins (thx Bob!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
linked to WPML string translation page on layer edit pages instead to https://mapsmarker.com/multilingual even if WPML was not available (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
add workaround if marker icons are not displayed on backend on marker edit & tools page (thx Ron!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
MMPAPI: fix issue for layer ID selection and bounding box search error message (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
conflict with iThemes Security Pro plugin & htaccess configs preventing direct access to Maps Marker Pro API endpoints (thx David!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
WP Session entries in wp_options table were not deleted via WordPress cron job (thx a lot Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix PHP APC cache detection for importer and MMP_Geocoding class
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker export: search in layers via select2 library was broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP warning after settings were reset to default settings (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
list of markers below layer maps: marker count could be wrong under certain circumstances
</td></tr>
<tr><td>' . $fixed . '</td><td>
divider in zoom control between + and - buttons was missing since v2.9
</td></tr>
<tr><td>' . $fixed . '</td><td>
location search field overlapping GPX media upload overlay caused by too high z-value
</td></tr>
<tr><td>' . $fixed . '</td><td>
sort order for "list all layers" page was broken if sort criteria was selected (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker edit page could be broken due to undefined variable warnings on specific PHP configurations only (thx Nadine!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
directions link was added to popuptext on marker edit page (during preview only) even if setting was disabled
</td></tr>
<tr><td>' . $fixed . '</td><td>
Javascript error when using paging in list of markers below layer maps on layer edit pages
</td></tr>
<tr><td>' . $fixed . '</td><td>
layer center marker on backend was not shown anymore after clusters got loaded
</td></tr>
<tr><td>' . $fixed . '</td><td>
default marker popuptext properties were not considered if triggered via geocoding
</td></tr>
<tr><td>' . $fixed . '</td><td>
distinct marker zoom levels when open popups via list of markers links were not used if clustering was disabled
</td></tr>
<tr><td>' . $fixed . '</td><td>
opening popups via list of markers could break map center if clustering was enabled (thx Damian!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
undefined javascript warning when clicking on marker name in list of markers if clustering was disabled
</td></tr>
<tr><td>' . $fixed . '</td><td>
JSON error when using the WebAPI/search feature (thx Elizabeth!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
sort order for list of markers was not restored after clearing search field (thx Damian & Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
using "change layer ID"-tool could result in wrong layer assignments (thx Patricia & Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
CSS conflicts with selected themes (resulting in borders around Google Maps tile images)
</td></tr>
<tr><td>' . $fixed . '</td><td>
control characters like tabs in marker name can break validity of GeoJSON array for list of markers (thx Stefan!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken list of markers when search term yields no results (thx Damian & Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
license protection was too strict for localhost installations were unlimited testing is allowed (thx Daniel!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fixed geolocation being lost when using list of markers search field (thx Damian & Thorsten!)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact (exploitable for admins only): Reflected XSS vulnerability on license settings page (thx to Deepanker Chawla via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact (exploitable for backend map editors only): Stored XSS vulnerability for location and marker/layer name on "list all layers"/ "list all markers" page (thx to Deepanker Chawla via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact (exploitable for backend map editors only): Reflected XSS vulnerability on marker edit page (thx to Deepanker Chawla via hackerone)
</td></tr>
<tr><td>' . $security_fixed . '</td><td>
Low impact (exploitable for admins only): DOM based XSS vulnerability on settings page (thx to Deepanker Chawla via hackerone)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $changed . '</td><td>
updated <a href="https://translate.mapsmarker.com" target="_blank">https://translate.mapsmarker.com</a> to GlotPress 2.3.1 (mark translations as fuzzy, re-enabled password reset by users, design update & more)
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Marijke Metz - <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>, Patrick Ruers, Fokko van der Leest - <a href="http://wandelenrondroden.nl" target="_blank">http://wandelenrondroden.nl</a> and Hans Temming - <a href="http://www.wonderline.nl" target="_blank">http://www.wonderline.nl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard, <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Galician translation thanks to Fernando Coello, <a href="http://www.indicepublicidad.com" target="_blank">http://www.indicepublicidad.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation thanks to Thorsten Gelz
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a> and Taisuke Shimamoto
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Lithuanian translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Portuguese - Brazil (pt_BR) translation thanks to Fabio Bianchi - <a href="http://www.bibliomaps.com" target="_blank">http://www.bibliomaps.com</a>, Andre Santos - <a href="http://pelaeuropa.com.br" target="_blank">http://pelaeuropa.com.br</a> and Antonio Hammerl
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>, Juan Valdes and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a>, Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a> and Tony Lygnersjö - <a href="https://www.dumsnal.se/" target="_blank">https://www.dumsnal.se/</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ and iOS10+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.9","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;clear:both;"><strong>' . sprintf($text_a, '2.9') . '</strong> - ' . $text_b . ' 25.12.2016 (<a href="https://www.mapsmarker.com/v2.9p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/multilingual" target="_blank">WPML translation support for multilingual maps</a>
</td></tr>
<tr><td>' . $new . '</td><td>
renewal for access to updates and support is now also available for 3 and 5 years - with 10% respectively 15% discount (<a href="https://www.mapsmarker.com/renew" target="_blank">details</a>)
</td></tr>
<tr><td>' . $new . '</td><td>
Javascript Events API for LeafletJS: add getAllMarkers() function
</td></tr>
<tr><td>' . $changed . '</td><td>
automatically trigger geocoding search after fallback geocoding is activated
</td></tr>
<tr><td>' . $changed . '</td><td>
update leaflet-locatecontrol from v0.49 to v0.58 (includes new options & bugfixes, <a href="https://github.com/domoritz/leaflet-locatecontrol/blob/gh-pages/CHANGELOG.md" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
disabled geolocation control by default for new installations only (as this feature will only work with modern browsers if map is accessed via https)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated compatibility check if plugin "WP External Link" is active, which can cause layer maps to break
</td></tr>
<tr><td>' . $changed . '</td><td>
if compatibility option "Deregister Google Maps API scripts enqueued by third parties" is enabled, scripts from maps.googleapis.com/maps/api/js are now dequeued too
</td></tr>
<tr><td>' . $changed . '</td><td>
disabled SQLite & SQLite3 caching method for importer if PHP 5.6.29 is used - will be fixed with PHP 5.6.30 (thx Frederic!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
Latitude and longitude values were swapped when using Mapzen Search for importer or APIs (thx David!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapQuest Geocoding did not deliver correct results for importer and APIs
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom MapQuest Geocoding errors were not shown for importer and APIs
</td></tr>
<tr><td>' . $fixed . '</td><td>
list of markers CSS conflicts with twentyfifteen themes (thx <a href="http://blog.haunschmid.name/" target="_blank">Verena</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
geocoding provider selection for Mapquest Geocoding and Google Geocoding was broken for importer (thx Resi!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
trim bing maps API key to prevent issues caused by spaces on input
</td></tr>
<tr><td>' . $fixed . '</td><td>
opening popups from links in list of markers could result in javascript error on layer maps with clustering enabled
</td></tr>
<tr><td>' . $fixed . '</td><td>
"duplicate layer and assigned markers" button did not duplicate layer controlbox status correctly (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
selecting geocoded address was broken on marker edit pages if direction link was not added to popuptext automatically (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
action bar search for list of markers was broken for multi-layer-map with "display all markers" option enabled (thx jacob!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
importer: links to show the detailed error message for each row did not work properly
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri, Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a> and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Marijke Metz - <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>, Patrick Ruers, Fokko van der Leest - <a href="http://wandelenrondroden.nl" target="_blank">http://wandelenrondroden.nl</a> and Hans Temming - <a href="http://www.wonderline.nl" target="_blank">http://www.wonderline.nl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Greek translation thanks to Philios Sazeides - <a href="http://www.mapdow.com" target="_blank">http://www.mapdow.com</a>, Evangelos Athanasiadis and Vardis Vavoulakis - <a href="http://avakon.com" target="_blank">http://avakon.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Lithuanian translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>, Juan Valdes and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a>, Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a> and Tony Lygnersjö - <a href="https://www.dumsnal.se/" target="_blank">https://www.dumsnal.se/</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ and iOS10+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.8.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;clear:both;"><strong>' . sprintf($text_a, '2.8.1') . '</strong> - ' . $text_b . ' 04.11.2016 (<a href="https://www.mapsmarker.com/v2.8.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/mapzen-partnership" target="_blank">blog post about our new partnership with Mapzen - the new default geocoding provider for Maps Marker Pro</a>
</td></tr>
<tr><td>' . $new . '</td><td>
new compatibility setting "maxZoom compatibility mode" for themes conflicts where markers on (Google) maps are not displayed properly
</td></tr>
<tr><td>' . $new . '</td><td>
https is now also required on iOS/Safari 10+ for geolocation to work properly (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
do not clear (existing) geocoding search results if (no more additional) results are found anymore
</td></tr>
<tr><td>' . $changed . '</td><td>
show 10 instead of 5 geocoding search results for Mapzen, Algolia and Photon@MapsMarker
</td></tr>
<tr><td>' . $changed . '</td><td>
do not switch to alternative geocoding provider if Google Geocoding returns no results
</td></tr>
<tr><td>' . $changed . '</td><td>
removed MemCached support for importer and Stiphle rate limiting due to compatibility issues reported
</td></tr>
<tr><td>' . $changed . '</td><td>
auto-select marker/layername, mapwidth, mapheight & zoom input values on backend on input focus
</td></tr>
<tr><td>' . $changed . '</td><td>
show detailed error message if MapQuest Geocoding failed
</td></tr>
<tr><td>' . $fixed . '</td><td>
bulk actions on "list all markers" and "list all layers" page could be broken since v2.8 (thx reeser!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"add marker link" for layer center icon was broken after geocoding search result was selected on layer pages
</td></tr>
<tr><td>' . $fixed . '</td><td>
fatal error on activation if another plugin also utilizes WP_Session_Utils-class (thx Jan-Willem!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP warnings if Photon@MapsMarker for APIs or importer is used and an empty address is given
</td></tr>
<tr><td>' . $fixed . '</td><td>
unneeded checked="checked" output on import pages on backend
</td></tr>
<tr><td>' . $fixed . '</td><td>
openpopup-links in list of markers after search did not work since v2.8 (thx Takeo!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
loading indicator for geocoding search was not shown on marker edit pages
</td></tr>
<tr><td>' . $fixed . '</td><td>
Maps Marker Pro could not be activated on PHP 5.2 installations (thx Clive!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ & iOS/Safari 10+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.8","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;clear:both;"><strong>' . sprintf($text_a, '2.8') . '</strong> - ' . $text_b . ' 28.10.2016 (<a href="https://www.mapsmarker.com/v2.8p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
support for geocoding providers Mapzen Search, Algolia Places, MapQuest Geocoding, Photon@MapsMarker
</td></tr>
<tr><td>' . $new . '</td><td>
add support OpenStreetMap variants (Mapnik, Black&White, DE, France, HOT)
</td></tr>
<tr><td>' . $new . '</td><td>
add support for <a href="http://maps.stamen.com/" target="_blank">Stamen</a> basemaps terrain & toner
</td></tr>
<tr><td>' . $new . '</td><td>
add support for MapQuest (Hybrid) basemap
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for "Geo Redirect" plugin (thx Vladislav!)
</td></tr>
<tr><td>' . $new . '</td><td>
add .htaccess file to plugin folder to explicitly allow direct access to specific PHP plugin files (thx Nikos!)
</td></tr>
<tr><td>' . $new . '</td><td>
new Leaflet.markercluster option "animate" for smoothly splitting/merging cluster children (enabled by default)
</td></tr>
<tr><td>' . $new. '</td><td>
add info texts about marker/layer concept to better assist new users
</td></tr>
<tr><td>' . $new. '</td><td>
add loading indicators on license settings page to show progress of license validation
</td></tr>
<tr><td>' . $new. '</td><td>
new filter mmp_before_setview which allowing to utilize the map load-event (thx Jose!)
</td></tr>
<tr><td>' . $new. '</td><td>
support for <a href="http://korona.geog.uni-heidelberg.de/" target="_blank">OpenMapSurfer Bounds</a> as default custom basemap 1 (enabled for new installs only)
</td></tr>
<tr><td>' . $new. '</td><td>
support for <a href="https://opentopomap.org/" target="_blank">OpenTopoMap</a> as default custom basemap 2 (enabled for new installs only)
</td></tr>
<tr><td>' . $new. '</td><td>
support for <a href="http://openstreetmap.se/" target="_blank">Hydda</a> as default custom basemap 3 (enabled for new installs only)
</td></tr>
<tr><td>' . $new. '</td><td>
support for new default custom overlays <a href="http://waymarkedtrails.org/" target="_blank">Waymarked Trails</a> and <a href="http://openweathermap.org/" target="_blank">OpenWeatherMap</a> (enabled for new installs only)
</td></tr>
<tr><td>' . $new. '</td><td>
add access to markers in MMP JS API (thx Jose!)
</td></tr>
<tr><td>' . $new. '</td><td>
new MMP JS API function to open a popup on a layer map (thx Rob!)
</td></tr>
<tr><td>' . $new. '</td><td>
add pagination for "list all layers" page on backend
</td></tr>
<tr><td>' . $changed . '</td><td>
increase maxNativeZoom level for OpenStreetMap from 18 to 19 for higher details
</td></tr>
<tr><td>' . $changed . '</td><td>
option "Google Maps JavaScript API" has been reset due to compatibility reasons & disabled for new installations due to mandatory API key
</td></tr>
<tr><td>' . $changed . '</td><td>
removed compatibility fallback from https to http for tile images & API requests if locale zh (Chinese) is used
</td></tr>
<tr><td>' . $changed . '</td><td>
reorganized settings page for better usability
</td></tr>
<tr><td>' . $changed . '</td><td>
jump to top of list of markers below layer maps after pagination is used (thx Mark!)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance of marker icons loading on marker edit & tools page (by eliminating extra http requests by using base64 image encoding instead)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance on backend for OpenStreetMap-based maps by support for conditional & deferred Google Maps API loading
</td></tr>
<tr><td>' . $changed . '</td><td>
trim Mapbox custom basemap parameters to prevent broken URLs
</td></tr>
<tr><td>' . $changed . '</td><td>
updated Leaflet.markercluster codebase to v0.5.0 (thx danzel!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery timepicker addon from v1.6.1 to v1.6.3 (bugfix release, <a href="https://github.com/trentrichardson/jQuery-Timepicker-Addon/commits/master" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated minimap addon from v3.3.0 to v3.4.0 (<a href="https://github.com/Norkart/Leaflet-MiniMap/releases" target="_blank">release notes</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
reorder menu items and collapse links to advanced features by default for better focus (thx Wieland from <a href="http://user-experience.wien/" target="_blank">http://user-experience.wien/</a>!)
</td></tr>
<tr><td>' . $changed . '</td><td>
hide advanced layer edit functions and make them visible on click only (to better assist new users)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated default error tile image which provides possible explanations for loading issues and also suggests solutions
</td></tr>
<tr><td>' . $changed . '</td><td>
replaced built-in-support for OGD Vienna maps with support for basemap.at (covering whole Austria, disabled by default)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated setting link to new "Google Styled Maps Wizard"
</td></tr>
<tr><td>' . $changed . '</td><td>
disable update button on marker edit page as long as TinyMCE HTML editor is not fully loaded to prevent issues with popuptext not saving correctly (thx JunJie!)
</td></tr>
<tr><td>' . $changed . '</td><td>
increase search process timeout from 0.5 to 1sec to better support double byte characters (thx Takeo!)
</td></tr>
<tr><td>' . $changed . '</td><td>
add home control button on backend only when editing of existing marker or layer maps (thx Thorsten!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapQuest basemaps were broken since July 11th 2016 (automatic fallback to OpenStreetMap for existing maps if mandatory API key is not set)
</td></tr>
<tr><td>' . $fixed . '</td><td>
unresponsive map when too much markers were loaded and marker icon or marker name in list of markers was clicked (thx Daryn!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom post types were not found for "used in content" feature, showing where a Maps Marker Pro shortcode is used (thx Brian!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
markers from layer included in mlm with filter status "no" are not loaded on frontend (thx Carles!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
sort order for ID in filter controlbox was by ID text and not ID number (thx Brian!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
bing attribution could disappear when map getBounds() return out range values
</td></tr>
<tr><td>' . $fixed . '</td><td>
settings page could be visible to non-admins (changes could not be made though)
</td></tr>
<tr><td>' . $fixed . '</td><td>
large icons could distort "list all markers"-page (thx Hockey!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
add fix for Google.asyncWait which can cause issues on mobile devices (thx <a href="http://codemonkeyseedo.blogspot.com/" target="_blank">nmccready</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken initialization of click events on filters (thx Patrick!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
if option "use layer zoom level for all markers" was set, popups in clusters were not opened by using links in list of markers
</td></tr>
<tr><td>' . $fixed . '</td><td>
"Too few arguments" PHP warning for list of markers sort order (thx Martin!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP error log entries "Undefined variable: mapname_js" if invalid shortcode was used
</td></tr>
<tr><td>' . $fixed . '</td><td>
i18n/translation issue on marker edit page (thx Hans!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom marker icon was not used as fallback if importer errors occured
</td></tr>
<tr><td>' . $fixed . '</td><td>
errorTile-images option for custom basemap 2&3 was not considered on marker&layer edit pages
</td></tr>
<tr><td>' . $fixed . '</td><td>
action bar for list of markers was also shown on empty layer maps even if list of markers option was unchecked
</td></tr>
<tr><td>' . $fixed . '</td><td>
unsaved-warning was shown on layer edit pages even if no changes were made
</td></tr>
<tr><td>' . $fixed . '</td><td>
vertical scrolling on marker and layer edit pages was broken on mobiles
</td></tr>
<tr><td>' . $fixed . '</td><td>
map was partially broken after exiting HTML5 fullscreen view with Google Chrome (thx Maj-Britt!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"add new layer" link was not visible below layer selection list when creating new marker
</td></tr>
<tr><td>' . $fixed . '</td><td>
OpenRouteService.org directions integration was partially broken (no start point was set due to changed layer IDs - thx Marco!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
autofocus on marker/layer name on backend did not work in Google Chrome
</td></tr>
<tr><td>' . $fixed . '</td><td>
compatibility check issue with W3 Total Cache Plugin v0.9.5 only (see <a href="https://www.mapsmarker.com/w3tc-hotfix" target="_blank">mapsmarker.com/w3tc-hotfix</a> for background info)
</td></tr>
<tr><td>' . $fixed . '</td><td>
layer maps could be broken if sort by distance in list of markers is set by default (depending on PHP error log level)
</td></tr>
<tr><td>' . $fixed . '</td><td>
new layer defaults for panel, listmarkers and clustering settings were not saved if unchecked by default (thx Thorsten!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $changed . '</td><td>
changed rewards for translators: get a free professional license key worth €249 for <=80% instead of <=50% completeness
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri, Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a> and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Marijke Metz - <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>, Patrick Ruers, Fokko van der Leest - <a href="http://wandelenrondroden.nl" target="_blank">http://wandelenrondroden.nl</a> and Hans Temming - <a href="http://www.wonderline.nl" target="_blank">http://www.wonderline.nl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard, <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Hungarian translation thanks to István Pintér, <a href="http://www.logicit.hu" target="_blank">http://www.logicit.hu</a> and Csaba Orban, <a href="http://www.foto-dvd.hu" target="_blank">http://www.foto-dvd.hu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a> and Taisuke Shimamoto
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Lithuanian translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>, Juan Valdes and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a>, Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a> and Tony Lygnersjö - <a href="https://www.dumsnal.se/" target="_blank">https://www.dumsnal.se/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Ukrainian translation thanks to Andrexj, <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>, Sergey Zhitnitsky and Mykhailo, <a href="http://imgsplanet.com" target="_blank">http://imgsplanet.com</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.7.3","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.7.3') . '</strong> - ' . $text_b . ' 26.06.2016 (<a href="https://www.mapsmarker.com/v2.7.3p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
compatibility check and option to deregister Google Maps API scripts added by 3rd party themes or plugins
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for "Page Builder by SiteOrigin" & "Yoast SEO" where a special settings combination is causing maps to break
</td></tr>
<tr><td>' . $new . '</td><td>
list of markers-searchbox now also supports enter to start a search (thx Jeff!)
</td></tr>
<tr><td>' . $new . '</td><td>
show loading indicator when using search in list of markers (thx Jeff!)
</td></tr>
<tr><td>' . $changed . '</td><td>
enhanced Google Maps API key support which is mandatory since June 22nd 2016
</td></tr>
<tr><td>' . $fixed . '</td><td>
compatibility check for Autoptimize plugin was broken as plugin was updated
</td></tr>
<tr><td>' . $fixed . '</td><td>
"improve map" and "ToS" links on layer maps with Google as basemaps were not clickable
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.7.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.7.2') . '</strong> - ' . $text_b . ' 18.06.2016 (<a href="https://www.mapsmarker.com/v2.7.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
new bulk action to delete assigned markers on layer edit page (thx Chris!)
</td></tr>
<tr><td>' . $new . '</td><td>
add compatibility setting for maps to load correctly in proprietary tab solutions and hidden divs
</td></tr>
<tr><td>' . $new . '</td><td>
show error message if users tries to assign a marker directly to a multi-layer-map
</td></tr>
<tr><td>' . $changed . '</td><td>
"change layer ID" feature on tools page now also updates layer ID used in multi-layer-maps (thx Coen!)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed icon width option for widgets (as icon got distorted)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated minimum recommended PHP version for built-in PHP check to 5.6 - supporting <a href="http://www.wpupdatephp.com" target="_blank">wpupdatephp.com</a>
</td></tr>
<tr><td>' . $fixed . '</td><td>
compatibility for ContactForm7 forms in popuptexts on layer maps
</td></tr>
<tr><td>' . $fixed . '</td><td>
upscaling for MapQuest OSM basemaps to zoom level 18+ was broken (thx Michael!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
layer maps could be broken if a special settings combination for list of markers was used (thx Lynn!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
recent marker widget: show separator lines-, show popuptext- and show icons-options did not work as designed (thx Harald!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
recent marker widget: option to set color value for separator line was broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
control characters like tabs in marker name could break layer maps with enabled list of markers (thx Peter!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
REST API error "The specified user already has API keys or the specified user does not exist."
</td></tr>
<tr><td>' . $fixed . '</td><td>
compatibility check for WP external links plugin did not work anymore since v2.0 (thx Oleg!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
trial period independent-access to frontend maps on localhost installations was broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
layer(s) assignment-dropdown was not ordered by layer ID on marker edit- and tools-page (thx Coen!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
javascript undefined warning for list of markers if nonce has changed
</td></tr>
<tr><td>' . $fixed . '</td><td>
"change layer ID" feature on tools page did not update assigned markers since v2.4 (thx Coen!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"list of markers" table on layer edit pages for multi-layer-map with all markers assigned was not displayed correctly
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Lithuanian translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.7.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.7.1') . '</strong> - ' . $text_b . ' 21.05.2015 (<a href="https://www.mapsmarker.com/v2.7.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/wpruby-com" target="_blank">Introducing WPRuby: our official partner for custom Maps Marker Pro development</a>
</td></tr>
<tr><td>' . $new . '</td><td>
add CSS class <i>mlm-filters-icon</i> to filter controlbox to allow better & easier custom styling (thx Paige!)
</td></tr>
<tr><td>' . $changed . '</td><td>
remove default HTML5-URL verification from input field for filter icons to also support URLs starting with // (thx Chris!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
maps could be broken on mobile devices if maximum zoom level was used (thx Giampiero!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
uploaded marker icons with custom sizes were not resized to default size in list of markers (thx Patrick!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"duplicate layer and assigned markers" and "delete layer and assigned markers" for single layer maps was broken with v2.7
</td></tr>
<tr><td>' . $fixed . '</td><td>
GeoJSON-output for layers was broken if GET parameter full was set to yes
</td></tr>
<tr><td>' . $fixed . '</td><td>
confirm-dialogs on backend were partly broken if Italian translation was used (thx Giampiero!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
layer row on "list all layers"-page was not hidden if layer was deleted
</td></tr>
<tr><td>' . $fixed . '</td><td>
assigned-marker-table at layer edit page was not hidden on "layer duplicate only" and "add new layer" actions
</td></tr>
<tr><td>' . $fixed . '</td><td>
backend header navigation was not shown if markers were duplicated from "list all markers"-page
</td></tr>
<tr><td>' . $fixed . '</td><td>
WordPress default audio player ([audio]-shortcode) was not visible in popuptexts on layer maps (thx Jochen!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"used in content" row on marker&layer edit pages was not hidden when duplicating an existing marker/layer
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri, Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a> and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Greek translation thanks to Philios Sazeides - <a href="http://www.mapdow.com" target="_blank">http://www.mapdow.com</a>, Evangelos Athanasiadis and Vardis Vavoulakis - <a href="http://avakon.com" target="_blank">http://avakon.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Lithuanian translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>, Juan Valdes and Marta Espinalt, <a href="http://www.martika.es" target="_blank">http://www.martika.es</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.7","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.7') . '</strong> - ' . $text_b . ' 30.04.2016 (<a href="https://www.mapsmarker.com/v2.7p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
support for multi-layer-map filtering on frontend (yeah!)
</td></tr>
<tr><td>' . $new . '</td><td>
support for paging and search in the list of markers below layer maps
</td></tr>
<tr><td>' . $new . '</td><td>
support for sorting list of markers based on current geolocation
</td></tr>
<tr><td>' . $new . '</td><td>
RESTful API allowing you to access some of the common core functionalities
</td></tr>
<tr><td>' . $new . '</td><td>
Javascript Events API for LeafletJS to to attach events handlers to markers and layers
</td></tr>
<tr><td>' . $new . '</td><td>
enhanced MMPAPI to also support delete_markers parameter for delete_layer and delete_layers function
</td></tr>
<tr><td>' . $new . '</td><td>
"resize map link"-button allowing you to restore the map to its initial state
</td></tr>
<tr><td>' . $new . '</td><td>
new tool: marker validity check for layer assignements to verify if markers are assigned to layers that do not exist (anymore)
</td></tr>
<tr><td>' . $new . '</td><td>
AJAX support for deleting a layer from "list all layers"-page (no reload needed anymore)
</td></tr>
<tr><td>' . $new . '</td><td>
new "tap" & "tapTolerance" maps interaction options (enables mobile hacks for supporting instant taps) - thx Mauricio!
</td></tr>
<tr><td>' . $new . '</td><td>
new "bounceAtZoomLimits" maps interaction option (to disable bouncing back when pinch-zooming beyond min/max zoom level)
</td></tr>
<tr><td>' . $new . '</td><td>
CSS class lmm-icon-download-gpx for download-gpx icon (to prevent conflicts with stylesheets for mobile devices)
</td></tr>
<tr><td>' . $new . '</td><td>
confirmation prompts before performing bulk delete actions on "list all markers"- and "list all layer"-pages
</td></tr>
<tr><td>' . $new . '</td><td>
new interaction option to enable scrollWheelZoom for fullscreen maps only (thx iamjwk!)
</td></tr>
<tr><td>' . $new . '</td><td>
support for highlighting markers also on fullscreen layer maps by using the URL parameter ?highlightmarker=...
</td></tr>
<tr><td>' . $new . '</td><td>
option to center maps on popup centers instead of markers when opening popups (hopefully fixing autopan issues with markers at map borders)
</td></tr>
<tr><td>' . $new . '</td><td>
use marker zoom level for centering markers on layer maps by clicking on list of markers-links (can be changed to layer zoom in settings)
</td></tr>
<tr><td>' . $new . '</td><td>
add paging support on layer edit pages for the table below the editor (listing all assigned markers)
</td></tr>
<tr><td>' . $new . '</td><td>
show error instead of failing silently if Bing layers return with an error
</td></tr>
<tr><td>' . $new . '</td><td>
show edit-marker-link as image in list of markers for each marker on backend and frontend
</td></tr>
<tr><td>' . $changed . '</td><td>
improved Google maps performance by reducing laggy panning (thx rcknr!)
</td></tr>
<tr><td>' . $changed . '</td><td>
shortcode parameter highlightmarker now also centers layer maps on marker coordinates (thx Carlos!)
</td></tr>
<tr><td>' . $changed . '</td><td>
replaced GPX proxy transient with nonce to better support multiple consecutive map edits without timeouts
</td></tr>
<tr><td>' . $changed . '</td><td>
replaced add_object_page() with add_menu_page() as former will be depreciated with WordPress 4.5
</td></tr>
<tr><td>' . $changed . '</td><td>
AJAX search on "list of markers" page on backend now also shows if no matches have been found
</td></tr>
<tr><td>' . $changed . '</td><td>
layer import: show next layer ID which would be used and helptext for copying markers and layers from one site to another (thx Oliver!)
</td></tr>
<tr><td>' . $changed . '</td><td>
bulk action for duplicating layer+assigned markers now displays warning if multi-layer-map is selected (thx Angelo!)
</td></tr>
<tr><td>' . $changed . '</td><td>
security hardening for import/export & gpx proxy by adding additional user permission checks (thx Giuseppe!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery timepicker addon from v1.5.5 to v1.6.1 (bugfix release, <a href="https://github.com/trentrichardson/jQuery-Timepicker-Addon/commits/master" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
proper semantic usage of admin notices (error/warning/success/info) and consistent display above header table on all plugin pages
</td></tr>
<tr><td>' . $changed . '</td><td>
"open popup"-links in the list of markers below layer maps now also change URL for better shareability (by adding ?highlightmarker=... - thx Peter!)
</td></tr>
<tr><td>' . $changed . '</td><td>
better performance on marker edit pages due to optimized loading of custom TinyMCE CSS stylesheets
</td></tr>
<tr><td>' . $changed . '</td><td>
remove Google Adsense integration feature as javascript adsense library has been retired (thx Niall!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated leaflet locate control from v0.4.5 to v0.4.9 (bugfix release, <a href="https://github.com/domoritz/leaflet-locatecontrol/commits" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated bing maps codebase (<a href="https://github.com/shramov/leaflet-plugins/commits/master/layer/tile/Bing.js" target="_blank">changelog</a>, thx brunob!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
permission settings: backend menu was not visible for contributors (capability: edit_posts) even if correct permissions were set
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapsMarker Web API: layer assignments for markers were not saved correctly (thx Janne!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
&lt;/div&gt; was not set if GPX panel was disabled, resulting in issues on certain themes (thx Dirk!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
bottom admin notice after layer/marker updates was shown on top of edit table since WordPress 4.4
</td></tr>
<tr><td>' . $fixed . '</td><td>
latest news from mapsmarker.com for admin dashboard widget was broken since Yahoo Pipes! was discontinued
</td></tr>
<tr><td>' . $fixed . '</td><td>
depreciated notice in error logs if PHP 7+ is used (thx Chris!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
workaround for maps in WooCommerce tabs was broken since last WooCommerce tabs plugin update (thx Richard!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
QR code links in list of markers below layer maps were broken if Google was set as QR code provider (thx Niall!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
background color, margin and padding for basemap controlbox was overridden by some themes
</td></tr>
<tr><td>' . $fixed . '</td><td>
issues with other plugins using an older version of the same <a href="https://github.com/YahnisElsts/plugin-update-checker" targert="_blank">plugin update checker library</a> (thx Yahnis!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
map view was not centered on marker if GET-parameter ?highlightmarker=... was used
</td></tr>
<tr><td>' . $fixed . '</td><td>
license validation could be broken if HHVM was used (thx Alex!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"used in content" row on marker&layer edit pages was not hidden when creating a new marker/layer
</td></tr>
<tr><td>' . $fixed . '</td><td>
occasional incomplete loading of map tiles for minimap on mobile devices
</td></tr>
<tr><td>' . $fixed . '</td><td>
minimap toogle icon being distorted due to CSS conflicts with selected themes
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP warnings when using importer with enabled test mode and disabled geolocation
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Danish translation thanks to Mads Dyrmann Larsen and Peter Erfurt, <a href="http://24-7news.dk" target="_blank">http://24-7news.dk</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Marijke Metz - <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>, Patrick Ruers, Fokko van der Leest - <a href="http://wandelenrondroden.nl" target="_blank">http://wandelenrondroden.nl</a> and Hans Temming - <a href="http://www.wonderline.nl" target="_blank">http://www.wonderline.nl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Finnish (fi_FI) translation thanks to Jessi Bj&ouml;rk - <a href="https://twitter.com/jessibjork" target="_blank">@jessibjork</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Galician translation thanks to Fernando Coello, <a href="http://www.indicepublicidad.com" target="_blank">http://www.indicepublicidad.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Greek (el) translation thanks to Philios Sazeides - <a href="http://www.mapdow.com" target="_blank">http://www.mapdow.com</a>, Evangelos Athanasiadis and Vardis Vavoulakis - <a href="http://avakon.com" target="_blank">http://avakon.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Hungarian translation thanks to István Pintér, <a href="http://www.logicit.hu" target="_blank">http://www.logicit.hu</a> and Csaba Orban, <a href="http://www.foto-dvd.hu" target="_blank">http://www.foto-dvd.hu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Malawy translation thanks to Mohd Zulkifli, <a href="http://www.caridestinasi.com/" target="_blank">http://www.caridestinasi.com/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank"></a>, Robert Pawlak and Daniel - <a href="http://mojelodzkie.pl" target="_blank">http://mojelodzkie.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Portuguese - Brazil (pt_BR) translation thanks to Fabio Bianchi - <a href="http://www.bibliomaps.com" target="_blank">http://www.bibliomaps.com</a>, Andre Santos - <a href="http://pelaeuropa.com.br" target="_blank">http://pelaeuropa.com.br</a> and Antonio Hammerl
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a>, Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a> and Tony Lygnersjö - <a href="https://www.dumsnal.se/" target="_blank">https://www.dumsnal.se/</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.6.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.6.2') . '</strong> - ' . $text_b . ' 06.12.2015 (<a href="https://www.mapsmarker.com/v2.6.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
added option to disable deferred Google Maps API loading as some theme compatibility issues were reported
</td></tr>
<tr><td>' . $changed . '</td><td>
always load tiles for OpenStreetMap, MapQuest, Mapbox and OGD Vienna via https (except if Chinese locale is set as performance issues with https in China have been reported)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker clustering on maps using Google basemaps by default was broken on certain themes since v2.6.1
</td></tr>
<tr><td>' . $fixed . '</td><td>
GeoJSON-output for markers with full=yes was broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
restored old headings order (h2+h3+h4) on Settings page as admin notices were not shown correctly on top of page
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.6.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.6.1') . '</strong> - ' . $text_b . ' 29.11.2015 (<a href="https://www.mapsmarker.com/v2.6.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
significantly decreased loadtimes for OpenStreetMap-based maps by supporting conditional & deferred Google Maps API loading (~370kb(!) less uncompressed data transmission)
</td></tr>
<tr><td>' . $changed . '</td><td>
URL hashes introduced with v2.6 are now disabled by default for new updates
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized URL hashes (Prevent from registering events twice if calling startListening twice)
</td></tr>
<tr><td>' . $changed . '</td><td>
Tools page/move markers-bulk action: multi-layer-maps are now excluded as markers cannot be assigned directly to multi-layer-maps (thx Andres!)
</td></tr>
<tr><td>' . $changed . '</td><td>
now loading Google Maps API by default via https and only via http for WordPress installations with Chinese locale (as performance issues with https in China have been reported)
</td></tr>
<tr><td>' . $changed . '</td><td>
sort "list of markers" for multi-layer-map selection on layer edit-pages by ID ascending
</td></tr>
<tr><td>' . $fixed . '</td><td>
"used in content" warnings if special widget configurations were used
</td></tr>
<tr><td>' . $fixed . '</td><td>
occassionally wrong "used in content"-results linking to contents where shortcode is not used (thx Hans-Georg!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
responsive tables were not shown correctly on some devices (column with relative instead of absolute widths)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker edit link on fullscreen maps linked to layer edit page instead of marker edit page
</td></tr>
<tr><td>' . $fixed . '</td><td>
layer maps could get broken recently if other plugins or themes also embedded the Google Maps API
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker count for multi-layer-maps on "list all layers"-page was broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
duplicate marker button on marker edit page did not duplicate assigned layer(s) but unassigned the marker from any layer
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix for "WPBakery Visual Composer" plugin v4.7+ introduced with v2.6 did not work correctly on all sites
</td></tr>
<tr><td>' . $fixed . '</td><td>
bulk actions for layer maps did not delete or re-assign markers from sub layers
</td></tr>
<tr><td>' . $fixed . '</td><td>
Web API: assigned markers are not deleted or re-assigned when using delete action for layer maps
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.6","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.6') . '</strong> - ' . $text_b . ' 21.11.2015 (<a href="https://www.mapsmarker.com/v2.6p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
updated Leaflet from v0.7.5 to v0.7.7 (bugfix release - including a fix for obscure iOS issue where tiles would sometimes disappear, <a href="https://github.com/Leaflet/Leaflet/releases/tag/v0.7.7" target="_blank">release notes</a>)
</td></tr>
<tr><td>' . $new . '</td><td>
improved backend usability by listing all contents (posts, pages, CPTs, widgets) where each shortcode is used
</td></tr>
<tr><td>' . $new . '</td><td>
added option to sort list of markers below layer maps by distance from layer center
</td></tr>
<tr><td>' . $new . '</td><td>
XML sitemaps integration: improved local SEO value by automatically adding links to KML maps to your XML sitemaps (if plugin "Google XML Sitemaps" is active)
</td></tr>
<tr><td>' . $new . '</td><td>
highlight a marker on a layer map by opening its popup via shortcode attribute [mapsmarker layer="1" highlightmarker="2"] or by adding ?highlightmarker=2 to the URL where the map is embedded
</td></tr>
<tr><td>' . $new . '</td><td>
added support for URL hashes to web pages with maps, allowing users to easily link to specific map views. Example: https://domain/link-to-map/#11/48.2073/16.3792
</td></tr>
<tr><td>' . $new . '</td><td>
added support for responsive tables on "list all markers" and "list all layer" pages
</td></tr>
<tr><td>' . $new . '</td><td>
added support for dynamic clustering preview for multi-layer-maps on backend
</td></tr>
<tr><td>' . $new . '</td><td>
added option to hide default GPX start and end icons (thx Rich!)
</td></tr>
<tr><td>' . $new . '</td><td>
added automatic check if custom plugin directory name is used (which would break layer maps)
</td></tr>
<tr><td>' . $new . '</td><td>
added new marker clustering options to style spiderLeg polylines
</td></tr>
<tr><td>' . $new . '</td><td>
added new CSS class lmm-listmarkers-popuptext-only to allow better styling of "list of markers" entries
</td></tr>
<tr><td>' . $changed . '</td><td>
tiles for default custom basemap2 "<a href="http://maps.stamen.com/watercolor/" target="_blank">Stamen Watercolor</a>" are now delivered via https to prevent mixed content warnings (thx Alan &amp; Duncan!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated minimum recommended PHP version for built-in PHP check to 5.5 - supporting <a href="http://www.wpupdatephp.com" target="_blank">wpupdatephp.com</a>
</td></tr>
<tr><td>' . $changed . '</td><td>
updated leaflet locate control from v0.4.0 to v0.4.5 (bugfix release, <a href="https://github.com/domoritz/leaflet-locatecontrol/commits" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery timepicker addon from v1.5.0 to v1.5.5 (bugfix release, <a href="https://github.com/trentrichardson/jQuery-Timepicker-Addon/commits/master" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated Select2 addon from v3.5.2 to v3.5.4 (bugfix release, <a href="https://github.com/select2/select2/releases/tag/3.5.4" target="_blank">release notes</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated minimap addon from v2.1 to v3.0 (<a href="https://github.com/Norkart/Leaflet-MiniMap/releases" target="_blank">release notes</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated plugin update checker from v2.0 to v2.2 (bugfix release, <a href="https://github.com/YahnisElsts/plugin-update-checker/releases" target="_blank">release notes</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved license key check on license settings page (check if license key starts with MapsMarker with immediate feedback)
</td></tr>
<tr><td>' . $changed . '</td><td>
remove &lt;br/&gt; before address section in list of markers to enable better optional custom padding via CSS class lmm-listmarkers-hr overrides
</td></tr>
<tr><td>' . $changed . '</td><td>
improved GPX file validity check (thx Andi!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated markercluster codebase (<a href="https://github.com/Leaflet/Leaflet.markercluster/commits/master" target="_blank">using build from 27/03/2015</a> - thx danzel!)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed support for directions provider map.project-osrm.org as requested by project owners
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized headings hierarchy in the admin screens to better support screen readers
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix MMPAPI class issue (marker assignments to multiple layers were not saved correctly)
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapsMarker Web API: icon was reset to default value on updates if icon parameter was not set (thx Sohin!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
incomplete map tiles display after device orientation change on mobile devices (thx Duncan!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
search on settings page did not display "no matches found" if there were no search results
</td></tr>
<tr><td>' . $fixed . '</td><td>
maps in tabs or accordions created with "WPBakery Visual Composer" plugin were broken since v4.7 (thx Raitis!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
directions provider openrouteservice.org changed URL schema, this resulted in broken directions links
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $new . '</td><td>
Malawy translation thanks to Mohd Zulkifli, <a href="http://www.caridestinasi.com/" target="_blank">http://www.caridestinasi.com/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard, <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Greek (el) translation thanks to Philios Sazeides - <a href="http://www.mapdow.com" target="_blank">http://www.mapdow.com</a>, Evangelos Athanasiadis and Vardis Vavoulakis - <a href="http://avakon.com" target="_blank">http://avakon.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.5","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.5') . '</strong> - ' . $text_b . ' 12.09.2015 (<a href="https://www.mapsmarker.com/v2.5p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
new API: <a href="https://www.mapsmarker.com/mmpapi" target="_blank">MMPAPI-class</a> which allows you to easily develop add-ons for example
</td></tr>
<tr><td>' . $new . '</td><td>
AJAX support (no reloads needed) for layer edits and list of markers page
</td></tr>
<tr><td>' . $new . '</td><td>
update to Leaflet v0.7.5 (<a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#075-september-2-2015" target="_blank">full changelog</a>)
</td></tr>
<tr><td>' . $new . '</td><td>
get to know the team behind Maps Marker Pro on our updated <a href="https://www.mapsmarker.com/about-us" target="_blank">About us-page</a>
</td></tr>
<tr><td>' . $new . '</td><td>
new permission settings: configure capability needed to view other markers and layers
</td></tr>
<tr><td>' . $new . '</td><td>
"edit map"-link on frontend based on user-permissions for better maintainability (thx David!)
</td></tr>
<tr><td>' . $new . '</td><td>
"add new marker to this layer" button & link enhancements: now using current layer center for new marker position (thx Angelo from <a href="http://www.wocmultimedia.com/" target="_blank">wocmultimedia.com</a>!)
</td></tr>
<tr><td>' . $new . '</td><td>
dynamic preview of all markers from assigned layer(s) on marker edit pages (thx Angelo from <a href="http://www.wocmultimedia.com/" target="_blank">wocmultimedia.com</a>!)
</td></tr>
<tr><td>' . $new . '</td><td>
dynamic preview of markers from checked multi-layer-map layer(s) on layer edit pages (thx Angelo from <a href="http://www.wocmultimedia.com/" target="_blank">wocmultimedia.com</a>!)
</td></tr>
<tr><td>' . $new . '</td><td>
option to duplicate layer AND assigned markers (for single layers and for layer bulk actions) - thx Angelo from <a href="http://www.wocmultimedia.com/" target="_blank">wocmultimedia.com</a>!
</td></tr>
<tr><td>' . $new . '</td><td>
option to disable map dragging on touch devices only (thx Peter!)
</td></tr>
<tr><td>' . $new . '</td><td>
import/export: add option to export markers and layers as OpenDocument Spreadsheet (.ods)
</td></tr>
<tr><td>' . $new . '</td><td>
added "import mode" option for bulk additions/updates to import/export-feature for better usability
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for plugin "WP Deferred JavaScripts" which can cause maps to break
</td></tr>
<tr><td>' . $new . '</td><td>
add option to order marker in list of markers below layer maps by address (thx Anton!)
</td></tr>
<tr><td>' . $new . '</td><td>
added new CSS class "lmm-map" to map divs to allow better custom styling (thx Marco!)
</td></tr>
<tr><td>' . $new . '</td><td>
automatic check: disallow conversion of layer maps into multi-layer-maps if markers have already been directly assigned
</td></tr>
<tr><td>' . $changed . '</td><td>
updated PHPExcel to v1.8.1 (<a href="https://github.com/PHPOffice/PHPExcel/blob/1.8/changelog.txt" target="_blank">changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
extended compatibility for maps in bootstrap-tabs (added support for <i>.tabbed-area a</i> and <i>.nav-tabs a</i> parent elements)
</td></tr>
<tr><td>' . $changed . '</td><td>
bing maps: load metadata only once to reduce API usage (thx Skrupellos!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
readme-qr-codes.zip was not removed from QR code cache directory after installation
</td></tr>
<tr><td>' . $fixed . '</td><td>
list of layers: improper clickable area for duplicate layer-links likely to result in unwanted layer duplications (thx Holger!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
maps could not be saved if WordPress username was longer than 30 chars (thx Erich Lech!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
unintended line break after GPX file download link on some themes
</td></tr>
<tr><td>' . $fixed . '</td><td>
GPX direct download link did not work on all browsers (thx Alex!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHPExcel source comments were misinterpreted as hacker credits by VaultPress (thx Christophe!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
AJAX actions &amp; GeoJSON arrays/layer maps were broken if WP Debug was enabled &amp; on-screen warnings or errors were shown (thx Angelo from <a href="http://www.wocmultimedia.com/" target="_blank">wocmultimedia.com</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"add markername to popup" setting was ignored on the "list of markers below layer maps" (thx Sarah!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
selection of MapBox basemaps was not saved on marker- & layer-edit pages (thx Jelger!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Marijke Metz - <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>, Patrick Ruers  and Fokko van der Leest - <a href="http://wandelenrondroden.nl" target="_blank">http://wandelenrondroden.nl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun, <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.4","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.4') . '</strong> - ' . $text_b . ' 19.07.2015 (<a href="https://www.mapsmarker.com/v2.4p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
assign markers to multiple layers (thx <a href="https://waseem-senjer.com/" target="_blank">Waseem</a>!)
</td></tr>
<tr><td>' . $new . '</td><td>
support for displaying MaqQuest basemaps via https (thx Duncan!)
</td></tr>
<tr><td>' . $new . '</td><td>
option to hide link "download GPX file" in GPX panel
</td></tr>
<tr><td>' . $new . '</td><td>
add gpx_url and gpx_panel to GeoJSON output for markers and layers
</td></tr>
<tr><td>' . $new . '</td><td>
option to select markers from multiple layers when exporting to XLSX/XLS/CSV/ODS
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for <a href="https://wordpress.org/plugins/autoptimize/" target="_blank">Autoptimize</a> plugin which can breaks maps if not properly configured
</td></tr>
<tr><td>' . $new . '</td><td>
multisite: option to activate license key on custom domains
</td></tr>
<tr><td>' . $changed . '</td><td>
enhanced examples for customizing geolocation styling options (thx Bart!)
</td></tr>
<tr><td>' . $changed . '</td><td>
<a href="https://www.visualead.com">Visualead</a> API for creating QR codes now uses secure https by default
</td></tr>
<tr><td>' . $fixed . '</td><td>
distorted minimap controlbox icon if CSS box-sizing was applied to all elements by themes like enfold
</td></tr>
<tr><td>' . $fixed . '</td><td>
XML output for search results via MapsMarker API was not valid
</td></tr>
<tr><td>' . $fixed . '</td><td>
QR code cache image for layers was not deleted via API
</td></tr>
<tr><td>' . $fixed . '</td><td>
XLSX importer for marker updates: if layer set does not exist, value was set to unassigned instead of current value
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix compatibility for WordPress installations using HHVM (thx Rolf!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
unwanted linebreaks respectively broken shortcodes in popuptexts on layermaps (thanks CJ!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $new . '</td><td>
Afrikaans (af) translation thanks to Hans, <a href="http://bmarksa.org/nuus/" target="_blank">http://bmarksa.org/nuus/</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Arabic (ar) translation thanks to Abdelouali Benkheil, Aladdin Alhamda, Nedal Elghamry - <a href="http://arabhosters.com" target="_blank">http://arabhosters.com</a>, yassin and Abdelouali Benkheil - <a href="http://www.benkh.be" target="_blank">http://www.benkh.be</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Finnish (fi_FI) translation thanks to Jessi Bj&ouml;rk - <a href="https://twitter.com/jessibjork" target="_blank">@jessibjork</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Greek (el) translation thanks to Philios Sazeides - <a href="http://www.mapdow.com" target="_blank">http://www.mapdow.com</a>, Evangelos Athanasiadis and Vardis Vavoulakis - <a href="http://avakon.com" target="_blank">http://avakon.com</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Hebrew (he_IL) translation thanks to Alon Gilad - <a href="http://pluto2go.co.il" target="_blank">http://pluto2go.co.il</a> and kobi levi
</td></tr>
<tr><td>' . $new . '</td><td>
Lithuanian (lt_LT) translation thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Thai (th) translation thanks to Makarapong Chathamma and Panupong Siriwichayakul - <a href="http://siteprogroup.com/" target="_blank">http://siteprogroup.com/</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Uighur (ug) translation thanks to Yidayet Begzad - <a href="http://ug.wordpress.org/" target="_blank">http://ug.wordpress.org/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Galician translation thanks to Fernando Coello, <a href="http://www.indicepublicidad.com" target="_blank">http://www.indicepublicidad.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a>, Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a> and Flo Bejgu, <a href="http://www.inboxtranslation.com" target="_blank">http://www.inboxtranslation.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish/Mexico translation thanks to Victor Guevera, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Eze Lazcano
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
<tr><td>' . $issue . '</td><td>
Internet Explorer can crash with WordPress 4.2 to 4.2.2 due to Emoji conflict (<a href="https://core.trac.wordpress.org/ticket/32305" target="_blank">details</a>) - planned to be fixed with WordPress 4.2.3 & 4.3, workaround until WordPress 4.2.3 & 4.3 is available: <a href="https://wordpress.org/plugins/disable-emojis/" target="_blank"">disable Emojis</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.3.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.3.1') . '</strong> - ' . $text_b . ' 29.05.2015 (<a href="https://www.mapsmarker.com/v2.3.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
add support for displaying maps in bootstrap tabs
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized install- and update routine script (less database queries needed)
</td></tr>
<tr><td>' . $fixed . '</td><td>
3 potential XSS vulnerabilities discovered by <a href="https://www.stateoftheinternet.com/security-cybersecurity.html" target="_blank">Akamai</a> - many thanks for the responsible disclosure!
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.3","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.3') . '</strong> - ' . $text_b . ' 23.05.2015 (<a href="https://www.mapsmarker.com/v2.3p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
new option to automatically start geolocation globally on all maps (see changelog on how to start geolocation for selected maps only)
</td></tr>
<tr><td>' . $new . '</td><td>
added javascript variables <i>mapid_js</i> and <i>mapname_js</i> to ease the re-usage of javascript-function from outside the plugin
</td></tr>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/maptiler" target="_blank">new tutorial: how to create custom basemaps using MapTiler</a>
</td></tr>
<tr><td>' . $new . '</td><td>
new 3d logo for Maps Marker Pro :-)
</td></tr>
<tr><td>' . $changed . '</td><td>
use CSS classes instead of inline-styles for recent marker widgets to better support overrides (thx Patrick!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated customer area on mapsmarker.com as well as switching to PHP 5.6 - please report any issues!
</td></tr>
<tr><td>' . $fixed . '</td><td>
GPX tracks using UTF8 with BOM encoding do not show up in Google Chrome (thx José!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
<a href="https://siteorigin.com/" target="_blank">SiteOrigin</a> fixed a plugin conflict by releasing <a href="https://wordpress.org/plugins/siteorigin-panels/" target="_blank">Page Builder v2.1</a>
</td></tr>
<tr><td>' . $fixed . '</td><td>
Removed unset() for validate_local_key() as it could cause the second validation of the local key after refresh to fail
</td></tr>
<tr><td>' . $fixed . '</td><td>
issues with license API calls on servers where SSLVerifyClient directive is set to "required" (thx Ron!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom default icon was not saved after "add new marker"-link was used a second time (thx Cyrille!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom PHP separator settings for floatval() could result in broken maps (thx Tamas!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken layer edit link on marker edit pages after publish- or update-button has been clicked
</td></tr>
<tr><td>' . $fixed . '</td><td>
check for PHP Suhosin patch led to whitescreens on special server configurations if phpinfo() was blocked
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $new . '</td><td>
Slovenian (sl_SL) translation thanks to Anna Dukan, <a href="http://www.unisci24.com/blog/" target="_blank">http://www.unisci24.com/blog/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard, <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a>, Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a> and Flo Bejgu, <a href="http://www.inboxtranslation.com" target="_blank">http://www.inboxtranslation.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Vietnamese (vi) translation thanks to Hoai Thu, <a href="http://bizover.net" target="_blank">http://bizover.net</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.2') . '</strong> - ' . $text_b . ' 15.03.2015 (<a href="https://www.mapsmarker.com/v2.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/2015/03/09/map-icons-collection/" target="_blank">Map Icons Collection now hosted on mapicons.mapsmarker.com</a>
</td></tr>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/2015/02/28/mobile-version-of-mapsmarker-com-launched/" target="_blank">mobile version of mapsmarker.com launched</a>
</td></tr>
<tr><td>' . $new . '</td><td>
support for plugin updates via encrypted and authenticated https connection (with fallback to http if server uses outdated libraries)
</td></tr>
<tr><td>' . $new . '</td><td>
show warning message in dynamic changelog if server uses outdated and potentially insecure PHP version (<5.4) - supporting <a href="http://www.wpupdatephp.com/" target="_blank">wpupdatephp.com</a>
</td></tr>
<tr><td>' . $changed . '</td><td>
improved sanitising of GeoJSON, GeoRSS, KML, Wikitude API input parameters
</td></tr>
<tr><td>' . $fixed . '</td><td>
admin-authenticated SQL injection vulnerability
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP undefined index warnings when adding new recent marker widget
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.1') . '</strong> - ' . $text_b . ' 21.02.2015 (<a href="https://www.mapsmarker.com/v2.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
optimized editing workflow for marker maps - no more reloads needed due to AJAX support
</td></tr>
<tr><td>' . $new . '</td><td>
support for parsing shortcodes in popuptexts on layer maps (thx caneblu!)
</td></tr>
<tr><td>' . $new . '</td><td>
CSS classes and labels for GPX panel data (thx caneblu!)
</td></tr>
<tr><td>' . $new . '</td><td>
added CSS class .lmm-listmarkers-markername to allow better styling (thx Christian!)
</td></tr>
<tr><td>' . $new . '</td><td>
improved SEO for fullscreen maps by adding Settings->General->"Site Title" to end of &lt;title&gt;-tag
</td></tr>
<tr><td>' . $new . '</td><td>
enhanced tools section with bulk editing for URL to GPX tracks and GPX panel status
</td></tr>
<tr><td>' . $new . '</td><td>
HTML in popuptexts is now also parsed in recent marker widgets (thx Oleg!)
</td></tr>
<tr><td>' . $new . '</td><td>
enhance duplicate markers-bulk action to allow reassigning duplicate markers to different layers (thx Fran!)
</td></tr>
<tr><td>' . $changed . '</td><td>
update Mapbox integration to API v4 <span style="font-weight:bold;color:red;">(attention is needed if you are using custom Mapbox styles! <a href="https://www.mapsmarker.com/mapbox" target="_blank">show details</a>)</span>
</td></tr>
<tr><td>' . $changed. '</td><td>
minimap improvements: toggle icon & minimised state now scalable; use of SVG instead of PNG for toggle icon (thx <a href="https://github.com/Norkart/Leaflet-MiniMap/" target="_blank">robpvn</a>!)
</td></tr>
<tr><td>' . $changed . '</td><td>
link to changelog on mapsmarker.com for update pointer if dynamic changelog has already been hidden
</td></tr>
<tr><td>' . $changed . '</td><td>
strip invisible control chars when adding/updating maps via importer as this could break maps
</td></tr>
<tr><td>' . $changed . '</td><td>
strip invisible control chars from GeoJSON array added via importer/do_shortcode() as this could break maps
</td></tr>
<tr><td>' . $changed . '</td><td>
check for updates more often when the user visits update relevant WordPress backend pages (thx Yahnis!)
</td></tr>
<tr><td>' . $changed . '</td><td>
show complete troubleshooting link on frontend only if map could not be loaded to users with manage_options-capability (thx Moti!)
</td></tr>
<tr><td>' . $changed . '</td><td>
use custom name instead of MD5-hash for dashboard RSS item cache file to prevent false identification as malware by WordFence (thx matiasgt!)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimize load time on backend by executing custom select2 javascripts only on according settings page
</td></tr>
<tr><td>' . $changed . '</td><td>
disable location input field on backend until Google Places search has been fully loaded
</td></tr>
<tr><td>' . $changed . '</td><td>
strip invisible control chars from Wikitude API as this could break the JSON array
</td></tr>
<tr><td>' . $changed . '</td><td>
hide Wikitude API endpoint links in map panels by default as they are not relevant to map viewers (for new installations only)
</td></tr>
<tr><td>' . $changed . '</td><td>
use site name for Wikitude augmented-reality world name if layer=all to enhance findability within Wikitude app
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery select2 addon to v3.5.2
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery UI custom theme for datepicker to v1.11.2
</td></tr>
<tr><td>' . $changed . '</td><td>
improved loading times on layer edit pages by dequeuing unneeded stylesheet for jquery UI datepicker
</td></tr>
<tr><td>' . $changed . '</td><td>
allow full layer selection on marker edit pages after button "add new marker to this layer" has been clicked on layer edit pages
</td></tr>
<tr><td>' . $changed . '</td><td>
openpopup state for marker maps now gets saved too after opening the popup by clicking on the map only (not just by ticking the checkbox)
</td></tr>
<tr><td>' . $changed . '</td><td>
fire load-event on "tilesloaded" on Google basemaps
</td></tr>
<tr><td>' . $changed . '</td><td>
updated markercluster codebase (<a href="https://github.com/Leaflet/Leaflet.markercluster/commits/master" target="_blank">using build from 27/10/2014</a> - thx danzel!)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated <a href="https://github.com/domoritz/leaflet-locatecontrol" target="_blank">locatecontrol codebase</a> to v0.4.0 (txh domoritz!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker names were not added to popuptexts on fullscreen maps (thx Oleg!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP warnings on marker edit page if option "add directions to popuptext" was set to false
</td></tr>
<tr><td>' . $fixed . '</td><td>
IE8 did not show markers on layer maps if async loading was enabled (thx Marcus!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
XLSX/XLS/ODS/CSV import: links to detailed warning messages were broken if detailed results were hidden
</td></tr>
<tr><td>' . $fixed . '</td><td>
incomplete dynamic preview of popuptexts on marker edit pages if option "add markername to popup" was set to true
</td></tr>
<tr><td>' . $fixed . '</td><td>
incomplete dynamic preview of popuptexts on marker edit pages if position of marker was changed via mouse click
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker map center view on backend was set incorrectly if popuptext was closed after marker dragging
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken popups on marker maps when option "where to include javascripts?" was set to header+inline-javascript
</td></tr>
<tr><td>' . $fixed . '</td><td>
slashes from markernames were not stripped if option to add markername to popuptext was set to true
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken maps if negative lat/lon values for maps created by shortcodes directly were used (thx Keith!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
Wikitude API endpoint for all maps did not deliver any results if a layer with ID 1 did not exist (thx Maurizio!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
dynamic preview of markername in map panels was broken if TinyMCE editor was set to text mode
</td></tr>
<tr><td>' . $fixed . '</td><td>
dynamic preview: switching controlbox status to "collapsed" was broken if saved controlbox status was "expanded"
</td></tr>
<tr><td>' . $fixed . '</td><td>
issues with access to WordPress backend on servers with incomplete applied "Shellshock"-vulnerability-fix (thx Elger!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
replaced 3 broken EEA default WMS layers 5/9/10 (for new installs only in order not to overwrite custom WMS settings)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"Your user does not have the permission to delete this marker!" was shown to non-admins when trying to create new markers
</td></tr>
<tr><td>' . $fixed . '</td><td>
form submit buttons on backend were not displayed correctly with Internet Explorer 9
</td></tr>
<tr><td>' . $fixed . '</td><td>
Google exception when zooming to non-whole numbers (issue evident during touch zoom on touch devices)
</td></tr>
<tr><td>' . $fixed . '</td><td>
occasionally frozen zoom control buttons and broken map panning on marker maps using Google Maps basemaps
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Croatian translation thanks to Neven Pausic, <a href="http://www.airsoft-hrvatska.com" target="_blank">http://www.airsoft-hrvatska.com</a>, Alan Benic and Marijan Rajic, <a href="http://www.proprint.hr" target="_blank">http://www.proprint.hr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard, <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
Galician translation thanks to Fernando Coello, <a href="http://www.indicepublicidad.com" target="_blank">http://www.indicepublicidad.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Japanese translations thanks to <a href="http://twitter.com/higa4" target="_blank">Shu Higash</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank"></a>, Robert Pawlak and Daniel - <a href="http://mojelodzkie.pl" target="_blank">http://mojelodzkie.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a>, Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a> and Flo Bejgu, <a href="http://www.inboxtranslation.com" target="_blank">http://www.inboxtranslation.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to David Ramí­rez, <a href="http://www.hiperterminal.com/" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish/Mexico translation thanks to Victor Guevera, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Eze Lazcano
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun, <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"2.0","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '2.0') . '</strong> - ' . $text_b . ' 06.12.2014 (<a href="https://www.mapsmarker.com/v2.0p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
GPX file download link added to GPX panels (thx Jason for the idea!)
</td></tr>
<tr><td>' . $new . '</td><td>
search for layers by ID, layername and address on "list all layers" page
</td></tr>
<tr><td>' . $new . '</td><td>
support for duplicating layer maps (without assigned markers)
</td></tr>
<tr><td>' . $new . '</td><td>
bulk actions for layers (duplicate, delete layer only, delete & re-assign markers)
</td></tr>
<tr><td>' . $new . '</td><td>
support for search by ID and address within the list of markers (thx Will!)
</td></tr>
<tr><td>' . $new . '</td><td>
database cleanup: remove expired update pointer IDs from user_meta-table (dismissed_wp_pointers) for active user
</td></tr>
<tr><td>' . $new . '</td><td>
added SHA-256 hashes and PGP signing to verify the integrity of plugin packages (<a href="https://www.mapsmarker.com/integrity-checks" target="_blank">more details</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved security for mapsmarker.com & license API (support for Perfect Forward Secrecy, TLS 1.2 & SHA-256 certificate hashes)
</td></tr>
<tr><td>' . $changed . '</td><td>
moved mapsmarker.com to a more powerful server for increased performance & reduced loadtimes (thx <a href="https://www.twosteps.net/?lang=en" target="_blank">twosteps.net</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
GPX files that could not be loaded could break maps (thx Sebastian!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
HTML lang attribute on fullscreen maps set to de-DE instead of custom $locale (thx sprokt!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom sort order on list of markers was reset if direct paging was used (thx Will!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"go back to prepare import"-link on import page was broken (thx Will!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
visual TinyMCE button was broken if Sucuri WAF was active (thx Sucuri for whitelisting!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
removed backticks for dbdelta()-SQL statements to prevent PHP error log entries (thx QROkes!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard, <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish/Mexico translation thanks to Victor Guevera, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Eze Lazcano
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.9.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.9.2') . '</strong> - ' . $text_b . ' 15.11.2014 (<a href="https://www.mapsmarker.com/v1.9.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
proxy support for license activation to overcome censorship by Russian authorities
</td></tr>
<tr><td>' . $new . '</td><td>
support for automatic background Maps Marker Pro updates (<a href="http://codex.wordpress.org/Configuring_Automatic_Background_Updates#Plugin_.26_Theme_Updates_via_Filter" target="_blank">if explicitly enabled by using filters</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved accessibility/screen reader support by using proper alt texts (thx <a href="http://opencommons.public1.linz.at/" target="_blank">Open Commons Linz</a>!)
</td></tr>
<tr><td>' . $changed . '</td><td>
update library for geolocation feature (including minor fixes)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed ioncube encoded plugin package to increase compatibility with PHP5.5+
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery timepicker addon to v1.5.0
</td></tr>
<tr><td>' . $changed . '</td><td>
hide admin notice for monitoring tool for "active shortcodes for already deleted maps" immediately after clearing the list
</td></tr>
<tr><td>' . $fixed . '</td><td>
WMS legend link on frontend and fullscreen maps was broken (thx Graham!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
incompatibility notices with certain themes using jQuery mobile (now displaying console warnings instead of alert errors - thx Jody!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapsMarker API search action did not show correct results for popuptext and address (thx Erik!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fix issues with license key grace period on hosts with special setups
</td></tr>
<tr><td>' . $fixed . '</td><td>
HTML5 fullscreen mode was partly broken on IE11 (thx Dan!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
QR code image creation was broken due to visualead API changes if certain parameters were set to null
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Danish translation thanks to Mads Dyrmann Larsen and Peter Erfurt, <a href="http://24-7news.dk" target="_blank">http://24-7news.dk</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank"></a> and Robert Pawlak
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish/Mexico translation thanks to Victor Guevera, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Eze Lazcano
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Ukrainian translation thanks to Andrexj, <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>, Sergey Zhitnitsky and Mykhailo, <a href="http://imgsplanet.com" target="_blank">http://imgsplanet.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.9.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.9.1') . '</strong> - ' . $text_b . ' 11.10.2014 (<a href="https://www.mapsmarker.com/v1.9.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
support for accent folding for API and importer geocoding calls (to better support special chars)
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for Sucuri Security plugin which breaks maps if option "Restrict wp-content access" is active
</td></tr>
<tr><td>' . $changed . '</td><td>
MapsMarker API: use "MapsMarker API" as createdby & updatedby attribute if not set
</td></tr>
<tr><td>' . $fixed . '</td><td>
leaflet-min.css was not properly loaded on RTL themes (thx Nic!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
potential CSS conflict resulting in geolocate icon not being shown (thx Christopher!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom default marker icon was not saved when creating a new marker map (thx Oleg!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom panel background for marker maps was taken from layer map settings (thx Bernd!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
API delete action for markers was broken (thx Jason!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
"Delete all markers from all layers" function on tools page did not delete cached QR code images
</td></tr>
<tr><td>' . $fixed . '</td><td>
Google+Bing language localizations could be broken since WordPress 4.0 as constant WPLANG has been depreciated
</td></tr>
<tr><td>' . $fixed . '</td><td>
Bing culture parameter was ignored and fallback set to en-US when constant WPLANG with hypen was used
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapsMarker API search action did not work as designed if popuptext or address was empty (thx Jason!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
RSS & Atom feeds for marker and layer maps did not validate with http://validator.w3.org
</td></tr>
<tr><td>' . $fixed . '</td><td>
remove slashes before single apostrophes (Arc d\\\'airain) in addresses for new maps / on map updates (thx Guffroy!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
sort order on "list all markers" page was broken on page 2+ if custom sort order was selected (thx kluong!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Danish translation thanks to Mads Dyrmann Larsen and Peter Erfurt, <a href="http://24-7news.dk" target="_blank">http://24-7news.dk</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank"></a> and Robert Pawlak
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.9","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.9') . '</strong> - ' . $text_b . ' 30.08.2014 (<a href="https://www.mapsmarker.com/v1.9p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
geolocation support: show and follow your location when viewing maps
</td></tr>
<tr><td>' . $new . '</td><td>
added IE11 native fullscreen support
</td></tr>
<tr><td>' . $new . '</td><td>
search function for layerlist on marker edit page
</td></tr>
<tr><td>' . $new . '</td><td>
support for using WMTS servers as custom overlays (thx dimizu!)
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for plugin "WP External Links" which can cause maps to break
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized RTL (right-to-left) language support
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery select2 addon to v3.5.1
</td></tr>
<tr><td>' . $changed . '</td><td>
added backticks (`) around column and table names in all SQL statements to prevent collisions with reserved words
</td></tr>
<tr><td>' . $fixed . '</td><td>
some settings were not selectable when RTL (right-to-left) language support was active
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom overlays and custom basemaps with & and {} chars in URLs were broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
fullscreen mode for multiple maps on one page
</td></tr>
<tr><td>' . $fixed . '</td><td>
cancel fullscreen mode did not work with Firefox 31
</td></tr>
<tr><td>' . $fixed . '</td><td>
additional output (0) before maps created with shortcodes directly (thx Bernd!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
default marker icon was not used for maps created with shortcodes directly (thx Bernd!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken layer maps/plugin installations on mySQL instances using <i>clustering</i> as reserved word (thx Tim!)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Bosnian translation thanks to Kenan Dervišević, <a href="http://dkenan.com" target="_blank">http://dkenan.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank"></a> and Robert Pawlak
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a>, Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a> and Flo Bejgu, <a href="http://www.inboxtranslation.com" target="_blank">http://www.inboxtranslation.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.8.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.8.1') . '</strong> - ' . $text_b . ' 22.07.2014 (<a href="https://www.mapsmarker.com/v1.8.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/2014/07/22/10-discount-code-to-celebrate-the-1st-anniversary-of-maps-marker-pro/" target="_blank">10% discount code to celebrate the 1st anniversary of Maps Marker Pro</a>
</td></tr>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com" target="_blank">enabled SSL by default for MapsMarker.com website & installed EV SSL certificate (=verified identity)</a>
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility check for "Page Builder by SiteOrigin" plugin (thx porga!)
</td></tr>
<tr><td>' . $new . '</td><td>
tested against WordPress 4.0
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized version compare functions by using PHP version_compare();
</td></tr>
<tr><td>' . $fixed . '</td><td>
not all sections within settings could be selected on smaller screens (thx Francesco!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
display of popuptext in GeoRSS feed was broken (thx Indrajit!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fixed broken incompatibility check with Better WordPress Minify plugin v1.3.0
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.8","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.8') . '</strong> - ' . $text_b . ' 27.06.2014 (<a href="https://www.mapsmarker.com/v1.8p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
layer maps: center map on markers and open popups by clicking on list of markers entries
</td></tr>
<tr><td>' . $new . '</td><td>
new tool for monitoring "active shortcodes for already deleted maps"
</td></tr>
<tr><td>' . $new . '</td><td>
option to disable Google Places Autocomplete API on backend (for <a href="http://travel.synyan.net" target="_blank">John</a> & other users in countries, where access to Google APIs is blocked)
</td></tr>
<tr><td>' . $changed . '</td><td>
replaced discontinued predefined MapBox tiles "MapBox Streets" with "Natural Earth I"
</td></tr>
<tr><td>' . $fixed . '</td><td>
input field for marker and layer zoom on backend was too small on mobile devices
</td></tr>
<tr><td>' . $fixed . '</td><td>
undefined index PHP warnings on maps created with shortcodes only
</td></tr>
<tr><td>' . $fixed . '</td><td>
backslashes in popuptexts resulted in broken layer maps - now replaced with slashes (thx Dmitry!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
option to hide new mapsmarker.com blogposts and link section in dashboard widget was broken
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun, <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.7","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.7') . '</strong> - ' . $text_b . ' 07.06.2014 (<a href="https://www.mapsmarker.com/v1.7p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $changed . '</td><td>
upgrade to leaflet.js v0.7.3 (maintenance release with 8 bugfixes, <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#073-may-23-2014" target="_blank">changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
update marker cluster codebase (using build 28/05/14 instead of 14/03/14)
</td></tr>
<tr><td>' . $changed . '</td><td>
show more detailed error messages on issues with mapsmarker.com license API calls
</td></tr>
<tr><td>' . $fixed . '</td><td>
image edit+remove overlay buttons in TinyMCE editor for popuptexts on marker edit pages were missing since WordPress 3.9 (thx <a href="http://dorf.vsgtaegerwilen.ch" target="_blank">Bruno</a>)
</td></tr>
<tr><td>' . $fixed . '</td><td>
tiles for Google Maps disappeared during zoom when pinch zooming on mobile phones
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken license API calls on servers with outdated SSL libraries
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a>, Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a> and Flo Bejgu, <a href="http://www.inboxtranslation.com" target="_blank">http://www.inboxtranslation.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.6","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.6') . '</strong> - ' . $text_b . ' 18.05.2014 (<a href="https://www.mapsmarker.com/v1.6p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
improved performance for layer maps by asynchronous loading of markers via GeoJSON
</td></tr>
<tr><td>' . $new . '</td><td>
added support for loading maps within jQuery Mobile frameworks (thanks Håkan!)
</td></tr>
<tr><td>' . $new . '</td><td>
option to disable loading of Google Maps API for higher performance if alternative basemaps are used only
</td></tr>
<tr><td>' . $new . '</td><td>
map parameters can be overwritten within shortcodes (e.g. [mapsmarker marker="1" height="100"]) - <a href="https://www.mapsmarker.com/shortcodes" target="_blank">see available shortcode parameters</a>
</td></tr>
<tr><td>' . $new . '</td><td>
added support for GeoJSON-API-links for multi-layer-maps in map panels
</td></tr>
<tr><td>' . $new . '</td><td>
added new sort order options for "list of markers" below layer maps (popuptext, icon, created by, updated by, kml_timestamp)
</td></tr>
<tr><td>' . $changed . '</td><td>
significantly improve loading time for huge layer maps by limiting (hidden) geo microformat tags
</td></tr>
<tr><td>' . $changed . '</td><td>
update import-export library PHPExcel to v1.8.0 (<a href="https://github.com/PHPOffice/PHPExcel/blob/develop/changelog.txt" target="_blank">changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
increase timeout for loading gpx files from 10 to 30 seconds to better support larger files
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized CSS classes and removed inline-styles for list of markers-table for better custom styling
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery timepicker addon to v1.4.4
</td></tr>
<tr><td>' . $changed . '</td><td>
updated jQuery select2 addon for settings to v3.4.8
</td></tr>
<tr><td>' . $changed . '</td><td>
hardened icon upload function to better prevent potential directory traversal attacks
</td></tr>
<tr><td>' . $changed . '</td><td>
renamed transient for proxy access to avoid plugin conflicts (thanks <a href="https://twitter.com/pippinsplugins" target="_blank">@pippinsplugins</a>!)
</td></tr>
<tr><td>' . $changed . '</td><td>
hardened SQL queries for multi-layer-maps
</td></tr>
<tr><td>' . $fixed . '</td><td>
&lt;ol&gt; and &lt;ul&gt; lists were not shown correctly in popuptexts (thanks <a href="http://storyv.com/world/" target="_blank">Dan</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
wrong line-height applied to panel api images could break map layout on certain themes (thx K.W.!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
if number of markers within a cluster was 5 digits or more, a linebreak was added
</td></tr>
<tr><td>' . $fixed . '</td><td>
potential low-critical PHP object injection vulnerabilities with PHPExcel, discovered by <a href="https://security.dxw.com/" target="_blank">https://security.dxw.com/</a>
</td></tr>
<tr><td>' . $fixed . '</td><td>
issues on plugin updates on servers with PHP 5.5 and ioncube support
</td></tr>
<tr><td>' . $fixed . '</td><td>
license key propagation to subsites on multisite installations was broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
uploaded icons were not saved in the marker icon directory on multisite installations
</td></tr>
<tr><td>' . $fixed . '</td><td>
GPX tracks were not shown on layer maps if Google Adsense was active
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Pawel Wyszy&#324;ski, <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank"></a> and Robert Pawlak
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.9","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.9') . '</strong> - ' . $text_b . ' 13.04.2014 (<a href="https://www.mapsmarker.com/v1.5.9p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
Maps Marker Pro reseller program launched - see <a href="https://www.mapsmarker.com/reseller" target="_blank">https://www.mapsmarker.com/reseller</a> for more details
</td></tr>
<tr><td>' . $new . '</td><td>
show warning message if incompatible plugin "Root Relative URLs" is active (thx Brad!)
</td></tr>
<tr><td>' . $changed . '</td><td>
plugin updates are now delivered via SSL to prevent man-in-the-middle-attacks (supporting <a href="https://www.resetthenet.org/" target="_blank">resetthenet.org</a> - <a href="http://mapsmarker.com/helpdesk" target="_blank">please report any issues</a>!)
</td></tr>
<tr><td>' . $changed . '</td><td>
remove plugin version used from source code on frontend to prevent information disclosure
</td></tr>
<tr><td>' . $changed . '</td><td>
remove source code comment about Maps Marker Pro when "remove backlink" option is enabled
</td></tr>
<tr><td>' . $fixed . '</td><td>
update plugin-update-checker to v1.5 (as it may conflict with other plugins using this library, resulting in no info about new updates - thx Shepherd!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
fixed potential XSS issues (exploitable by admins only)
</td></tr>
<tr><td>' . $fixed . '</td><td>
attribution for mapbox 2 basemap was wrong on marker and layer edit pages
</td></tr>
<tr><td>' . $fixed . '</td><td>
WMS demo layer "Vienna public toilets" was not shown on KML view (fixed on new installations only to not overwrite existing custom settings)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun, <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.8","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.8') . '</strong> - ' . $text_b . ' 27.03.2014 (<a href="https://www.mapsmarker.com/v1.5.8p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
add css classes markermap/layermap and marker-ID/layer-ID to each map div for better custom styling
</td></tr>
<tr><td>' . $new . '</td><td>
option to add markernames to popups automatically (default = false)
</td></tr>
<tr><td>' . $new . '</td><td>
allow admins to change createdby and createdon information for marker and layer maps
</td></tr>
<tr><td>' . $new . '</td><td>
display an alert for unsaved changes before leaving marker/layer edit or settings pages
</td></tr>
<tr><td>' . $new . '</td><td>
new tool to clear QR code images cache
</td></tr>
<tr><td>' . $new . '</td><td>
map moves back to initial position on marker maps after popup is closed
</td></tr>
<tr><td>' . $new . '</td><td>
added support for gif and jpg marker icons
</td></tr>
<tr><td>' . $changed . '</td><td>
replaced option "maximum width for images in popups" with option "CSS for images in popups" (<strong>action is needed if you changed maximum width for images in popups!</strong>)
</td></tr>
<tr><td>' . $changed . '</td><td>
switch to persistent javascript variable names instead of random numbers on frontend (thx Sascha!)
</td></tr>
<tr><td>' . $changed . '</td><td>
remove support for Cloudmade basemaps as free tile service is discontinued (->changing basemap to OSM for maps using Cloudmade)
</td></tr>
<tr><td>' . $changed . '</td><td>
layer center pin on backend now always stays on top of markers and is now a bit transparent (thx Sascha!)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized live preview of popup content on marker edit page (now also showing current address for directions link)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed option "extra CSS for table cells" for list of markers
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized backend loadtimes on marker+layer updates (not loading plugin header twice anymore; next: AJAX ;-)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved plugin security by implementing recommendations resulting from second security audit by the City of Vienna
</td></tr>
<tr><td>' . $changed . '</td><td>
license verification calls are now done via WordPress HTTP API, supporting proxies configured in wp-config.php
</td></tr>
<tr><td>' . $changed . '</td><td>
use WordPress HTTP API instead of cURL() for custom marker icons and shadow check
</td></tr>
<tr><td>' . $changed . '</td><td>
use wp_handle_upload() for icon upload instead of WP_Filesystem() for better security
</td></tr>
<tr><td>' . $changed . '</td><td>
update marker cluster codebase (using build 14/03/14 instead of 21/01/14)
</td></tr>
<tr><td>' . $changed . '</td><td>
set appropriate title for HTML5 fullscreen button (view fullscreen/exit fullscreen)
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker icon selection on backend was broken on Internet Explorer 11 (use of other browsers is recommended generally)
</td></tr>
<tr><td>' . $fixed . '</td><td>
Maps Marker API: validity check for post requests for createdon/updatedon parameter failed (thx Sascha!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
added clear:both; to directions link in popup text to fix display of floating images (thx Sascha!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
custom css for marker clusters was not used if shortcode is used within a template file or widget
</td></tr>
<tr><td>' . $fixed . '</td><td>
link to directions settings in marker popup texts on marker edit pages was broken (visible on advanced editor only)
</td></tr>
<tr><td>' . $fixed . '</td><td>
dynamic preview of WMS layers was broken on backend since v1.5.7
</td></tr>
<tr><td>' . $fixed . '</td><td>
potential cross site scripting issues (mostly exploitable by admin users only)
</td></tr>
<tr><td>' . $fixed . '</td><td>
wpdb::prepare() warning message on Wikitude API output for layer maps
</td></tr>
<tr><td>' . $fixed . '</td><td>
visual tinyMCE editor was broken on marker edit and tools pages since WordPress 3.9-alpha
</td></tr>
<tr><td>' . $fixed . '</td><td>
icon upload button was broken since WordPress 3.9-alpha
</td></tr>
<tr><td>' . $fixed . '</td><td>
escaping of input values with mysql_real_escape_string() was broken since WordPress 3.9-alpha (now replaced with esc_sql())
</td></tr>
<tr><td>' . $fixed . '</td><td>
resetting the settings was broken since WordPress 3.9-alpha (now replaced with esc_sql())
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>, Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a> and Juan Valdes
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Swedish translation thanks to Olof Odier, Tedy Warsitha, Dan Paulsson <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren, <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a> and Anton Andreasson, <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.7","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.7') . '</strong> - ' . $text_b . ' 01.03.2014 (<a href="https://www.mapsmarker.com/v1.5.7p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
support for dynamic switching between simplified and advanced editor (no more reloads needed)
</td></tr>
<tr><td>' . $new . '</td><td>
more secure authentication method for <a href="https://www.mapsmarker.com/mapsmarker-api">MapsMarker API</a> (<strong>old method with public key only is not supported anymore!</strong>)
</td></tr>
<tr><td>' . $new . '</td><td>
new <a href="https://www.mapsmarker.com/mapsmarker-api">MapsMarker API</a> search action with support for bounding box searches and more
</td></tr>
<tr><td>' . $new . '</td><td>
support for filtering of marker icons on backend (based on filename)
</td></tr>
<tr><td>' . $new . '</td><td>
support for changing marker IDs and layer IDs from the tools page
</td></tr>
<tr><td>' . $new . '</td><td>
support for bulk updates of marker maps on the tools page for selected layers only
</td></tr>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/order" target="_blank">store on mapsmarker.com</a> now also accepts Diners Club credit cards
</td></tr>
<tr><td>' . $changed . '</td><td>
updated marker edit page (optimized marker icons display, less whitespace for better workflow, added "Advanced settings" row)
</td></tr>
<tr><td>' . $changed . '</td><td>
checkbox for multi layer maps is now also visible by default on layer edit pages
</td></tr>
<tr><td>' . $changed . '</td><td>
WMS legend link is not added to WMS attribution if legend link is empty
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized input on backend by adding labels to all form elements
</td></tr>
<tr><td>' . $fixed . '</td><td>
single quotes in marker map names were escaped (thx Eric!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
double quotes in marker map names would break maps if marker was updated/created via import
</td></tr>
<tr><td>' . $fixed . '</td><td>
double quotes in marker map names would break maps if marker was updated via API
</td></tr>
<tr><td>' . $fixed . '</td><td>
parameter clustering on layer view action in Maps Marker API did not return any results
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Slovak translation thanks to Zdenko Podobny
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
custom marker cluster colors do not show up on backend layer maps if WordPress <3.7 is used - upgrade is advised!
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.6","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.6') . '</strong> - ' . $text_b . ' 10.02.2014 (<a href="https://www.mapsmarker.com/v1.5.6p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
import and export of layer maps as CSV/XLS/XLSX/ODS file
</td></tr>
<tr><td>' . $new . '</td><td>
support for conditional SSL loading of Javascript for Google Maps to increase performance (thx John!)
</td></tr>
<tr><td>' . $new . '</td><td>
re-added option to load javascript in header (for conflicts with certain themes and plugins, default: footer)
</td></tr>
<tr><td>' . $new . '</td><td>
added check if browser support window.console for displaying gpx track status info on backend
</td></tr>
<tr><td>' . $changed . '</td><td>
icons on marker maps and layer maps center icon on backend are now also draggable (thx Sascha for the hint!)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized mysql queries for list all marker admin page and georss-feeds (by removing concat()-function)
</td></tr>
<tr><td>' . $changed . '</td><td>
use plugin name "Maps Marker Pro" instead of "Leaflet Maps Marker" for texts on plugin-inactive-checks and for wp_nonce-messages
</td></tr>
<tr><td>' . $changed . '</td><td>
renamed plugin from "Leaflet Maps Marker Pro" to "Maps Marker Pro" on WordPress plugins page for better consistency
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker import verification could fail under certain circumstances
</td></tr>
<tr><td>' . $fixed . '</td><td>
removed display of custom css on backend map pages on WordPress <3.7 (=bug solved with WordPress 3.7)
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Danish translation thanks to Mads Dyrmann Larsen and Peter Erfurt, <a href="http://24-7news.dk" target="_blank">http://24-7news.dk</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.5","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.5') . '</strong> - ' . $text_b . ' 31.01.2014 (<a href="https://www.mapsmarker.com/v1.5.5p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
loading progress bar for markerclusters when loading of markers takes longer than 1 second
</td></tr>
<tr><td>' . $changed . '</td><td>
updated Google Maps codebase (removed boolean that will always execute)
</td></tr>
<tr><td>' . $changed . '</td><td>
split leaflet.js in leaflet-core.js and leaflet-addons.js to utilize parallel loading
</td></tr>
<tr><td>' . $changed . '</td><td>
minimized leaflet.css into leaflet.min.css to save a few kb
</td></tr>
<tr><td>' . $changed . '</td><td>
removed option to add javascript to header (as popuptext got broken; default was footer)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed option to disabled conditional css loading (=only load leaflet.css when shortcode used)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed workarounds for WordPress <3.3 for better performance
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun, <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.4","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.4') . '</strong> - ' . $text_b . ' 24.01.2014 (<a href="https://www.mapsmarker.com/v1.5.4p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $changed . '</td><td>
optimized TinyMCE media button integration for posts/pages (showing button just once & design update)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance for marker edit pages and posts/pages (by removing TinyMCE scripts and additional WordPress initialization)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance for dynamic changelog (by removing additional WordPress initialization)
</td></tr>
<tr><td>' . $changed . '</td><td>
improved performance for gpx loading on backend (by recuding database queries needed)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized css loading on backend (load leaflet.css only on marker and layer edit pages)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed backend compatibility check for flickr-gallery plugin
</td></tr>
<tr><td>' . $changed . '</td><td>
GeoJSON API: add marker=all parameter & only allow all/* to list all markers
</td></tr>
<tr><td>' . $changed . '</td><td>
KML API: add marker=all parameter & only allow all/* to list all markers
</td></tr>
<tr><td>' . $changed . '</td><td>
add minimap css styles for Internet Explorer < 9 (thx kermit-the-frog!)
</td></tr>
<tr><td>' . $changed . '</td><td>
update ioncube loader wizard to v2.40
</td></tr>
<tr><td>' . $changed . '</td><td>
update jQuery timepicker addon to v1.43
</td></tr>
<tr><td>' . $changed . '</td><td>
reduced http requests for jquery time picker addon css on marker edit page
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized backend performance by reducing SQL queries and http requests on new layer edit page
</td></tr>
<tr><td>' . $changed . '</td><td>
only show first 25 characters for layernames in select box on marker edit page in order not to break page layout
</td></tr>
<tr><td>' . $changed . '</td><td>
reduced mysql queries on layer edit page by showing marker count for multi-layer-maps only on demand
</td></tr>
<tr><td>' . $fixed . '</td><td>
fit bounds on GPX additions and click on "fit bounds"-link were broken
</td></tr>
<tr><td>' . $fixed . '</td><td>
bing maps were broken if https was used due to changes in the bing url templates
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP error log entries when Wikitude API was called with specific parameters
</td></tr>
<tr><td>' . $fixed . '</td><td>
GeoRSS API for marker parameter displayed incorrect titles
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
<a href="https://translate.mapsmarker.com/projects/lmm" target="_blank">new design template on translation.mapsmarker.com & support for SSL-login</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.3","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.3') . '</strong> - ' . $text_b . ' 17.01.2014 (<a href="https://www.mapsmarker.com/v1.5.3p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
upgrade to <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#072-january-17-2014" target="_blank">leaflet.js v0.7.2</a> (fixing a zooming bug with Chrome 32)
</td></tr>
<tr><td>' . $new . '</td><td>
Vietnamese (vi) translation thanks to Hoai Thu, <a href="http://bizover.net" target="_blank">http://bizover.net</a>
</td></tr>
<tr><td>' . $new . '</td><td>
increased security by loading basemaps for OSM, Mapbox and OGD Vienna via SSL if WordPress also loads via SSL
</td></tr>
<tr><td>' . $new . '</td><td>
increased security by hardening search input field for markers on backend
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized performance by moving version checks for PHP and WordPress to register_activation_hook()
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized performance by running pro active check only on admin pages
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Russian translation thanks to Ekaterina Golubina (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko, <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Turkish translation thanks to Emre Erkan, <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun, <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.2') . '</strong> - ' . $text_b . ' 21.12.2013 (<a href="https://www.mapsmarker.com/v1.5.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="https://www.mapsmarker.com/bitcoin"  target="_top">MapsMarker.com now also supports bitcoin payments</a>
</td></tr>
<tr><td>' . $new . '</td><td>
warning message on importer if . instead of , is used as comma separater for lat/lon values (thx Yannick!)
</td></tr>
<tr><td>' . $new . '</td><td>
additional check if loaded GPX file is valid
</td></tr>
<tr><td>' . $new . '</td><td>
added marker cluster fallback colors for IE6-8 (via markercluster codebase update to v0.4)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated markercluster codebase to v0.4 (<a href="https://github.com/Leaflet/Leaflet.markercluster/blob/master/CHANGELOG.md" target="_blank">changelog</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized minimap control box to better fit leaflet design (thx robpvn!)
</td></tr>
<tr><td>' . $changed . '</td><td>
use WordPress wp_remove_get() function instead of proprietary proxy for fetching GPX files
</td></tr>
<tr><td>' . $changed . '</td><td>
switched from wp_remote_post() to wp_remove_get() to avoid occasional IIS7.0 issues (thx Chas!)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized detailed import log messages to better indicate if test mode is on
</td></tr>
<tr><td>' . $fixed . '</td><td>
import log showed wrong row number on marker updates
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a> and Phibu Reza, <a href="http://www.dedoho.pw/" target="_blank">http://www.dedoho.pw/</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Korean translation thanks to Andy Park, <a href="http://wcpadventure.com" target="_blank">http://wcpadventure.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5.1') . '</strong> - ' . $text_b . ' 07.12.2013 (<a href="https://www.mapsmarker.com/v1.5.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
upgrade to leaflet.js v0.7.1 with 7 bugfixes (<a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#071-december-6-2013" target="_blank">detailed changelog</a>)
</td></tr>
<tr><td>' . $new . '</td><td>
duplicate markers feature
</td></tr>
<tr><td>' . $new . '</td><td>
option to use Google Maps API for Business for csv/xls/xlsx/ods import geocoding (which allows up to 100.000 instead of 2.500 requests per day)
</td></tr>
<tr><td>' . $changed . '</td><td>
geocoding for csv/xls/xlsx/ods import: if Google Maps API returns error OVER_QUERY_LIMIT, wait 1.5sec and try again once
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized backend pages for WordPress 3.8/MP6 theme (re-added separator lines, reduce white space usage)
</td></tr>
<tr><td>' . $changed . '</td><td>
geocoding for MapsMarker API requests: if Google Maps API returns error OVER_QUERY_LIMIT, wait 1.5sec and try again once
</td></tr>
<tr><td>' . $changed . '</td><td>
hardened SQL statements needed for fullscreen maps by additionally using prepared-statements
</td></tr>
<tr><td>' . $changed . '</td><td>
change main menu and admin bar entry from "Maps Marker" to "Maps Marker Pro" again to avoid confusion with lite version
</td></tr>
<tr><td>' . $changed . '</td><td>
removed link from main admin bar menu entry ("Maps Marker Pro") for better usability on mobile devices
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken terms of service and feedback links on Google marker maps
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken Google Adsense ad links on layer maps
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.5","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.5') . '</strong> - ' . $text_b . ' 01.12.2013 (<a href="https://www.mapsmarker.com/v1.5p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
upgrade to leaflet.js v0.7 with lots of improvements and bugfixes (more infos: <a href="http://leafletjs.com/2013/11/18/leaflet-0-7-released-plans-for-future.html" target="_blank">release notes</a> and <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#07-november-18-2013" target="_blank">detailed changelog</a>)
</td></tr>
<tr><td>' . $new . '</td><td>
global maximum zoom level (21) for all basemaps with automatic upscaling if native maximum zoom level is lower
</td></tr>
<tr><td>' . $new . '</td><td>
improved accessibility by adding marker name as alt attribute for marker icon
</td></tr>
<tr><td>' . $new . '</td><td>
compatibility with WordPress 3.8/MP6 (responsive admin template)
</td></tr>
<tr><td>' . $changed . '</td><td>
HTML5 fullscreen updates: support for retina icon + different icon for on/off
</td></tr>
<tr><td>' . $changed . '</td><td>
cleaned up admin dashboard widget (showing blog post titles only)
</td></tr>
<tr><td>' . $changed . '</td><td>
visualead QR code generation: API key needed for custom image url, added support for caching - see blog post for more details
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized license settings page for registering free 30-day-trials
</td></tr>
<tr><td>' . $fixed . '</td><td>
maps break if the option worldCopyJump is set to true
</td></tr>
<tr><td>' . $fixed . '</td><td>
toogle layers control image was not shown on mobile devices with retina display
</td></tr>
<tr><td>' . $fixed . '</td><td>
undefined index message on pro plugin activation
</td></tr>
<tr><td>' . $fixed . '</td><td>
fullscreen layer maps with no panel showed wrong layer center (thx Massimo!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
PHP warning message with debug enabled on license page when no license key was entered
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.4","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.4') . '</strong> - ' . $text_b . ' 16.11.2013 (<a href="https://www.mapsmarker.com/v1.4p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_import_export" target="_top">support for CSV/XLS/XLSX/ODS import and export for bulk additions and bulk updates of markers</a>
</td></tr>
<tr><td>' . $new . '</td><td>
Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
</td></tr>
<tr><td>' . $new . '</td><td>
added a check if marker icon directory is writeable before trying to upload new icons
</td></tr>
<tr><td>' . $changed . '</td><td>
switched from curl() to wp_remote_post() on API geocoding calls for higher compatibility
</td></tr>
<tr><td>' . $changed . '</td><td>
updated markercluster codebase (<a href="https://github.com/Leaflet/Leaflet.markercluster/commits/master" target="_blank">using build from 13/11/2013</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
Improved error handling on metadata errors on bing maps - use console.log() instead of alert()
</td></tr>
<tr><td>' . $changed . '</td><td>
ensure zoom levels of google maps and leaflet maps stay in sync
</td></tr>
<tr><td>' . $changed . '</td><td>
remove zoomanim event handler in onRemove on google maps
</td></tr>
<tr><td>' . $fixed . '</td><td>
alignment of panel and list marker icon images could be broken on certain themes
</td></tr>
<tr><td>' . $fixed . '</td><td>
added fix for loading maps in woocommerce tabs (thx Glenn!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
default error tile image and map deleted image showed wrong www.mapsmarker.com url (ups)
</td></tr>
<tr><td>' . $fixed . '</td><td>
backslashes in map name and address broke GeoJSON output (and thus layer maps) - now replaced with /
</td></tr>
<tr><td>' . $fixed . '</td><td>
tabs in popuptext (character literals) broke GeoJSON output (and thus layer maps) - now replaced with space
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.3.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.3.1') . '</strong> - ' . $text_b . ' 09.10.2013 (<a href="https://www.mapsmarker.com/v1.3.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
new options to set text color in marker cluster circles (thanks Simon!)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed shortcode parsing in popup texts from layer maps completely
</td></tr>
<tr><td>' . $fixed . '</td><td>
GeoJSON output for markers did not display marker name if parameter full was set to no
</td></tr>
<tr><td>' . $fixed . '</td><td>
GeoJSON output could break if special characters were used in markername
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.3","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.3') . '</strong> - ' . $text_b . ' 08.10.2013 (<a href="https://www.mapsmarker.com/v1.3p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
support for shortcodes in popup texts (with some limitations - <a href="https://www.mapsmarker.com/v1.3p" target="_blank">see release notes</a>)
</td></tr>
<tr><td>' . $new . '</td><td>
set marker cluster colors in settings / map defaults / marker clustering settings
</td></tr>
<tr><td>' . $new . '</td><td>
optimized marker and layer admin pages for mobile devices
</td></tr>
<tr><td>' . $new . '</td><td>
notification about new pro versions now also works if access to plugin updates has expired
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized GeoJSON-mySQL-statement (less memory needed now on each execution)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized GeoJSON-output of directions link (using separate parameter dlink now)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized minimap toogle icon (with transition effect, thank robpvn!)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed workaround for former incompatibility with jetpack plugin (has been fixed with jetpack 2.2)
</td></tr>
<tr><td>' . $changed . '</td><td>
make custom update checker more consistent with how WP handles plugin updates (<a href="https://github.com/YahnisElsts/plugin-update-checker/commit/c3a8325c2d81be96c795aaf955aed44e1873f251" target="_blank">details</a>)
</td></tr>
<tr><td>' . $changed . '</td><td>
updated markercluster codebase (<a href="https://github.com/Leaflet/Leaflet.markercluster/commits/master" target="_blank">using build from 25/08/2013</a>)
</td></tr>
<tr><td>' . $fixed . '</td><td>
tabs from address now get removed on edits as this breakes GeoJSON/layer maps (thx Chris!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
save button in settings was not accessible with certain languages active (thx Herbert!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
htmlspecialchars in marker name (< > &) were not shown correctly on hover text (thx fredel+devEdge!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
update class conflict with WordPress "quick edit" feature when debug bar plugin is active (<a href="https://github.com/YahnisElsts/plugin-update-checker/commit/2edd17e" target="_blank">details</a>)
</td></tr>
<tr><td>' . $fixed . '</td><td>
deleting layers when using custom capability settings was broken on layer edit page
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.2.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.2.1') . '</strong> - ' . $text_b . ' 14.09.2013 (<a href="https://www.mapsmarker.com/v1.2.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
<a title="click here for more information" href="https://www.mapsmarker.com/affiliateid" target="_blank">support for MapsMarker affiliate links instead of default backlinks - sign up as an affiliate and receive commissions up to 50% !</a>
</td></tr>
<tr><td>' . $changed . '</td><td>
parsing of GeoJSON for layer maps is now up to 3 times faster by using JSON.parse instead of eval()
</td></tr>
<tr><td>' . $changed . '</td><td>
improved gpx backend proxy security by adding transients
</td></tr>
<tr><td>' . $changed . '</td><td>
using WordPress function antispambot() instead of own function hide_email() for API links
</td></tr>
<tr><td>' . $changed . '</td><td>
display gpx fitbounds-link already on focusing gpx url field (when pasting gpx URL manually)
</td></tr>
<tr><td>' . $fixed . '</td><td>
MapsMarker API - icon-parameter could not be set (always returned null) - thx Hovhannes!
</td></tr>
<tr><td>' . $fixed . '</td><td>
fixed broken settings page when plugin wp photo album plus was active (thx Martin!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
Wikitude API was not accepted on registration if ar:name was empty (now using map type + id as fallback)
</td></tr>
<tr><td>' . $fixed . '</td><td>
plugin uninstall did not remove all database entries completely on multisite installations
</td></tr>
<tr><td>' . $fixed . '</td><td>
incorrect warning on multisite installations to upgrade to latest free version before uninstalling
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Bosnian translation thanks to Kenan Dervišević, <a href="http://dkenan.com" target="_blank">http://dkenan.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.2') . '</strong> - ' . $text_b . ' 31.08.2013 (<a href="https://www.mapsmarker.com/v1.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
support for displaying GPX tracks on marker and layer maps
</td></tr>
<tr><td>' . $new . '</td><td>
option to whitelabel backend admin pages
</td></tr>
<tr><td>' . $new . '</td><td>
advanced permission settings
</td></tr>
<tr><td>' . $new . '</td><td>
optimized settings page (added direct links, return to last seen page after saving and full-text-search)
</td></tr>
<tr><td>' . $changed . '</td><td>
removed visualead logo and backlink from QR code output pages
</td></tr>
<tr><td>' . $changed . '</td><td>
changed minimum required WordPress version from v3.0 to v3.3 (needed for tracks)
</td></tr>
<tr><td>' . $changed . '</td><td>
increased database field for multi layer maps from 255 to 4000 (allowing you to add more layers to a multi layer map)
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized marker and layer edit page (widened first column to better fit different browsers)
</td></tr>
<tr><td>' . $changed . '</td><td>
allow custom icon upload only if user has the capability upload_files
</td></tr>
<tr><td>' . $changed . '</td><td>
optimized default backlinks and added QR-link to visualead
</td></tr>
<tr><td>' . $changed . '</td><td>
reduced maximum zoom level for bing maps to 19 as 21 is not supported worldwide
</td></tr>
<tr><td>' . $fixed . '</td><td>
API does not break anymore if parameter type is not set to json or xml
</td></tr>
<tr><td>' . $fixed . '</td><td>
marker icons in widgets were not aligned correctly on IE<9 on some themes
</td></tr>
<tr><td>' . $fixed . '</td><td>
javascript errors on backend pages when clicking "show more" links
</td></tr>
<tr><td>' . $fixed . '</td><td>
Using W3 Total Cache >=v0.9.3 with active CDN no longer requires custom config
</td></tr>
<tr><td>' . $fixed . '</td><td>
wrong image url on on backend edit pages resulting in 404 http request
</td></tr>
<tr><td>' . $fixed . '</td><td>
wrong css url on on tools page resulting in 404 http request
</td></tr>
<tr><td>' . $fixed . '</td><td>
plugin install failed if php_uname() had been disabled for security reasons (thx Stefan!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
Wikitude API was broken when multiple multi-layer-maps were selected
</td></tr>
<tr><td>' . $fixed . '</td><td>
broken settings page when other plugins enqueued jQueryUI on all admin pages
</td></tr>
<tr><td>' . $fixed . '</td><td>
undefined index error messages on recent marker widget with debug enabled
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $new . '</td><td>
Spanish/Mexico translation thanks to Victor Guevera, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Eze Lazcano
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri and  Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Croatian translation thanks to Neven Pausic, <a href="http://www.airsoft-hrvatska.com" target="_blank">http://www.airsoft-hrvatska.com</a>, Alan Benic and Marijan Rajic, <a href="http://www.proprint.hr" target="_blank">http://www.proprint.hr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard and cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a> and <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Ukrainian translation thanks to Andrexj, <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.1.2","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.1.2') . '</strong> - ' . $text_b . ' 10.08.2013 (<a href="https://www.mapsmarker.com/v1.1.2p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $changed . '</td><td>
tweaked transparency for minimap toogle display (thx <a href="http://twitter.com/robpvn" target="_blank">@robpvn</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
maps did not load correctly in (jquery ui) tabs (thx <a href="http://twitter.com/leafletjs" target="_blank">@leafletjs</a>!)
</td></tr>
<tr><td>' . $fixed . '</td><td>
icon upload button got broken with WordPress 3.6
</td></tr>
<tr><td>' . $fixed . '</td><td>
undefined index messages on license activation if debug is enabled
</td></tr>
<tr><td>' . $fixed . '</td><td>
console warning message "Resource interpreted as script but transferred with MIME type text/plain."
</td></tr>
<tr><td>' . $fixed . '</td><td>
preview of qr code image in settings was broken
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri and  Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.1.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.1.1') . '</strong> - ' . $text_b . ' 06.08.2013 (<a href="https://www.mapsmarker.com/v1.1.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
added option to start an anonymous free 30-day-trial period
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Catalan translation thanks to Efraim Bayarri and  Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard and cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
</td></tr>
</table>'.PHP_EOL;
}

if ( (version_compare($lmm_version_old,"1.1","<")) && ( $lmm_version_old > '0' ) ) {
echo '<p><hr noshade size="1"/></p>';
echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf($text_a, '1.1') . '</strong> - ' . $text_b . ' 02.08.2013 (<a href="https://www.mapsmarker.com/v1.1p" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>
upgraded leaflet.js ("the engine of this plugin") from v0.5.1 to v0.6.4 - please see <a href="http://leafletjs.com/2013/06/26/leaflet-0-6-released-dc-code-sprint-mapbox.html" target="_blank">blog post on leafletjs.com</a> and <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md" target="_blank">full changelog</a> for more details
</td></tr>
<tr><td>' . $new . '</td><td>
Leaflet Maps Marker Pro can now be tested on localhost installations without time limitation and on up to 25 domains on live installations
</td></tr>
<tr><td>' . $new . '</td><td>
added option to switch update channel and download new beta releases (not advised on production sites!)
</td></tr>
<tr><td>' . $new . '</td><td>
minimap now also supports bing maps
</td></tr>
<tr><td>' . $new . '</td><td>
show compatibility warning if plugin "Dreamgrow Scrolled Triggered Box" is active (which is causing settings page to break)
</td></tr>
<tr><td>' . $changed . '</td><td>
move scale control up when using Google basemaps in order not to hide the Google logo (thx Kendall!)
</td></tr>
<tr><td>' . $changed . '</td><td>
reset option worldCopyJump to new default false instead of true (as advised by leaflet API docs)
</td></tr>
<tr><td>' . $changed . '</td><td>
using uglify v2 instead of v1 for javascript minification
</td></tr>
<tr><td>' . $fixed . '</td><td>
minimaps caused main map to zoom change on move with low zoom
</td></tr>
<tr><td>' . $fixed . '</td><td>
do not load Google Adsense ads on minimaps
</td></tr>
<tr><td>' . $fixed . '</td><td>
fixed warning message "constant SUHOSIN_PATCH not found"
</td></tr>
<tr><td>' . $fixed . '</td><td>
fixed warning message "Cannot modify header information" when plugin woocommerce is active
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Bosnian translation thanks to Kenan Dervišević, <a href="http://dkenan.com" target="_blank">http://dkenan.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Croatian translation thanks to Neven Pausic, <a href="http://www.airsoft-hrvatska.com" target="_blank">http://www.airsoft-hrvatska.com</a>, Alan Benic and Marijan Rajic, <a href="http://www.proprint.hr" target="_blank">http://www.proprint.hr</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Dutch translation thanks to Patrick Ruers
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Korean translation thanks to Andy Park, <a href="http://wcpadventure.com" target="_blank">http://wcpadventure.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Slovak translation thanks to Zdenko Podobny
</td></tr>
<tr><td>' . $transl . '</td><td>
updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
</td></tr>
</table>'.PHP_EOL;
}
echo '</div>';

/*******************************************************************************************************************************/
/* 2do: change version numbers and date in first line on each update and add if ( ($lmm_version_old < 'x.x' ) ){ to old changelog
********************************************************************************************************************************
echo '<p style="margin:0.5em 0 0 0;clear:both;"><strong>' . sprintf($text_a, '3.x') . '</strong> - ' . $text_b . ' xx.04.2017 (<a href="https://www.mapsmarker.com/v3.xp" target="_blank">' . $text_c . '</a>):</p>
<table>
<tr><td>' . $new . '</td><td>

</td></tr>
<tr><td>' . $changed . '</td><td>

</td></tr>
<tr><td>' . $fixed . '</td><td>

</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_d . '</a></p></strong>
<p>' . sprintf($text_e, 'https://translate.mapsmarker.com/projects/lmm') . '</p>
</td></tr>
<tr><td>' . $transl . '</td><td>
updated German translation
</td></tr>
<tr><td colspan="2">
<p><strong>' . $text_f . '</a></p></strong>
</td></tr>
<tr><td>' . $issue . '</td><td>
Geolocation feature does not work anymore with Google Chrome 50+ and iOS10+ unless your site is securely accessible via https (<a href="https://www.mapsmarker.com/geolocation-https-only" target="_blank">details</a>)
</td></tr>
</table>'.PHP_EOL;

echo '<p><hr noshade size="1"/></p>';
*******************************************************************************************************************************/
?>
</body>
</html>
<?php } ?>
