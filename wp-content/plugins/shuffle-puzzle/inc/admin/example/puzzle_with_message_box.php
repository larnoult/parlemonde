<?php
function puzzle_with_message_box(){
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
		'auto_size' => 'true',
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
		'gl_var' => '',
		'onCompleted' => 'jQuery("p.mini").removeClass(\'mini\');'."\r\n".
			'jQuery(".%sp_name% .sp_box .retry-button.bt1").html(\'Try again\');'."\r\n".
			'jQuery(".%sp_name% .message-box > div > p").html("Congratulations!");'."\r\n".
			'jQuery(".%sp_name% .sp_box").addClass(\'visible\');'."\r\n".
			'jQuery(".%sp_name% .retry-button.bt2").addClass(\'visible\');',

		'onStart' => 'jQuery(".sp_box").removeClass(\'visible\');',
		'afterCreate' => '',
		'onChange' => '',

		'firstStart' => 'var images = [],'."\r\n".
            'num_img = 0,'."\r\n".
            'sh_num = 5;'."\r\n\r\n".
			'for(var key in this.menu_image) {'."\r\n".
			'  if (!this.menu_image.hasOwnProperty(key)) continue'."\r\n".
			'  images.push(this.menu_image[key]);'."\r\n".
			'}'."\r\n\r\n".
			'jQuery(\'.%sp_name%\').append(\'<div class="sp_box"><div class="message-box"><div><p class="mini">Gather the Suffle Puzzle</p><div class="retry-button bt1">Start</div><div class="retry-button bt2">Next</div><br></div></div></div>\');'."\r\n".
			'jQuery(".%sp_name%>.sp_box").addClass(\'visible\');'."\r\n\r\n".
			'jQuery(".%sp_name% .retry-button.bt1").bind(\'mousedown touchstart\', function(e) {'."\r\n".
			'  jQuery(\'.%sp_name%\').shufflepuzzle[\'%sp_name%\']();'."\r\n".
			'});'."\r\n\r\n".
			'jQuery(".%sp_name% .retry-button.bt2").bind(\'mousedown touchstart\', function(e) {'."\r\n".
			'  sh_num++;'."\r\n".
			'  num_img++;'."\r\n".
			'  num_img = num_img%images.length;'."\r\n".
			'  jQuery(\'.%sp_name%\').shufflepuzzle[\'%sp_name%\']({'."\r\n".
			'    img_puzzle: images[num_img],'."\r\n".
			'    shuffleNum: sh_num'."\r\n".
			'  });'."\r\n".
			'});',
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
            '}'."\r\n".
            '.sp_box.visible {'."\r\n".
            '  visibility:visible;'."\r\n".
            '  opacity:1;'."\r\n".
            '  transition-delay:0s;'."\r\n".
            '  z-index: 9999;'."\r\n".
            '}'."\r\n\r\n".
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
            '  text-align: center;'."\r\n".
            '  height:100%;      '."\r\n".
            '  width: 100%;  '."\r\n".
            '  display: table-cell;      '."\r\n".
            '  vertical-align: middle;'."\r\n".
            '}'."\r\n\r\n".
            '.sp_box .retry-button  {'."\r\n".
            '  display: inline-block;'."\r\n".
            '  background: #F3A809;'."\r\n".
            '  border-bottom: 6px solid #BE7211;'."\r\n".
            '  font-size: 22px;'."\r\n".
            '  padding: 0 20px 5px;'."\r\n".
            '  text-decoration: none;'."\r\n".
            '  color: #FFFFFF;'."\r\n".
            '  height: 48px;'."\r\n".
            '  line-height: 40px;'."\r\n".
            '  cursor: pointer;'."\r\n".
            '  margin-left: 9px;'."\r\n".
            '  box-sizing: content-box;'."\r\n".
            '}'."\r\n\r\n".
            '.sp_box .retry-button:active {'."\r\n".
            '    background: #BE7211;'."\r\n".
            '}'."\r\n\r\n".
            '.sp_box > div > div > .mini {'."\r\n".
            '  font-size: 26px;'."\r\n".
            '  line-height: 40px;'."\r\n".
            '}'."\r\n\r\n".
            '.sp_box .retry-button.bt2{'."\r\n".
            '  display: none;'."\r\n".
            '}'."\r\n".
            '.sp_box .retry-button.bt2.visible{'."\r\n".
            '  display: inline-block;'."\r\n".
            '}'."\r\n\r\n".
            '.sp_box > div > div > iframe{'."\r\n".
            '  position: absolute;'."\r\n".
            '  bottom: 10px;'."\r\n".
            '  left: 10px;'."\r\n".
            '}'."\r\n".
            '.sp_box > div > div > iframe.bt_facebook{'."\r\n".
            '  bottom: 34px;'."\r\n".
            '}'
    );
}
?>