(function($) {

	$.fn.twClick = function (click) {
		this.bind("touchstart", function (e) { click.call(this, e); e.stopPropagation(); e.preventDefault(); });
		this.bind("click", function (e) { click.call(this, e); });
		return this;
	};

	$.fn.extend({
		shufflepuzzle: function(options) {
			var defaults = {
				img_puzzle: 'img/puzzle.jpg',
				width: 400,
				height: 400,
				showStart: true,
				tilesH: 4,
				tilesV: 4,
				gap: true,
				auto_size: false,
				duration: 100,
				bgColor: '#fff',
				bgOpacity: 1,
				imgBgOpacity: .2,
				shuffleNum: 5,
				menuVisible: true,
				menuNameShuffle: 'Shuffle',
				menuNameGrid: 'Grid',
				menuNameImage: null,
				menu_shuffle:{
					Easy: 10,
					Medium: 30,
					Hard: 60
				},
				menu_grid: ['3x3', '4x4', '5x5'],
				onStart : null,
				onChange : null,
				afterCreate : null,
				onCompleted : null,
				firstStart : false,
				stop : false
			};

			//var options = $.extend(defaults, options);
			var rand = Math.round(Math.random() * 999999);
			var _Completed = false;

			return this.each(function() {
				
				$.fn.shufflepuzzle[$(this).attr("class")] = function (opt) {
					$('.img_title' + rand).remove();
					o = $.extend(o, opt);
					loading_vis(1);
				};

				var o = $.extend(defaults, options),
					tiles = [],
					tilesShuffle = [],
					grid = [],
					sum = o.tilesV*o.tilesH,
					lineWidth = o.gap ? 1 : 0,
					i = 0,
					nu = 0,
					ii = false,
					cl = sum-1,
					cl1 = -1,
					cl2 = cl,
					i_w = 0,
					i_h = 0,
					obj = $(this),
					m_shuffle = '',
					m_grid = '',
					m_img = '',
					ratio = 1,
					_firstStart = o.firstStart,
					duration_ = o.duration,
					vs_h, vs_v;

				if(!o.showStart){
					o.duration = 1;
				}
				
				ratio = o.width/o.height;

				var img = new Image();
				img.onload = function() {
					StartPuzzle();
				};

				if(o.startImage){
					for (i in o.menu_image){
						if(nu == o.startImage){
							o.img_puzzle = o.menu_image[i];
							break;
						}
						nu++;
					}
					o.img_puzzle = o.menu_image[i];
				}else{
					for (i in o.menu_image) break;
					o.img_puzzle = o.menu_image[i];
				}

				img.src = o.img_puzzle;

				function loading_vis (a) {
					if(!o.showStart){
						o.duration = 1;
					}
					if(a){
						$('#imgbg' + rand).attr('src', o.img_puzzle);
					}
					Create();
				}
				
				function StartPuzzle(){
					for(var key in o.menu_shuffle) {
						m_shuffle += '<li>'+key+'</li>';
					}
					for (var i = 0; i < o.menu_grid.length; i++) {
						m_grid += '<li>'+o.menu_grid[i]+'</li>';
					}
					if(!o.control_image){
						for (var key in o.menu_image) {
							m_img += '<li>'+key+'</li>';
						}
					}

					var mNS ='';
					var mNG='';
					var mNI='';

					if(o.menuNameShuffle){
						mNS = '<li>'+o.menuNameShuffle+'<ul class="pz_shuffle' + rand + '">'+m_shuffle+'</ul></li>';
					}

					if(o.menuNameGrid){
						mNG = '<li>'+o.menuNameGrid+'<ul class="pz_grid' + rand + '">'+m_grid+'</ul></li>';
					}

					if(o.menuNameImage){
						mNI = '<li>'+o.menuNameImage+'<ul class="pz_img' + rand + '">'+m_img+'</ul></li>';
					}

					$(obj).width(o.width).height(o.height);
					$(obj).prepend('<div class="sp_bg visible" id="bg' + rand + '"><img class="imgbg" id="imgbg' + rand + '" width="' + o.width + '" height="' + o.height + '" src="' + o.img_puzzle + '" /></div>');
					$(obj).addClass("sh_puzzle");

					$('#imgbg' + rand).css({
						"transition": "opacity "+duration_+"ms ease-out"
					})

					if (o.menuVisible){
						$(obj).before('<div class="spmenu" id="menu' + rand + '">'+
							'<ul id="puzzle-navigation">' + mNS + mNG + mNI + '</ul>'+
						'</div>');
					}

					$('ul.pz_shuffle' + rand + ' li').twClick(function(e) {
						if(e.button != 2){
							$('.img_title' + rand).remove();
							o.shuffleNum = o.menu_shuffle[$(this).text()];
							loading_vis();
						}
					});

					$('ul.pz_grid' + rand + ' li').twClick(function(e) {
						if(e.button != 2){
							$('.img_title' + rand).remove();
							grid = $(this).text().replace(/[^A-Za-z0-9#:;-]/g,'').split('x');
							o.tilesV = parseInt(grid[0]);
							o.tilesH = parseInt(grid[1]);
							sum = o.tilesV*o.tilesH;
							loading_vis();
						}
					});

					$('ul.pz_img' + rand + ' li').twClick(function(e) {
						if(e.button != 2 && o.img_puzzle != o.menu_image[$(this).text()]){
							$('.img_title' + rand).remove();
							var ss = o.menu_image[$(this).text()];
							img.src = o.img_puzzle = ss;
							img.onload = function() {
								loading_vis(1);
							}
						}
					});

					$('#bg' + rand).css({
						'background-color': o.bgColor,
						'opacity': o.bgOpacity
					}).twClick(function(e) {
						var evt = e || event;
						(evt.preventDefault) ? evt.preventDefault() : evt.returnValue = false;
					});

					/*$(obj).bind('mousedown touchstart', function(e) {
						var evt = e || event;
						(evt.preventDefault) ? evt.preventDefault() : evt.returnValue = false;
					});*/

					$('#menu' + rand).css({
						'color': 'white',
						'position': 'absolute',
						'z-index': 99
					});


					if(_firstStart){
						o.firstStart();
						_firstStart = false;
					}
					if(!o.stop){
						Create();
					}

					sp_rezize();
					$(window).resize(function(){
						sp_rezize();
					});
				}

				function bg_show() {
					$('#bg' + rand).removeClass('visible').css({
						'background-color': o.bgColor,
						'opacity': o.bgOpacity
					});
				}
				
				function Create(){

					vs_h = Math.floor((o.width - (Math.floor(o.width/o.tilesH)*o.tilesH))/2);
					vs_v = Math.floor((o.height - (Math.floor(o.height/o.tilesV)*o.tilesV))/2);

					ii = false;
					cl = sum-1;
					cl1 = -1;
					cl2 = cl;

					_Completed=false;

					tiles = [];
					tilesShuffle = [];

					if(o.showStart){
						bg_show();
						$('#imgbg' + rand).css({
							'opacity': o.imgBgOpacity
						});
					}

					for(i_h=0; i_h<sum; i_h++){
						tiles.push(i_h);
						tilesShuffle.push(i_h);				
					}
					
					i=0;


					for(i_h=0; i_h<o.tilesV; i_h++){
						for(i_w=0; i_w<o.tilesH; i_w++){
							$(obj).prepend('<div class="anim img_title' + rand+'" id="img_num' + i + rand + '"><img id="imgb' + i + rand +'" width="'+o.width+'" height="'+o.height+'" src="'+o.img_puzzle+'"></div>');
							$('#imgb' + i + rand).css({
								'margin-top': '-'+(vs_v+i_h*Math.floor(o.height/o.tilesV))+'px',
								'margin-left': '-'+(vs_h+i_w*Math.floor(o.width/o.tilesH))+'px',
								'max-width': 'none'
							});
							
							$('#img_num' + i + rand).css({
								'width': Math.floor(o.width/o.tilesH)-lineWidth + 'px',
								'height': Math.floor(o.height/o.tilesV)-lineWidth + 'px',
								'position': 'absolute',
								'overflow': 'hidden',
								'left': vs_h+i_w*Math.floor(o.width/o.tilesH) + 'px',
								'top': vs_v+i_h*Math.floor(o.height/o.tilesV) + 'px',
								'z-index': 1
							}).data({
								'name': tiles[i],
								'etalon': tiles[i]
							}).twClick(function(e) {
								if(_Completed){
									return false;
								}

								if(e.button != 2){
									if( ($(this).data().name+1 == cl && cl % o.tilesH != 0) || ($(this).data().name-1 == cl && cl % o.tilesH != o.tilesH-1)){
										cl2 = $(this).data().name;
										$(this).data('name', cl).css({
											'left': vs_h+(cl%o.tilesH * Math.floor(o.width/o.tilesH))
										});
										cl = cl2;
										if(o.onChange) {
											o.onChange();
										}
									}else if($(this).data().name+o.tilesH == cl || $(this).data().name-o.tilesH == cl){
										cl2 = $(this).data().name;
										$(this).data('name', cl).css({
											'top': vs_v+(Math.floor(cl/o.tilesH) * Math.floor(o.height/o.tilesV))
										});
										cl = cl2;
										if(o.onChange){
											o.onChange();
										}
									}
									
									if (Sort()){
										$('#imgbg' + rand).css('opacity', 1);
										$('#bg' + rand).addClass('visible');

										if(o.onCompleted){
											_Completed=true;
											setTimeout(function() {o.onCompleted()}, o.duration+50)
										};
									}
								}
							});
							i++;
							if(i==sum-1){
								i_w = o.tilesH;
							}
						}
					}


					if(o.showStart){
						$(".sh_puzzle > .anim").css({
							"transition": "all "+o.duration+"ms ease-out"
						});
					}else{
						$(".sh_puzzle > .anim").css({
							"opacity": 0
						});
					}
					cl = tiles[tiles[sum-1]];
					var m_rand=0;
					i=0;

					if(o.shuffleNum!=0){
						setTimeout(Step, 100);
					}else{
						return false;
					}
					function Step(){
						m_rand = Math.round(Math.random()*(sum-2));
						var parm_n = $('#img_num' + m_rand + rand).data().name;
						ii=false;
						while(!ii){
							if(cl1!=parm_n){
								if( (parm_n+1 == cl && cl % o.tilesH != 0) || (parm_n-1 == cl && cl % o.tilesH != o.tilesH-1) ){
									cl2 = parm_n;
									cl1 = cl;
									$('#img_num' + m_rand + rand).data('name', cl)
									.css({
										'left': vs_h+(cl%o.tilesH * Math.floor(o.width/o.tilesH))
									});
									Comp()
									cl = cl2;
									ii = true;
								}else if(parm_n+o.tilesH == cl || parm_n-o.tilesH == cl){						
									cl2 = parm_n;
									cl1 = cl;
									$('#img_num' + m_rand + rand).data('name', cl)
									.css({
										'top': vs_v+(Math.floor(cl/o.tilesH) * Math.floor(o.height/o.tilesV))
									});
									Comp()
									cl = cl2;
									ii = true;
								}
							}
							m_rand = Math.round(Math.random()*(sum-2));
							parm_n = $('#img_num' + m_rand + rand).data().name;
						}
						i++;
					}

					function Comp() {
						if(i<o.shuffleNum-1){
							setTimeout(Step, o.duration/5);
						}else{
							if(!o.showStart){
								$(".sh_puzzle > .anim").css({
									"transition": "all "+duration_+"ms ease-out"
								});

								$('#imgbg' + rand).css({
									'opacity': o.imgBgOpacity
								});
										
								setTimeout(function () {
									o.duration = duration_;
									$(".sh_puzzle > .anim").css("opacity", 1);
								}, duration_);
							}

							if(o.afterCreate){
								o.afterCreate();
							}

							if(!o.showStart){
								bg_show();
							}
						}
					}
					function Sort() {
						for(i_h=0; i_h<sum-1; i_h++){
							if ($('#img_num' + i_h + rand).data().etalon == $('#img_num' + i_h + rand).data().name == false) {
								return false;
							}
						}
						return true;
					}

					sp_rezize();
					

					if(o.onStart){
						o.onStart();
					}
				}

				function sp_rezize() {
					if(o.auto_size){
						o.width = $(obj).parent().width();
						o.height = o.width/ratio;
					}
					$(obj).width(o.width).height(o.height);
					$('#imgbg' + rand).width(o.width).height(o.height);

					vs_h = Math.floor((o.width - (Math.floor(o.width/o.tilesH)*o.tilesH))/2);
					vs_v = Math.floor((o.height - (Math.floor(o.height/o.tilesV)*o.tilesV))/2);
					nu=0;
					for(i_h=0; i_h<o.tilesV; i_h++){
						for(i_w=0; i_w<o.tilesH; i_w++){
							$('#imgb' + nu + rand).css({
								width: o.width,
								height: o.height,
								'margin-top': -(vs_v+i_h*Math.floor(o.height/o.tilesV)),
								'margin-left': -(vs_h+i_w*Math.floor(o.width/o.tilesH))
							});


							$('#img_num' + nu + rand).css({
								'width': Math.floor(o.width/o.tilesH)-lineWidth,
								'height': Math.floor(o.height/o.tilesV)-lineWidth,
								'left': vs_h+($('#img_num' + nu + rand).data('name')%o.tilesH * Math.floor(o.width/o.tilesH)),
								'top': vs_v+(Math.floor($('#img_num' + nu + rand).data('name')/o.tilesH) * Math.floor(o.height/o.tilesV))
							});
							nu++;
						}
					}
				};
			});
		}
	});
})(jQuery);