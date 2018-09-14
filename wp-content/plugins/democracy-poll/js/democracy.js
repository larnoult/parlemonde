// @koala-prepend "_js-cookie.js"

function dem_ready($){
	'use strict';

	var demmainsel = '.democracy',
		$dems      = $(demmainsel),
		demScreen  = '.dem-screen', // селектор контейнера с результатами
		userAnswer = '.dem-add-answer-txt', // класс поля free ответа
		loader,
		$demLoader = $('.dem-loader').first(),
		Dem = {};

	if( ! $dems.length ) return; // чтобы никому не мешать...

	Dem.opts          = $dems.first().data('opts');
	Dem.ajaxurl       = Dem.opts.ajax_url;
	Dem.answMaxHeight = Dem.opts.answs_max_height;
	Dem.speed         = parseInt( Dem.opts.anim_speed ); // animation speed
	Dem.lineAnimSpeed = parseInt( Dem.opts.line_anim_speed ); // line animation

	// INIT (подождем функции) --------------
	setTimeout(function(){
		// Основные события Democracy для всех блоков
		var $demScreens         = $dems.find( demScreen ).filter(':visible'),
			demScreensSetHeight = function(){
				$demScreens.each(function(){
					Dem.setHeight( $(this), 1 );
				});
			};

		$demScreens.demInitActions(1);

		$(window).on('resize.demsetheight', demScreensSetHeight ); // высота при ресайзе

		$(window).load(demScreensSetHeight); // высота еще раз

		Dem.maxAnswLimit(); // ограничение выбора мульти ответов

		/*
		 * Обработка кэша.
		 * Нужен установленный js-cookie
		 * и дополнительные js переменные и методы самого Democracy.
		 */
		var $cache = $('.dem-cache-screens');
		if( $cache.length > 0 ){
			//console.log('Democracy cache gear ON');
			$cache.demCacheInit();
		}

	}, 10);


	// Инициализация всех событий связаных с внутренней частью каждого опроса: клики, высота, скрытие кнопки
	// применяется на '.dem-screen'
	$.fn.demInitActions = function( noanimation ){
		return this.each(function(){
			// Устанавливает события клика для всех помеченных элементов в переданом элементе:
			 // тут и AJAX запрос по клику и другие интерактивные события Democracy ----------
			var $this = $(this),
				attr = 'data-dem-act';

			$this.find('['+ attr +']').each(function(){
				$(this).attr('href','') // удалим УРЛ чтобы не было видно УРЛ запроса

				$(this).click(function(e){
					e.preventDefault();
					$(this).blur().demDoAction( $(this).attr( attr ) );
				});
			});

			// Прячем кнопку сабмита, где нужно ------------
			var autoVote = !! $this.find('input[type=radio][data-dem-act=vote]').first().length;
			if( autoVote ) $this.find('.dem-vote-button').hide();

			// прячем внутряк если слишком много вариантов ответа
			Dem.setAnswsMaxHeight( $this );

			// анимация заполненных граф - line_animatin
			if( Dem.lineAnimSpeed ){
				$this.find('.dem-fill').each(function(){
					var $fill = $(this);
					//setTimeout(function(){ fill.style.width = was; }, Dem.speed + 500); // на базе CSS transition - при сбросе тоже срабатывает и мешает...
					setTimeout(function(){
						$fill.animate({ width: $fill.data('width') }, Dem.lineAnimSpeed);
					}, Dem.speed, "linear");
				});
			}

			// Устанавливает высоту жестко ------------
			// Вешаем все на ресайз окна. Мобильники переворачиваются...
			Dem.setHeight( $this, noanimation );

			// событие сабмина формы
			$this.find('form').submit(function(e){
				e.preventDefault();

				var act = $(this).find('input[name="dem_act"]').val();
				if( act )
					$(this).demDoAction( $(this).find('input[name="dem_act"]').val() );
			});
		});
	};

	// Loader
	$.fn.demSetLoader = function(){
		var $the = this;

		if( $demLoader.length )
			$the.closest(demScreen).append( $demLoader.clone().css('display','table') );
		else
			loader = setTimeout( function(){ Dem.demLoadingDots($the); }, 50 ); // dots
		return this;
	};
	$.fn.demUnsetLoader = function(){
		if( $demLoader.length ) this.closest(demScreen).find('.dem-loader').remove();
		else clearTimeout( loader );
		return this;
	};

	// Добавить ответ пользователя (ссылка)
	$.fn.demAddAnswer = function(){
		var $the = this.first(),
			$demScreen  = $the.closest( demScreen ),
			isMultiple  = $demScreen.find('[type=checkbox]').length > 0,
			$input	  = $('<input type="text" class="'+ userAnswer.replace(/\./,'') +'" value="">'); // поле добавления ответа

		// покажем кнопку голосования
		$demScreen.find('.dem-vote-button').show();

		// обрабатывает input radio деселектим и вешаем событие клика
		$demScreen.find('[type=radio]').each(function(){
			$(this).click(function(){
				$the.fadeIn(300);
				$( userAnswer ).remove();
			});

			if( $(this)[0].type == 'radio' ) this.checked = false; // uncheck
		});

		$the.hide().parent('li').append( $input );
		$input.hide().fadeIn(300).focus(); // animation

		// добавим кнопку удаления пользовательского текста
		if( isMultiple ){
			var $ua = $demScreen.find( userAnswer );
			$('<span class="dem-add-answer-close">×</span>')
			.insertBefore( $ua )
			.css('line-height', $ua.outerHeight() + 'px' )
			.click( function(){
				var $par = $(this).parent('li');
				$par.find('input').remove();
				$par.find('a').fadeIn(300);
				$(this).remove();
			} );
		}

		return false; // !!!
	};

	// Собирает ответы и возращает их в виде строки
	$.fn.demCollectAnsw = function(){
		var $form	 = this.closest('form'),
			$answers  = $form.find('[type=checkbox],[type=radio],[type=text]'),
			userText  = $form.find( userAnswer ).val(),
			answ	  = [],
			$checkbox = $answers.filter('[type=checkbox]:checked');

		// multiple
		if( $checkbox.length > 0 ){
			$checkbox.each(function(){
				answ.push( $(this).val() );
			});
		}
		// single
		else {
			var str = $answers.filter('[type=radio]:checked');
			if( str.length )
				answ.push( str.val() );
		}
		// user_added
		if ( userText ){
			answ.push( userText );
		}

		answ = answ.join('~');

		return answ ? answ : '';
	};

	// обрабатывает запросы при клике, вешается на событие клика
	$.fn.demDoAction = function( act ){
		var $the = this.first(),
			$dem = $the.closest( demmainsel ),
			data = {
				dem_pid: $dem.data('opts').pid,
				dem_act: act,
				action: 'dem_ajax'
			};

		if( typeof data.dem_pid === 'undefined' ){
			console.log('Poll id is not defined!');
			return false;
		}

		// Соберем ответы
		if( act == 'vote' ){
			data.answer_ids = $the.demCollectAnsw();
			if( ! data.answer_ids ){
				Dem.demShake( $the );
				return false;
			}
		}

		// кнопка переголосовать, подтверждение
		if( act == 'delVoted' && ! confirm( $the.data('confirm-text') ) )
			return false;

		// кнопка добавления ответа посетителя
		if( act == 'newAnswer' ){
			$the.demAddAnswer();
			return false;
		}

		// AJAX
		$the.demSetLoader();
		$.post( Dem.ajaxurl, data,
			function( respond ){
				$the.demUnsetLoader();

				// устанавливаем все события
				$the.closest( demScreen ).html( respond ).demInitActions();
			}
		);

		return false;
	};


	// КЭШ ------------
	// показывает заметку
	$.fn.demCacheShowNotice = function( type ){
		var $the = this.first(),
			$notice = $the.find('.dem-youarevote').first(); // "уже голосовал"

		// Если могут овтечать только зарегистрированные
		if( type == 'blockForVisitor' ){
			$the.find('.dem-revote-button').remove(); // удаляем переголосовать
			$notice = $the.find('.dem-only-users').first();
		}

		$the.prepend( $notice.show() );
		// hide
		setTimeout( function(){ $notice.slideUp('slow'); }, 10000 );

		return this;
	};

	// устанавливает ответы пользователя в блоке результатов/голосования
	Dem.cacheSetAnswrs = function( $screen, answrs ){
		var aids = answrs.split(/,/);

		// если результаты
		if( $screen.hasClass('voted') ){
			var $dema	   = $screen.find('.dem-answers'),
				votedClass = $dema.data('voted-class'),
				votedtxt   = $dema.data('voted-txt');

			$.each( aids, function(key,val){
				$screen.find('[data-aid="'+ val +'"]')
					.addClass( votedClass )
					.attr('title', function(){ return votedtxt + $(this).attr('title'); } );
			});

			// уберем кнопку "Голосовать"
			$screen.find('.dem-vote-link').remove();
		}

		// если голосование
		else {
			var $answs    = $screen.find('[data-aid]'),
				$btnVoted = $screen.find('.dem-voted-button');

			// устанавливаем ответы
			$.each( aids, function(key,val){
				$answs.filter('[data-aid="'+ val +'"]').find('input').prop('checked','checked');
			});

			// все деактивирем
			$answs.find('input').prop('disabled','disabled');

			// уберем голосовать
			$screen.find('.dem-vote-button').remove();
			//$screen.find('[data-dem-act="vote"]').remove();

			// если есть кнопка "уже логосовали", то переголосование запрещено
			if( $btnVoted.length ){
				$btnVoted.show();
			}
			// показываем кнопку переголосовать
			else {
				$screen.find('input[value="vote"]').remove(); // чтобы можно было переголосовать
				$screen.find('.dem-revote-button-wrap').show();
			}

		}

	};

	$.fn.demCacheInit = function(){
		return this.each(function(){
			var $the = $(this),
				$dem   = $the.prev( demmainsel );

			// ищем главный блок
			if( ! $dem.length )
				$dem = $the.closest( demmainsel );
			if( ! $dem.length ){
				console.log('Main dem div not found');
				return;
			}

			var $screen     = $dem.find( demScreen ).first(), // основной блок результатов
				dem_id      = $dem.data('opts').pid,
				answrs      = Cookies.get('demPoll_' + dem_id),
				notVoteFlag = ( answrs == 'notVote' ) ? true : false, // Если уже проверялось, что пользователь не голосовал, не отправляем запрос еще раз
				isAnswrs    = !(typeof answrs == 'undefined') && ! notVoteFlag;

			// обрабатываем экраны, какой показать и что делать при этом
			var voteHTML  = $the.find( demScreen + '-cache.vote' ).html(),
				votedHTML = $the.find( demScreen + '-cache.voted' ).html();

			// если опрос закрыт должны кэшироваться только результаты голосования. Просто выходим.
			if( ! voteHTML )
				return;

			// устанавливаем нужный кэш
			// если закрыт просмотрт ответов
			var setVoted = isAnswrs && votedHTML;
			$screen.html( ( setVoted ? votedHTML : voteHTML ) + '<!--cache-->' )
				.removeClass('vote voted')
				.addClass( setVoted ? 'voted' : 'vote' );


			if( isAnswrs )
				Dem.cacheSetAnswrs( $screen, answrs );

			$screen.demInitActions(1);

			if( notVoteFlag )
				return; // если уже проверялось, что пользователь не голосовал, выходим

			// Если голосов нет в куках и опция плагина keep_logs включена,
			// отправляем запрос в БД на проверку, по событию (наведение мышки на блок),
			if( ! isAnswrs && $the.data('opt_logs') == 1 ){
				var tmout,
					notcheck = function(){ clearTimeout( tmout ); },
					check	= function(){
						tmout = setTimeout( function(){
							// Выполняем один раз!
							if( $dem.hasClass('checkAnswDone') ) return;
							$dem.addClass('checkAnswDone');

							var $forDotsLoader = $dem.find('.dem-link').first();
							$forDotsLoader.demSetLoader();
							$.post( Dem.ajaxurl,
								{
									dem_pid: $dem.data('opts').pid,
									dem_act: 'getVotedIds',
									action:  'dem_ajax'
								},
								function( reply ){
									$forDotsLoader.demUnsetLoader();
									if( ! reply ) return; // выходим если нет ответов

									$screen.html( votedHTML );
									Dem.cacheSetAnswrs( $screen, reply );

									$screen.demInitActions();

									// сообщение, что голосовал или только для пользователей
									$screen.demCacheShowNotice( reply );
								}
							);
						}, 700 ); // 700 для оптимизации, чтобы моментально не отправлялся запрос, если мышкой просто провели по опросу...
					};
				$dem.hover( check, notcheck );
				$dem.click( check );
			}

		});
	};


	// ФУНКЦИИ --------------

	// Определяет высоту указанного элемента при свойстве - height:auto
	Dem.detectRealHeight = function( $el ){
		// получим нужную высоту
		var $_el = $el.clone().css({height:'auto'}).insertBefore( $el ), // insertAfter не подходит - глюк какой-то
			realHeight = ($_el.css('box-sizing') == 'border-box') ? parseInt( $_el.css('height') ) : $_el.height();
		$_el.remove();

		//console.log($_el.css('height'), $_el.height(), $_el[0]);
		//setTimeout(function(){ console.log($_el.css('height'), $_el.height(), $_el[0]); }, 0);

		return realHeight;
	}

	// Устанавливает высоту жестко
	Dem.setHeight = function( $that, noanimation ){
		// получим нужную высоту
		var newH = Dem.detectRealHeight( $that )

		// Анимируем до нужной выстоты
		if( ! noanimation  )
			$that.css({ opacity:0 }).animate({ height: newH }, Dem.speed, function(){ $(this).animate({opacity:1}, Dem.speed*1.5); } );
		else
			$that.css({ height: newH });
	};

	// ограничение по высоте
	Dem.setAnswsMaxHeight = function( $that ){
		if( Dem.answMaxHeight === '-1' || Dem.answMaxHeight === '0' || ! Dem.answMaxHeight )
			return;

		var $el = $that.find('.dem-vote, .dem-answers').first(),
			maxHeight = /*parseInt( $el.css('max-height') ) ||*/ parseInt( Dem.answMaxHeight );

		$el.css({ "max-height":'none', "overflow-y":'visible' }); // сбросим если установлено

		var elHeight = ($el.css('box-sizing') == 'border-box') ? parseInt( $el.css('height') ) : $el.height();

		// сворачиваем, если больше чем максимальная высота и разница больше 100px - 100px прятать не резон...
		var diff = elHeight - maxHeight;
		if( diff > 100 ){
			$el.css('position', 'relative');

			var $overlay    = $('<span class="dem__collapser"><span class="arr"></span></span>').appendTo( $el ),
				fn__expand  = function(){ $overlay.addClass('expanded').removeClass('collapsed'); },
				fn__collaps = function(){ $overlay.addClass('collapsed').removeClass('expanded'); },
				timeout;

			// не сворачиваем, если было развернуто
			if( $that.data('expanded') ){
				fn__expand();
			}
			else {
				fn__collaps();
				$el.height( maxHeight ).css('overflow-y', 'hidden');
			}

			// клик на hover, чтобы не нужно было кликать для разворачивания
			$overlay.hover(
				function(){ if( ! $that.data('expanded') ) timeout = setTimeout(function(){ $overlay.trigger('click') }, 1000); },
				function(){ clearTimeout( timeout ); }
			);

			$overlay.click(function(){
				clearTimeout( timeout );

				// collapse
				if( $that.data('expanded') ){
					fn__collaps();

					$that.data('expanded', false);
					$that.height('auto'); // чтобы контейнер плавно передвигался вместе с внутяком, в конеце вернем ему высоту
					$el.stop().css('overflow-y', 'hidden').animate({ height:maxHeight }, Dem.speed, function(){
						Dem.setHeight( $that, true );
					});
				}
				// expand
				else{
					fn__expand();

					// определим высоту без скрытия
					var newH = Dem.detectRealHeight( $el );
					newH += 7; // запас для "добавить свой ответ"

					$that.data('expanded', true);
					$that.height('auto'); // чтобы контейнер плавно передвигался вместе с внутяком, в конеце вернем ему высоту
					$el.stop().animate({ height:newH }, Dem.speed, function(){
						Dem.setHeight( $that, true );
						$el.css('overflow-y', 'visible');

					});
				}
			});
		}

	}

	// max answers limit
	Dem.maxAnswLimit = function(){
		$dems.on('change', '[type=checkbox]', function( ev ){
			var maxAnsws   = $(this).closest( demmainsel ).data('opts').max_answs,
				$checkboxs = $(this).closest( demScreen ).find('[type=checkbox]'),
				$checked   = $checkboxs.filter(':checked').length,
				foo;

			if( $checked >= maxAnsws )
				$checkboxs.filter(':not(:checked)').each(function(){
					$(this).prop('disabled','disabled').closest('li').addClass('dem-disabled');
				});
			else
				$checkboxs.each(function(){
					$(this).removeProp('disabled').closest('li').removeClass('dem-disabled');
				});
		});
	};

	Dem.demShake = function( $that ){
		var a = $that.css("position");
		a && "static" !== a || $that.css("position","relative");
		for( a=1; 2>=a; a++ )
			$that.animate({left:-10},50).animate({left:10},100).animate({left:0},50);
	};

	// dots loading animation - ...
	Dem.demLoadingDots = function( $el ){
		var $the = $el,
			isInput = $the.is('input'),
			str = (isInput) ? $the.val() : $the.html();

		if( str.substring(str.length-3) === '...' ){
			if( isInput )
				$the[0].value = str.substring(0, str.length-3);
			else
				$the[0].innerHTML = str.substring(0, str.length-3);
		}
		else{
			if( isInput )
				$the[0].value += '.';
			else
				$the[0].innerHTML += '.';
		}

		loader = setTimeout( function(){ Dem.demLoadingDots($the); }, 200 );
	};

}

// wait for jQuery
var demjquerywait = setInterval(function(){
	if( typeof jQuery !== 'undefined' ){
		clearInterval( demjquerywait );

		jQuery(document).ready( dem_ready );
	}
}, 50);



