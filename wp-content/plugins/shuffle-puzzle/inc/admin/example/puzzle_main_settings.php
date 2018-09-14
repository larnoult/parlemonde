<?php
function puzzle_with_default_settings(){
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
        'sh_stop' => 'false',

        'duration' => '200',
        'bgColor' => '#fff',

        'w_img' => '400',
        'h_img' => '400',

        'bgOpacity' => '1',
        'imgBgOpacity' => '.2',
        'shuffleNum' => '5',
        'menuVisible' => 'true',

        'menuNameShuffle' => 'Shuffle',
        'menuNameGrid' => 'Grid',
        'menuNameImage' => 'Image',

        'menu_grid' => '3x3,4x4,5x5',

        'menu_shuffle' => 'Easy:10,Medium:30,Hard:60',
        'gl_var' => '',
        'onCompleted' => 'alert("Congratulations, %sp_name% you\'ve won!");',
        'onStart' => '',
        'onChange' => '',
        'afterCreate' => '',
        'firstStart' => '',
        'puzzle_style' => ''
    );
}
?>