(function() {
	tinymce.create('tinymce.plugins.kadcolumns', {
		init : function(ed, url) {
			// Register commands
			var t = this;
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_column(o.content, url);
			});
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_columnstart(o.content, url);
			});
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_columnend(o.content, url);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_column(o.content);
			});
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_columnstart(o.content);
			});
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_columnend(o.content);
			});
		},
		_do_column : function(co, url) {
			return co.replace(/\[columnhelper([^\]]*)\]/g, function(a,b){
				return '<img src="'+url+'/img/t.gif" class="columnhelper '+tinymce.DOM.encode(b)+' mceItem" title="columnhelper'+tinymce.DOM.encode(b)+'" />';
			});
		},
		_do_columnstart : function(co, url) {
			return co.replace(/\[hcolumns([^\]]*)\]/g, function(a,b){
				return '<img src="'+url+'/img/t.gif" class="columnstart mceItem" title="hcolumns" />';
			});
		},
		_do_columnend : function(co, url) {
			return co.replace(/\[\/hcolumns([^\]]*)\]/g, function(a,b){
				return '<img src="'+url+'/img/t.gif" class="columnend mceItem" title="/hcolumns" />';
			});
		},

		_get_column : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('columnhelper') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},
		_get_columnstart : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('columnstart') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},
		_get_columnend : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('columnend') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},
		 
		getInfo : function() {
			return {
				longname : 'Insert Columns',
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
	tinymce.PluginManager.add('kadcolumns', tinymce.plugins.kadcolumns);

})();