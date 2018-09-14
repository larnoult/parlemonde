(function() {
	tinymce.create('tinymce.plugins.kadvideo', {
		init : function(ed, url) {
			var t = this;
			// Register commands
			 ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('kadvideo', n.nodeName == 'IMG');
            });
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_video(o.content, url);
			});
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_videoend(o.content, url);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_video(o.content, url);
			});
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_videoend(o.content, url);
			});
		},
		
		_do_video : function(co) {
			return co.replace(/\[video([^\]]*)\]/g, function(a,b){
				return '<img src="'+url+'/img/t.gif" class="kadvideo mceItem" title="video'+tinymce.DOM.encode(b)+'" />';
			});
		},
		_do_videoend : function(co) {
			return co.replace(/\[\/video([^\]]*)\]/g, function(a,b){
				return '<img src="'+url+'/img/t.gif" class="kadvideoend mceItem" title="/video" />';
			});
		},

		_get_video : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('kadvideo') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},
		_get_videoend : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('kadvideoend') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},
		getInfo : function() {
			return {
				longname : 'Insert video',
				author : 'Benjamin Ritner',
				authorurl : 'http://kadencethemes.com',
				infourl : 'http://kadencethemes.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('kadvideo', tinymce.plugins.kadvideo);

})();