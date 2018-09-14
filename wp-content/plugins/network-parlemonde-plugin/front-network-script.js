jQuery(document).ready(function () {
	console.log("front network script");
var url = document.location.toString();
if (url.match('#')) {jQuery('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;} 

jQuery('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
window.location.hash = e.target.hash;
jQuery('.nav-tabs a[href='+e.target.hash+']').tab('show');
});
});