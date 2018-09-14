;(function ($, window, document, undefined) {

    var tours = [];

    // API

    $.wcpTourRegister = function(options) {
        var t = new WCPTour(options);
        tours[options.name] = t;
    }
    $.wcpTourStart = function(tourName) {
        tours[tourName].start();
    }
    $.wcpTourRestart = function(tourName) {
        tours[tourName].restart();
    }
    $.wcpTourIsFinished = function(tourName) {
        return tours[tourName].isFinished();
    }

    // API Events

    $.wcpTourEventStepWillChange = function(stepName) {

    }
    $.wcpTourEventStepDidChange = function(stepName) {

    }
    $.wcpTourEventStarted = function() {

    }
    $.wcpTourEventFinished = function() {

    }
    $.wcpTourCoordinatesForTipForStep = function(stepName) {
        return undefined;
    }
    $.wcpTourCoordinatesForHighlightRect = function(stepName) {
        return undefined;
    }

    function WCPTour(options) {
        this.id = Math.floor(Math.random() * 99999) + 1;
        this.options = options;
        this.currentStep = -1;
        this.reachedStep = -1;

        // DOM
        this.root = undefined;

        this.init();
    }
    WCPTour.prototype.init = function() {
        if (!localStorage[this.options.name]) {
            localStorage[this.options.name] = -1;
        }
    };
    WCPTour.prototype.start = function() {
        if (localStorage[this.options.name] == 'finished') {
            // Tour finished
            return;
        } else if (localStorage[this.options.name] >= 0) {
            // Tour is still active
            this.reachedStep = -1;
            this.currentStep = -1;
            this.drawContent();
            this.presentStep(localStorage[this.options.name]);

            // Send event
            $.wcpTourEventStarted();
        } else {
            // Tour has not started yet
            this.reachedStep = -1;
            this.drawContent();
            this.presentWelcomeScreen();

            // Send event
            $.wcpTourEventStarted();
        }
    };
    WCPTour.prototype.restart = function() {
        localStorage[this.options.name] = -1;
        this.start();
    };
    WCPTour.prototype.drawContent = function() {
        if (this.root) this.root.remove();

        var html = '';

        html += '<div id="wcp-tour-'+ this.id +'" class="wcp-tour-root">';
        html += '   <div class="wcp-tour-background">';
        html += '       <div class="wcp-tour-highlight-rect" id="wcp-tour-highlight-rect-1"></div>';
        html += '       <div class="wcp-tour-highlight-rect" id="wcp-tour-highlight-rect-2"></div>';
        html += '       <div class="wcp-tour-highlight-rect" id="wcp-tour-highlight-rect-3"></div>';
        html += '       <div class="wcp-tour-highlight-rect" id="wcp-tour-highlight-rect-4"></div>';
        html += '   </div>';

        // welcome screen

        html += '   <div class="wcp-tour-welcome-screen wcp-tour-centered-content">';
        html += '       <img class="wcp-tour-icon" src="data:image/png;base64,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">';
        html += '       <div class="wcp-tour-title">' + this.options.welcomeScreen.title + '</div>';
        html += '       <div class="wcp-tour-text">' + this.options.welcomeScreen.text + '</div>';
        html += '       <div class="wcp-tour-begin">' + this.options.welcomeScreen.startButtonTitle + '</div>';
        html += '       <div class="wcp-tour-skip">' + this.options.welcomeScreen.cancelButtonTitle + '</div>';
        html += '   </div>';

        // step
        html += '   <div class="wcp-tour-step wcp-tour-centered-content">';
        html += '       <div class="wcp-tour-step-nav"></div>';
        html += '       <div class="wcp-tour-step-title"></div>';
        html += '       <div class="wcp-tour-step-text"></div>';
        html += '       <div class="wcp-tour-step-click-anywhere">Click anywhere to continue</div>';
        html += '       <div class="wcp-tour-step-skip">Or skip this guide</div>';
        html += '   </div>';

        html += '</div>';

        $('body').append(html);

        if (!$('body').hasClass('wcp-tour-active')) {
            $('body').addClass('wcp-tour-active');
        }

        this.root = $('#wcp-tour-' + this.id);

        this.events();
    };
    WCPTour.prototype.events = function() {
        var self = this;

        this.root.find('.wcp-tour-begin').on('click', function() {
            // go to step 0
            self.presentStep(0);
        });
        this.root.find('.wcp-tour-skip').on('click', function() {
            // end tour
            self.endTour();
        });
        this.root.find('.wcp-tour-step-skip').on('click', function() {
            // end tour
            self.endTour();
        });
        $(document).off('click.' + this.id);
        $(document).on('click.' + this.id, function(e) {
            if ($(e.target).hasClass('wcp-tour-step') || $(e.target).hasClass('wcp-tour-step-title') || $(e.target).hasClass('wcp-tour-step-text') || $(e.target).hasClass('wcp-tour-step-click-anywhere')) {
                self.nextStep();
            }
        });
        $(document).off('click.' + this.id, '.wcp-tour-step-circle');
        $(document).on('click.' + this.id, '.wcp-tour-step-circle', function() {
            i = $(this).data('wcp-tour-circle-index');
            self.presentStep(i);
        });
        var visibleContainer = undefined;
        $(document).off('mouseover.' + this.id, '.wcp-tour-tip-media-button');
        $(document).on('mouseover.' + this.id, '.wcp-tour-tip-media-button', function() {
            visibleContainer = $(this).siblings('.wcp-tour-tip-media-container');
            visibleContainer.show();
            self.limitMediaContainer(visibleContainer);

            if (visibleContainer.find('video').length > 0) {
                visibleContainer.find('video').get(0).play();
            }
        });
        $(document).off('mousemove.' + this.id);
        $(document).on('mousemove.' + this.id, function(e) {
            if (visibleContainer && $(e.target).closest('.wcp-tour-tip-media-button').length == 0 && $(e.target).closest('.wcp-tour-tip-media-container').length == 0) {
                visibleContainer.hide();

                if (visibleContainer.find('video').length > 0) {
                    visibleContainer.find('video').get(0).pause();
                }

                visibleContainer = undefined;
            }
        });
        $(window).off('resize.wcp-tour');
        $(window).on('resize.wcp-tour', function() {
            $('.wcp-tour-highlight-rect').addClass('wcp-tour-highlight-rect-no-transition');
            self.updateHighlightRect();
            setTimeout(function() {
                $('.wcp-tour-highlight-rect').removeClass('wcp-tour-highlight-rect-no-transition');
            }, 10);
        });
    };
    WCPTour.prototype.nextStep = function() {
        if (parseInt(this.currentStep, 10) == this.options.steps.length - 1) {
            // done
            this.endTour();
        } else {
            // next step
            this.presentStep(parseInt(this.currentStep, 10) + 1);
        }
    };
    WCPTour.prototype.presentStep = function(stepIndex) {
        // is the step different?
        if (parseInt(this.currentStep, 10) == parseInt(stepIndex, 10)) return;

        if (this.currentStep == -1) {
            var self = this;

            // Hide welcome screen
            this.root.find('.wcp-tour-welcome-screen').css({ opacity: 0 });
            setTimeout(function() {
                self.root.find('.wcp-tour-welcome-screen').hide();
            }, 330);

            // Show step screen
            this.root.find('.wcp-tour-step').css({ display: 'flex' });
            setTimeout(function() {
                self.root.find('.wcp-tour-step').css({ opacity: 1 });
            }, 10);
        }


        localStorage[this.options.name] = stepIndex;
        this.currentStep = stepIndex;

        $.wcpTourEventStepWillChange(this.options.steps[this.currentStep].title);

        // Update navigation
        this.updateNav();

        // Set step content
        this.updateStep();

        // Update tip
        this.updateTip();

        // Send event
        $.wcpTourEventStepDidChange(this.options.steps[this.currentStep].title);
    };
    WCPTour.prototype.presentWelcomeScreen = function(stepIndex) {
        // Set current step to welcome screen
        localStorage[this.options.name] = -1;
        this.currentStep = -1;

        // Set the initial position of the highlight rects
        $('.wcp-tour-highlight-rect').addClass('wcp-tour-highlight-rect-no-transition');
        var rect = $.wcpTourCoordinatesForHighlightRect(this.options.steps[0].title);

        var windowWidth = $(window).width();
        var windowHeight = $(window).height();

        $('#wcp-tour-highlight-rect-1').css({
            left: 0,
            top: 0,
            width: '100%',
            height: rect.y + rect.height/2
        });
        $('#wcp-tour-highlight-rect-2').css({
            left: rect.x + rect.width/2,
            top: rect.y + rect.height/2,
            width: windowWidth - rect.x - rect.width/2,
            height: 0
        });
        $('#wcp-tour-highlight-rect-3').css({
            left: 0,
            top: rect.y + rect.height/2,
            width: '100%',
            height: windowHeight - rect.y - rect.height/2
        });
        $('#wcp-tour-highlight-rect-4').css({
            left: 0,
            top: rect.y + rect.height/2,
            width: rect.x + rect.width/2,
            height: 0
        });

        setTimeout(function() {
            $('.wcp-tour-highlight-rect').removeClass('wcp-tour-highlight-rect-no-transition');
        }, 50);
    };
    WCPTour.prototype.endTour = function(stepIndex) {
        // Set current step to welcome screen
        localStorage[this.options.name] = 'finished';
        this.currentStep = -1;

        // Send event
        $.wcpTourEventFinished();

        // Fade out root
        this.root.addClass('wcp-tour-hidden');
        var self = this;
        setTimeout(function() {
            self.root.remove();
            $('body').removeClass('wcp-tour-active');
        }, 330);
    };
    WCPTour.prototype.isFinished = function() {
        if (localStorage[this.options.name] == 'finished') {
            return true;
        }

        return false;
    };

    WCPTour.prototype.updateNav = function() {
        // does the number of circles need to be updated?
        while (parseInt(this.currentStep, 10) > this.reachedStep) {
            // this.reachedStep = parseInt(this.currentStep, 10);
            this.reachedStep++;

            // Add a new circle
            var html = '<div class="wcp-tour-step-circle" data-wcp-tour-circle-index="'+ this.reachedStep +'"><div class="wcp-tour-step-circle-inner">'+ (this.reachedStep + 1) +'</div></div>';
            this.root.find('.wcp-tour-step-nav').append(html);

            // Present the circle
            var self = this;
            setTimeout(presentCircle.bind(null, this.reachedStep), 10);
        }

        function presentCircle(index) {
            $('.wcp-tour-step-circle[data-wcp-tour-circle-index="'+ index +'"]').addClass('wcp-tour-circle-presented');
        }

        // set the currently active circle
        $('.wcp-tour-step-circle').removeClass('wcp-tour-circle-active');
        $('.wcp-tour-step-circle[data-wcp-tour-circle-index="'+ parseInt(this.currentStep, 10) +'"]').addClass('wcp-tour-circle-active');
    };
    WCPTour.prototype.updateStep = function() {
        this.root.find('.wcp-tour-step-title').html(this.options.steps[this.currentStep].title);
        this.root.find('.wcp-tour-step-text').html(this.options.steps[this.currentStep].text);
    };
    WCPTour.prototype.updateTip = function() {
        if (this.root.find('.wcp-tour-tip[data-wcp-tip-index='+ parseInt(this.currentStep, 10) +']').length == 0) {
            // Add HTML
            var html = '';

            html += '   <div class="wcp-tour-tip" data-wcp-tip-index="'+ parseInt(this.currentStep, 10) +'">';

            if (this.options.steps[this.currentStep].tip.position == 'bottom-left' || this.options.steps[this.currentStep].tip.position == 'bottom-right') {
                html += '       <div class="wcp-tour-tip-arrow"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAADTCAYAAABKv9f/AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MDQ0OUMxRjMxREE5MTFFNkIxNDlGNTZEN0E0QkIzQkUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MDQ0OUMxRjQxREE5MTFFNkIxNDlGNTZEN0E0QkIzQkUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowNDQ5QzFGMTFEQTkxMUU2QjE0OUY1NkQ3QTRCQjNCRSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDowNDQ5QzFGMjFEQTkxMUU2QjE0OUY1NkQ3QTRCQjNCRSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PvBYhb8AAAxKSURBVHja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"></div>';
                html += '       <div class="wcp-tour-clear"></div>';
            }

            html += '       <div class="wcp-tour-tip-media-button"></div>';
            html += '       <div class="wcp-tour-tip-title-wrap">';
            html += '           <div class="wcp-tour-tip-title"></div>';
            html += '           <div class="wcp-tour-tip-subtitle"></div>';
            html += '       </div>';

            if (this.options.steps[this.currentStep].tip.position == 'top-left' || this.options.steps[this.currentStep].tip.position == 'top-right') {
                html += '       <div class="wcp-tour-clear"></div>';
                html += '       <div class="wcp-tour-tip-arrow"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAADTCAYAAABKv9f/AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MDQ0OUMxRjMxREE5MTFFNkIxNDlGNTZEN0E0QkIzQkUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MDQ0OUMxRjQxREE5MTFFNkIxNDlGNTZEN0E0QkIzQkUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowNDQ5QzFGMTFEQTkxMUU2QjE0OUY1NkQ3QTRCQjNCRSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDowNDQ5QzFGMjFEQTkxMUU2QjE0OUY1NkQ3QTRCQjNCRSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PvBYhb8AAAxKSURBVHja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"></div>';
            }

            if (this.options.steps[this.currentStep].tip.media != undefined) {
                html += '       <div class="wcp-tour-tip-media-container">';

                if (this.options.steps[this.currentStep].tip.media.type == 'video') {
                    html += '<video loop>';
                    html += '   <source src="'+ this.options.steps[this.currentStep].tip.media.url_mp4 +'" type="video/mp4">';
                    html += '   <source src="'+ this.options.steps[this.currentStep].tip.media.url_webm +'" type="video/webm">';
                    html += '   <source src="'+ this.options.steps[this.currentStep].tip.media.url_ogv +'" type="video/ogv">';
                    html += '</video>';
                }
                if (this.options.steps[this.currentStep].tip.media.type == 'image') {
                    html += '<img src="'+ this.options.steps[this.currentStep].tip.media.url +'">';
                }
            }

            html += '       </div>';
            html += '   </div>';

            this.root.append(html);
        }

        var tip = this.root.find('.wcp-tour-tip[data-wcp-tip-index='+ parseInt(this.currentStep, 10) +']');

        tip.find('.wcp-tour-tip-title').html(this.options.steps[this.currentStep].tip.title);
        tip.find('.wcp-tour-tip-subtitle').html(this.options.steps[this.currentStep].tip.subtitle);

        // Set media
        if (this.options.steps[this.currentStep].tip.media != undefined) {
            tip.find('.wcp-tour-tip-media-button').show();

            if (this.options.steps[this.currentStep].tip.media.type == 'video') {
                tip.find('.wcp-tour-tip-media-button').html('<i class="fa fa-play" aria-hidden="true"></i>');
            }
            if (this.options.steps[this.currentStep].tip.media.type == 'image') {
                tip.find('.wcp-tour-tip-media-button').html('<i class="fa fa-camera" aria-hidden="true"></i>');
            }
        } else {
            tip.find('.wcp-tour-tip-media-button').hide();
        }

        // Position tip
        var c = this.options.steps[this.currentStep].tip.coordinates;

        if (c == undefined) {
            c = $.wcpTourCoordinatesForTipForStep(this.options.steps[parseInt(this.currentStep, 10)].title);
        }

        var x, y;
        if (this.options.steps[this.currentStep].tip.position == 'bottom-right') {
            x = c.x;
            y = c.y;
        }
        if (this.options.steps[this.currentStep].tip.position == 'bottom-left') {
            x = c.x - tip.width();
            y = c.y;
        }
        if (this.options.steps[this.currentStep].tip.position == 'top-right') {
            x = c.x;
            y = c.y - tip.height();
        }
        if (this.options.steps[this.currentStep].tip.position == 'top-left') {
            x = c.x - tip.width();
            y = c.y - tip.height();
        }

        tip.css({
            left: x,
            top: y
        });

        // position arrow
        if (this.options.steps[this.currentStep].tip.position == 'bottom-left') {
            tip.find('.wcp-tour-tip-arrow').css({
                transform: 'scaleX(-1)',
                float: 'right'
            });
        }
        if (this.options.steps[this.currentStep].tip.position == 'bottom-right') {
            tip.find('.wcp-tour-tip-arrow').css({
                transform: 'scaleX(1)',
            });
        }
        if (this.options.steps[this.currentStep].tip.position == 'top-left') {
            tip.find('.wcp-tour-tip-arrow').css({
                transform: 'scale(-1, -1)',
                float: 'right'
            });
        }
        if (this.options.steps[this.currentStep].tip.position == 'top-right') {
            tip.find('.wcp-tour-tip-arrow').css({
                transform: 'scaleY(-1)',
            });
        }

        // Apply extra arrow styles
        if (this.options.steps[this.currentStep].tip.arrowStyle) {
            tip.find('.wcp-tour-tip-arrow').attr('style', tip.find('.wcp-tour-tip-arrow').attr('style') + ' ' + this.options.steps[this.currentStep].tip.arrowStyle);
        }

        // Highlight rect
        this.updateHighlightRect();

        // If this is the first step, blink the media button
        if (this.currentStep == 0 && this.options.steps[this.currentStep].tip.media) {
            setTimeout(function() {
                $('.wcp-tour-tip-media-button').addClass('wcp-tour-tip-media-button-blink');
            }, 1000);
        }

        // Update tip classes
        for (var i=0; i<=parseInt(this.reachedStep, 10); i++) {
            var t = this.root.find('.wcp-tour-tip[data-wcp-tip-index='+ i +']');
            if (i == parseInt(this.currentStep, 10)) {
                t.addClass('wcp-tour-tip-visible');
                var animatedTip = t;
                setTimeout(function() {
                    animatedTip.addClass('wcp-tour-tip-animated');
                }, 10);
            } else {
                t.removeClass('wcp-tour-tip-animated');
                t.removeClass('wcp-tour-tip-visible');
            }
        }
    };
    WCPTour.prototype.updateHighlightRect = function() {
        if (this.currentStep >= 0 && this.options.steps[this.currentStep].tip.highlightRect) {
            var rect = $.wcpTourCoordinatesForHighlightRect(this.options.steps[this.currentStep].title);

            var windowWidth = $(window).width();
            var windowHeight = $(window).height();

            $('#wcp-tour-highlight-rect-1').css({
                left: 0,
                top: 0,
                width: '100%',
                height: rect.y
            });
            $('#wcp-tour-highlight-rect-2').css({
                left: rect.x + rect.width,
                top: rect.y,
                width: windowWidth - rect.x - rect.width,
                height: rect.height
            });
            $('#wcp-tour-highlight-rect-3').css({
                left: 0,
                top: rect.y + rect.height,
                width: '100%',
                height: windowHeight - rect.y - rect.height
            });
            $('#wcp-tour-highlight-rect-4').css({
                left: 0,
                top: rect.y,
                width: rect.x,
                height: rect.height
            });
        } else {
            // Set the initial position of the highlight rects
            $('.wcp-tour-highlight-rect').addClass('wcp-tour-highlight-rect-no-transition');
            var rect = $.wcpTourCoordinatesForHighlightRect(this.options.steps[0].title);

            var windowWidth = $(window).width();
            var windowHeight = $(window).height();

            $('#wcp-tour-highlight-rect-1').css({
                left: 0,
                top: 0,
                width: '100%',
                height: rect.y + rect.height/2
            });
            $('#wcp-tour-highlight-rect-2').css({
                left: rect.x + rect.width/2,
                top: rect.y + rect.height/2,
                width: windowWidth - rect.x - rect.width/2,
                height: 0
            });
            $('#wcp-tour-highlight-rect-3').css({
                left: 0,
                top: rect.y + rect.height/2,
                width: '100%',
                height: windowHeight - rect.y - rect.height/2
            });
            $('#wcp-tour-highlight-rect-4').css({
                left: 0,
                top: rect.y + rect.height/2,
                width: rect.x + rect.width/2,
                height: 0
            });

            setTimeout(function() {
                $('.wcp-tour-highlight-rect').removeClass('wcp-tour-highlight-rect-no-transition');
            }, 50);
        }
    };
    WCPTour.prototype.limitMediaContainer = function(container) {
        var dx = 0, dy = 0;

        container.css({ transform: 'translate('+ dx +'px, '+ dy +'px)' });

        if (container.offset().left < 0) {
            dx = container.offset().left;
        }
        if (container.offset().left + container.width() > $(window).width()) {
            dx = $(window).width() - (container.offset().left + container.width());
        }
        if (container.offset().top < 0) {
            dy = container.offset().left;
        }
        if (container.offset().top + container.height() > $(window).height()) {
            dy = $(window).height() - (container.offset().top + container.height());
        }

        container.css({ transform: 'translate('+ dx +'px, '+ dy +'px)' });
    }
})(jQuery, window, document);
