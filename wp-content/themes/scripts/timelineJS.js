
        var timeline_config = {
            width:              '100%',
            height:             '600',
            source:             'https://docs.google.com/spreadsheet/pub?key=0AhPaVbh88bUZdHpPaUV2eVl3Y3FxWDdlRlQ3a1VOVnc&output=html',
            embed_id:           'timeline-embed',               //OPTIONAL USE A DIFFERENT DIV ID FOR EMBED
            start_at_end:       false,                          //OPTIONAL START AT LATEST DATE
            start_at_slide:     '4',                            //OPTIONAL START AT SPECIFIC SLIDE
            start_zoom_adjust:  '3',                            //OPTIONAL TWEAK THE DEFAULT ZOOM LEVEL
            hash_bookmark:      true,                           //OPTIONAL LOCATION BAR HASHES
            font:               'Bevan-PotanoSans',             //OPTIONAL FONT
            debug:              true,                           //OPTIONAL DEBUG TO CONSOLE
            lang:               'fr',                           //OPTIONAL LANGUAGE
            maptype:            'watercolor',                   //OPTIONAL MAP STYLE
            css:                'path_to_css/timeline.css',     //OPTIONAL PATH TO CSS
            js:                 'path_to_js/timeline-min.js'    //OPTIONAL PATH TO JS
        }



//	<iframe src="http://cdn.knightlab.com/libs/timeline/latest/embed/index.html?source=0AhPaVbh88bUZdHpPaUV2eVl3Y3FxWDdlRlQ3a1VOVnc&amp;font=Bevan-PotanoSans&amp;maptype=toner&amp;lang=fr&amp;height=650" width="100%" height="650" frameborder="0"></iframe>