jQuery(document).ready(function($) {


	tinymce.PluginManager.add('test', function(editor, url) {
		
		
		editor.addButton('test', {
			
			image: url + '/test.png',
			tooltip: 'TEST',
			onclick: myTest
		});
		
		function myTest() {
			
			editor.windowManager.open({
					
				title: 'TEST',
				body: 
				[
					{
					
						type: "form",
						layout: 'grid',
						columns: 2,
						items: 
						[
							{
								label: "Search Terms",
								name: "queryinput",
								type: "textbox",
								size: 50
							},
							{
								text: "Search",
								name: "submit_button",
								type: "button",
								maxWidth: 60,
								//onClick: function() {alert('Hello World');}
								onClick: SearchYouTube()
							}
						]
					},
					{
						type: 'container',
						name: 'container',
						html: '<div id="search-results-block">TESTING...</div>',
						width: 100,
						height: 100
					}
				],
				
				// Submit window info back to tinymce editor
				onsubmit: function (t) {
					
					editor.execCommand('mceInsertContent', !1, 'TEST')
				}
			})
		}
		
		
		function SearchYouTube(query) {
			$.ajax({
				url: 'http://gdata.youtube.com/feeds/mobile/videos?alt=json-in-script&q=' + query,
				dataType: 'jsonp',
				success: function (data) {
					var row = "";
					for (i = 0; i < data.feed.entry.length; i++) {
						row += "<div class='search_item'>";
						row += "<table width='100%'>";
						row += "<tr>";
						row += "<td vAlign='top' align='left'>";
						row += "<a href='#' ><img width='120px' height='80px' src=" + data.feed.entry[i].media$group.media$thumbnail[0].url + " /></a>";
						row += "</td>";
						row += "<td vAlign='top' width='100%' align='left'>";
						row += "<a href='#' ><b>" + data.feed.entry[i].media$group.media$title.$t + "</b></a><br/>";
						row += "<span style='font-size:12px; color:#555555'>by " + data.feed.entry[i].author[0].name.$t + "</span><br/>";
						row += "<span style='font-size:12px' color:#666666>" + data.feed.entry[i].yt$statistics.viewCount + " views" + "<span><br/>";
						row += "</td>";
						row += "</tr>";
						row += "</table>";
						row += "</div>";
					}
					document.getElementById("search-results-block").innerHTML = row;
				},
				error: function () {
					alert("Error loading youtube video results");
				}
			});
			return false;
		}
		
	});
});