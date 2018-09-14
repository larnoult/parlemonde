<?php
/*
    Help and credits page - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-help-credits.php') { die ("Please do not access this file directly. Thanks!<br/><a href='https://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }

	$lmm_options = get_option( 'leafletmapsmarker_options' );
	include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php'); ?>
	<p>
	<h1><?php _e('Help','lmm') ?></h1>
	<p>
		<?php _e('If you have any questions or experienced issues, please use the following support channels appropriately.','lmm') ?>
	</p>
	<p>
		<strong><?php _e('One personal request: before you post a new support ticket in the <a href="http://wordpress.org/support/plugin/leaflet-maps-marker" target="_blank">Wordpress Support Forum</a>, please follow the instructions from <a href="http://www.mapsmarker.com/readme-first" target="_blank">http://www.mapsmarker.com/readme-first</a> which give you a guideline on how to deal with the most common issues.','lmm') ?></strong>
	</p>
	<ul>
		<li>- <a href="https://www.mapsmarker.com/faq/" target="_blank"><?php _e('FAQ','lmm') ?></a>	<?php _e('(frequently asked questions)','lmm') ?></li>
		<li>- <a href="https://www.mapsmarker.com/docs/" target="_blank"><?php _e('Documentation','lmm') ?></a></li>
		<li>- <a href="https://www.mapsmarker.com/docs/changelog/" target="_blank"><?php _e('Changelog','lmm') ?></a></li>
		<li>- <a href="https://wordpress.org/support/plugin/leaflet-maps-marker" target="_blank">WordPress Support Forum</a> (<?php _e('free community support','lmm') ?>)</li>
	</ul>
	<p>
		<a style="background:#f99755;display:block;padding:5px 5px 5px 10px;text-decoration:none;color:#2702c6;margin:10px 0;" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_pro_upgrade"><?php _e('If you want to get dedicated 1:1 support from the plugin author, please upgrade to the pro version. Click here to find out how you can start a free 30-day-trial easily','lmm'); ?></a>
	</p>
	<h2><?php _e('License','lmm') ?></h2>
	<p>
		<?php _e('Good news, this plugin is free for everyone! Since it is released under the GPL2, you can use it free of charge on your personal or commercial blog.<br/>Anyway if you enjoy using this plugin, please consider upgrading to the pro version.','lmm') ?>
	</p>
	<h2><?php _e('Trademarks and copyright','lmm') ?></h2>
	<p>
	MapsMarker<sup style="font-size:75%;">&reg;</sup><br/>
	Copyright &copy; 2011-<?php echo date('Y'); ?>, MapsMarker.com e.U., All Rights Reserved
	</p>
	<h2><?php _e('Translations','lmm') ?></h2>
	<p>
	<?php
	$translation_website = '<a href="https://translate.mapsmarker.com/projects/lmm" target="_blank">https://translate.mapsmarker.com/projects/lmm</a>';
	$translation_output = sprintf(__('Adding a new translation or updating an existing one is quite easy - please visit %s for more information!','lmm'),$translation_website);
	echo $translation_output;
	?>
	</p>
	<ul>
		<li>- Afrikaans (af) thanks to Hans, <a href="http://bmarksa.org/nuus/" target="_blank">http://bmarksa.org/nuus/</a></li>
		<li>- Arabic (ar) thanks to Abdelouali Benkheil, Aladdin Alhamda, Nedal Elghamry - <a href="http://arabhosters.com" target="_blank">http://arabhosters.com</a>, yassin and Abdelouali Benkheil - <a href="http://www.benkh.be" target="_blank">http://www.benkh.be</a></li>
		<li>- Bengali (ba_BD) thanks to Nur Hasan - <a href="http://www.answersbd.com" target="_blank">http://www.answersbd.com</a></li>
		<li>- Bosnian (bs_BA) thanks to Kenan Dervišević - <a href="http://dkenan.com" target="_blank">http://dkenan.com</a></li>
		<li>- Bulgarian (bg_BG) thanks to Andon Ivanov - <a href="http://coffebreak.info" target="_blank">http://coffebreak.info</a></li>
		<li>- Catalan (ca) thanks to Vicent Cubells - <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>, Efraim Bayarri and Marta Espinalt - <a href="http://www.martika.es" target="_blank">http://www.martika.es</a></li>
		<li>- Chinese (zh_CN) thanks to John Shen - <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck</li>
		<li>- Chinese (zh_TW) thanks to jamesho Ho - <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a></li>
		<li>- Croatian (hr) thanks to Neven Pausic - <a href="http://www.airsoft-hrvatska.com" target="_blank">http://www.airsoft-hrvatska.com</a>, Alan Benic and Marijan Rajic - <a href="http://www.proprint.hr" target="_blank">http://www.proprint.hr</a></li>
		<li>- Czech (cs_CZ) thanks to Viktor Kleiner and Vlad Kuzba - <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a></li>
		<li>- Danish (da_DK) thanks to Mads Dyrmann Larsen and Peter Erfurt - <a href="http://24-7news.dk" target="_blank">http://24-7news.dk</a></li>
	<li>- Dutch (nl_NL) thanks to Marijke Metz - <a href="http://www.mergenmetz.nl" target="_blank">http://www.mergenmetz.nl</a>, Patrick Ruers, Fokko van der Leest - <a href="http://wandelenrondroden.nl" target="_blank">http://wandelenrondroden.nl</a> and Hans Temming - <a href="http://www.wonderline.nl" target="_blank">http://www.wonderline.nl</a></li>
		<li>- English (en_US)</li>
		<li>- Finnish (fi_FI) thanks to Jessi Bj&ouml;rk - <a href="https://twitter.com/jessibjork" target="_blank">@jessibjork</a></li>
		<li>- French (fr_FR) thanks to Vincèn Pujol - <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville - <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, cazal cédric - <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>, Fabian Hurelle - <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a> and Thomas Guignard - <a href="http://news.timtom.ch" target="_blank">http://news.timtom.ch</a></li>
		<li>- Galician (gl_ES) thanks to Fernando Coello - <a href="http://www.indicepublicidad.com" target="_blank">http://www.indicepublicidad.com</a></li>
		<li>- German (de_DE)</li>
		<li>- Greek (el) thanks to Philios Sazeides - <a href="http://www.mapdow.com" target="_blank">http://www.mapdow.com</a>, Evangelos Athanasiadis and Vardis Vavoulakis - <a href="http://avakon.com" target="_blank">http://avakon.com</a></li>
		<li>- Hebrew (he_IL) thanks to Alon Gilad - <a href="http://pluto2go.co.il" target="_blank">http://pluto2go.co.il</a> and kobi levi</li>
		<li>- Hindi (hi_IN) thanks to Outshine Solutions - <a href="http://outshinesolutions.com" target="_blank">http://outshinesolutions.com</a> and Guntupalli Karunakar - <a href="http://indlinux.org" target="_blank">http://indlinux.org</a></li>
		<li>- Hungarian (hu_HU) thanks to István Pintér - <a href="http://www.logicit.hu" target="_blank">http://www.logicit.hu</a> and Csaba Orban - <a href="http://www.foto-dvd.hu" target="_blank">http://www.foto-dvd.hu</a></li>
		<li>- Indonesian (id_ID) thanks to Andy Aditya Sastrawikarta and Emir Hartato - <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a> and Phibu Reza - <a href="http://www.dedoho.pw/" target="_blank">http://www.dedoho.pw/</a></li>
		<li>- Italian (it_IT) thanks to <a href="mailto:lucabarbetti@gmail.com">Luca Barbetti</a> and Angelo Giammarresi - <a href="http://www.wocmultimedia.biz" target="_blank">http://www.wocmultimedia.biz</a></li>
		<li>- Japanese (ja) thanks to Shu Higashi - <a href="http://twitter.com/higa4" target="_blank">@higa4</a> and Taisuke Shimamoto</li>
		<li>- Korean (ko_KR) thanks to Andy Park - <a href="http://wcpadventure.com" target="_blank">http://wcpadventure.com</a></li>
		<li>- Latvian (lv) thanks to Juris Orlovs - <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess</li>
		<li>- Lithuanian (lt_LT) thanks to Donatas Liaudaitis - <a href="http://www.transleta.co.uk" target="_blank">http://www.transleta.co.uk</a> and Ovidijus - <a href="http://www.manokarkle.lt" target="_blank">http://www.manokarkle.lt</a></li>
		<li>- Malawy (ms_MY) translation thanks to Mohd Zulkifli - <a href="http://www.caridestinasi.com/" target="_blank">http://www.caridestinasi.com/</a></li>
		<li>- Norwegian/Bokmål (nb_NO) translation thanks to Inge Tang - <a href="http://ingetang.com" target="_blank">http://ingetang.com</a></li>
		<li>- Polish (pl_PL) thanks to Pawel Wyszy&#324;ski - <a href="http://injit.pl" target="_blank">http://injit.pl</a>, Tomasz Rudnicki - <a href="http://www.kochambieszczady.pl" target="_blank"></a>, Robert Pawlak and Daniel - <a href="http://mojelodzkie.pl" target="_blank">Daniel</a></li>
		<li>- Portuguese (pt_BR) thanks to Fabio Bianchi - <a href="http://www.bibliomaps.com" target="_blank">http://www.bibliomaps.com</a>, Andre Santos - <a href="http://pelaeuropa.com.br" target="_blank">http://pelaeuropa.com.br</a> and Antonio Hammerl</li>
		<li>- Portuguese (pt_PT) thanks to Joao Campos - <a href="http://www.all-about-portugal.com" target="_blank">http://www.all-about-portugal.com</a></li>
		<li>- Romanian (ro_RO) thanks to Arian - <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a>, Daniel Codrea - <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a> and Flo Bejgu - <a href="http://www.inboxtranslation.com" target="_blank">http://www.inboxtranslation.com</a></li>
		<li>- Russian (ru_RU) thanks to Ekaterina Golubina - (supported by Teplitsa of Social Technologies - <a href="http://te-st.ru" target="_blank">http://te-st.ru</a>) and Vyacheslav Strenadko - <a href="http://slavblog.ru" target="_blank">http://slavblog.ru</a></li>
		<li>- Slovak (sk_SK) thanks to Zdenko Podobny</a></li>
		<li>- Slovenian (sl_SL) thanks to Anna Dukan - <a href="http://www.unisci24.com/blog/" target="_blank">http://www.unisci24.com/blog/</a></li>
		<li>- Swedish (sv_SE) thanks to Olof Odier, Tedy Warsitha, Dan Paulsson - <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a>, Elger Lindgren - <a href="http://bilddigital.se" target="_blank">http://bilddigital.se</a>, Anton Andreasson - <a href="http://andreasson.org/" target="_blank">http://andreasson.org/</a> and Tony Lygnersjö - <a href="https://www.dumsnal.se/" target="_blank">https://www.dumsnal.se/</a></li>
		<li>- Spanish (es_ES) thanks to David Ramírez - <a href="http://www.hiperterminal.com" target="_blank">http://www.hiperterminal.com</a>, Alvaro Lara - <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Ricardo Viteri - <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>, Juan Valdes and Marta Espinalt - <a href="http://www.martika.es" target="_blank">http://www.martika.es</a></li>
		<li>- Spanish/Mexico (es_MX) thanks to Victor Guevera - <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Eze Lazcano</li>
		<li>- Swedish (sv_SE) thanks to Olof Odier, Tedy Warsitha and Dan Paulsson - <a href="http://www.paulsson.eu" target="_blank">http://www.paulsson.eu</a></li>
		<li>- Thai (th) thanks to Makarapong Chathamma and Panupong Siriwichayakul - <a href="http://siteprogroup.com/" target="_blank">http://siteprogroup.com/</a></li>
		<li>- Turkish (tr_TR) thanks to Emre Erkan - <a href="http://www.karalamalar.net" target="_blank">http://www.karalamalar.net</a> and Mahir Tosun - <a href="http://www.bozukpusula.com" target="_blank">http://www.bozukpusula.com</a></li>
		<li>- Uighur (ug) thanks to Yidayet Begzad - <a href="http://ug.wordpress.org/" target="_blank">http://ug.wordpress.org/</a></li>
		<li>- Ukrainian (uk_UK) thanks to Andrexj - <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>, Sergey Zhitnitsky - <a href="http://zhitya.com" target="_blank">http://zhitya.com</a> and Mykhailo - <a href="http://imgsplanet.com" target="_blank">http://imgsplanet.com</a></li>
		<li>- Vietnamese (vi) translation thanks to Hoai Thu - <a href="http://bizover.net" target="_blank">http://bizover.net</a></li>
		<li>- Yiddish (yi) thanks to Raphael Finkel - <a href="http://www.cs.uky.edu/~raphael/yiddish.html" target="_blank">http://www.cs.uky.edu/~raphael/yiddish.html</a></li>
	</ul>
	<h2><?php _e('Licenses for used libraries, services and images','lmm') ?></h2>
	<ul>
		<li>- Leaflet.js, <a href="http://www.leafletjs.com" target="_blank">http://www.leafletjs.com</a>, Copyright (c) 2010-2011: Cloudmade, Copyright (c) 2010-<?php echo date('Y'); ?>: Vladimir Agafonkin</li>
		<li>- Google Maps and bing maps plugin by shramov - <a href="https://github.com/shramov/leaflet-plugins" target="_blank">https://github.com/shramov/leaflet-plugins</a></li>
		<li>- OpenStreetMap: <a href="http://wiki.openstreetmap.org/wiki/OpenStreetMap_License" target="_blank">OpenStreetMap License</a></li>
		<li>- Address autocompletion powered by <a href="https://developers.google.com/places/documentation/autocomplete" target="_blank">Google Places API</a></li>
		<li>- Jquery TimePicker by Trent Richardson, <a href="http://trentrichardson.com/examples/timepicker/" target="_blank">http://trentrichardson.com/examples/timepicker/</a>, license: GPL</li>
		<li>- <a href="https://mapicons.mapsmarker.com" target="_blank">Map Icons Collection</a> by Nicolas Mollet</li>
		<li>- Map center icon by <a href="http://glyphish.com/" target="_blank">Joseph Wain</a>, license: Creative Commons Attribution (by)</li>
		<li>- Question Mark Icon by <a href="http://www.randomjabber.com/" target="_blank">RandomJabber</a></li>
		<li>- Images for changelog from <a href="http://www.mozilla.org/en-US/firefox/11.0/releasenotes/">Firefox release notes</a>, license: Creative Commons Attribution ShareAlike (CC BY-SA 3.0)</li>
		<li>- Plus-, json-, layer-, language-, exclamation-, csv-export- &amp; preview markers-icon from <a href="http://p.yusukekamiyamane.com/" target="_blank">Yusuke Kamiyamane</a>, license: Creative Commons Attribution (by)</li>
		<li>- Home-Icon from <a href="http://prothemedesign.com/" target="_blank">Pro Theme Design</a>, license: Creative Commons Attribution (by)</li>
		<li>- Editor-Switch-Icon by AMAZIGH Aneglus, license: GNU/GPL</li>
		<li>- Submenu icons from <a href="http://bijou.im/" target="_blank">Bijou</a> and <a href="http://somerandomdude.com/work/iconic/" target="_blank">Iconic</a> icon sets (GPL)</li>
		<li>- Import/Export icons by <a href="http://momentumdesignlab.com/" target="_blank">Momenticons</a>, license: Creative Commons Attribution (by)</li>
		<li>- PHPExcel library for import/export <a href="http://phpexcel.codeplex.com/" target="_blank">http://phpexcel.codeplex.com/</a>, License: LGPL</li>
	</ul>
	<h2><?php _e('Credits & special thanks','lmm') ?></h2>
	<ul>
		<li>- <a href="http://psha.org.ru/b/leaflet-plugins.html" target="_blank">shramov</a> for bing and google maps plugins for leaflet</li>
		<li>- Sindre Wimberger (<a href="http://www.sindre.at" target="_blank">http://www.sindre.at</a>) - bugfixing &amp; geo-consulting</li>
		<li>- Julia Loew (<a href="http://www.weiderand.net" target="_blank">http://www.weiderand.net</a>) - logo &amp; corporate design</li>
		<li>- <a href="http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/" target="_blank">WordPress-Settings-API-Class</a> by Aliso the geek</li>
		<li>- Hind who originally released a basic Leaflet plugin (not available anymore) which was used as a basis for Leaflet Maps Marker/Maps Marker Pro</li>
	</ul>
	</p>
</div>
<?php include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php'); ?>