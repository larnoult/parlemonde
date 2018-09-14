<?php
function puzzle_with_timer(){
	return array(
		'name_1' => 'Macaca',
		'name_2' => 'Sintel',
		'name_3' => 'Image',
		'name_4' => 'Big Buck Bunny',
		'name_5' => 'Car',
		'name_6' => 'Old Town',
		'tiles_h' => '4',
		'tiles_v' => '4',
		'gap' => 'true',
		'auto_size' => 'false',
		'showStart' => 'true',
        'sh_stop' => 'true',

		'duration' => '200',
		'bgColor' => '#fff',

		'w_img' => '400',
		'h_img' => '400',

		'bgOpacity' => '1',
		'imgBgOpacity' => '.2',
		'shuffleNum' => '5',
		'menuVisible' => 'false',

		'menuNameShuffle' => 'Shuffle',
		'menuNameGrid' => 'Grid',
		'menuNameImage' => 'Image',

		'menu_grid' => '3x3,4x4,5x5',

		'menu_shuffle' => 'Easy:10,Medium:30,Hard:60',

        'gl_var' => 'var interval;'."\r\n".
            'var minutes;'."\r\n".
            'var seconds;'."\r\n".
            'var game_over = false;',

		'onCompleted' => 'jQuery(".sp_box .retry-button").html(\'Try again\');'."\r\n".
            'if(game_over){'."\r\n".
            '   jQuery(".message-box > div > p").html("You\'ve lost...");'."\r\n".
            '}else{'."\r\n".
            '   jQuery(".message-box > div > p").html("You\'ve won!");'."\r\n".
            '   clearInterval(interval);'."\r\n".
            '}'."\r\n".
            'jQuery(".sp_box").addClass(\'visible\');',

		'onStart' => 'minutes = 0;'."\r\n".
            'seconds = 20;'."\r\n".
            'jQuery(".%sp_name% .sp_box").removeClass(\'visible\');',

		'afterCreate' => 'interval && clearInterval(interval);'."\r\n".
		    'interval = setInterval(function() {'."\r\n".
            '    main_timer();'."\r\n".
            '}, 1000);'."\r\n".
            'main_timer();'."\r\n".
            'function main_timer() {'."\r\n".
            '    if (seconds<0) {'."\r\n".
            '        minutes--;'."\r\n".
            '        seconds = 59;'."\r\n".
            '    }'."\r\n".
            '    jQuery(".%sp_name% .c_movies").html( (minutes<10?("0"+minutes):minutes) + ":" + (seconds<10?("0"+seconds):seconds) );'."\r\n".
            '    if(seconds == 0 && minutes == 0){'."\r\n".
            '        game_over = true;'."\r\n".
            '        config.onCompleted();'."\r\n".
            '        clearInterval(interval);'."\r\n".
            '        return;'."\r\n".
            '    }'."\r\n".
            '    seconds--;'."\r\n".
            '    game_over = false'."\r\n".
            '}',

		'firstStart' =>''.
            'jQuery(\'.%sp_name%\').append(\'<div id="Timer"><div class="c_movies"></div></div><div class="sp_box"><div class="message-box"><div><p class="mini">Gather the Puzzle for 20 seconds</p><div class="retry-button">Start</div><br></div></div></div>\');'."\r\n".
            'jQuery(".%sp_name% .retry-button").bind(\'mousedown touchstart\', function(e) {'."\r\n".
            '  jQuery(\'.%sp_name%\').shufflepuzzle[\'%sp_name%\']();'."\r\n".
            '});'."\r\n".
            'jQuery(".%sp_name% .sp_box").addClass(\'visible\');'."\r\n".
            'jQuery(".%sp_name% .sp_box .retry-button").html(\'Start\');',

		'puzzle_style' => '@import url(//fonts.googleapis.com/css?family=Open+Sans);'."\r\n".
            '.sp_box {'."\r\n".
            '  width: 100%;'."\r\n".
            '  height: 100%;'."\r\n".
            '  opacity: 0;'."\r\n".
            '  visibility:hidden;'."\r\n".
            '  transition:visibility 0s linear 0.5s, z-index 0s linear 0.5s, opacity 0.5s linear;'."\r\n".
            '  background: rgba(0, 0, 0, 0.73);'."\r\n".
            '  z-index: 0;'."\r\n".
            '  position: absolute;'."\r\n".
            '  z-index: 0;'."\r\n".
            '}'."\r\n".
            '.sp_box.visible {'."\r\n".
            '  visibility:visible;'."\r\n".
            '  opacity:1;'."\r\n".
            '  transition-delay:0s;'."\r\n".
            '  z-index: 9999;'."\r\n".
            '}'."\r\n".
            '       '."\r\n".
            '.sp_box > div{'."\r\n".
            '  display: table;'."\r\n".
            '  width: 100%;'."\r\n".
            '  height: 100%;'."\r\n".
            '}'."\r\n".
            '.sp_box > div > div > *{'."\r\n".
            '  margin: 0;'."\r\n".
            '}'."\r\n".
            '.sp_box > div > div {'."\r\n".
            '  color: #EBEBEB;'."\r\n".
            '  font-family: \'Open Sans\', sans-serif;'."\r\n".
            '  font-size: 46px;'."\r\n".
            '  line-height: 60px;'."\r\n".
            '  font-weight: bold;'."\r\n".
            '  text-align: center;         '."\r\n".
            '  height:100%;      '."\r\n".
            '  width: 100%;  '."\r\n".
            '  display: table-cell;      '."\r\n".
            '  vertical-align: middle;'."\r\n".
            '}'."\r\n".
            '       '."\r\n".
            '.sp_box > div > div > .mini {'."\r\n".
            '  font-size: 22px;'."\r\n".
            '  line-height: 40px;'."\r\n".
            '}'."\r\n".
            '       '."\r\n".
            '.sp_box .retry-button  {'."\r\n".
            '  display: inline-block;'."\r\n".
            '  background: #2F92A4;'."\r\n".
            '  border-bottom: 6px solid #205B66;'."\r\n".
            '  font-size: 22px;'."\r\n".
            '  padding: 0 20px 5px;'."\r\n".
            '  text-decoration: none;'."\r\n".
            '  color: #FFFFFF;'."\r\n".
            '  height: 48px;'."\r\n".
            '  line-height: 40px;'."\r\n".
            '  cursor: pointer;'."\r\n".
            '  margin-left: 9px;'."\r\n".
            '}'."\r\n".
            '       '."\r\n".
            '.counter{'."\r\n".
            '  font-size: 25px;'."\r\n".
            '  line-height: 25px;'."\r\n".
            '}'."\r\n".
            '       '."\r\n".
            '#Timer {'."\r\n".
            '  z-index: 999;'."\r\n".
            '  position: absolute;'."\r\n".
            '  width: 100%;'."\r\n".
            '  right: 0;'."\r\n".
            '}'."\r\n".
            '.sp_box .retry-button:active {'."\r\n".
            '    background: #205B66;'."\r\n".
            '}'."\r\n".
            '.c_movies {'."\r\n".
            '  background-color: rgba(22, 8, 34, 0.57);'."\r\n".
            '  font-family: \'Open Sans\', sans-serif;'."\r\n".
            '  color: #FFF;'."\r\n".
            '  text-align: center;'."\r\n".
            '  margin: 0 auto;'."\r\n".
            '  width: 60px;'."\r\n".
            '}'
    );
}
?>