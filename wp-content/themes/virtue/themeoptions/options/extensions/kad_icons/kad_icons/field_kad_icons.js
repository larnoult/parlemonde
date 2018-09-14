(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.kad_icons = redux.field_objects.kad_icons || {};

    $( document ).ready(
        function() {
            //redux.field_objects.kad_icons.init();
        }
    );

    redux.field_objects.kad_icons.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-kad_icons' );
        }

        $( selector ).each(
            function() {
                var el = $( this );

                redux.field_objects.media.init(el);

                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                
                if ( parent.hasClass( 'redux-container-kad_icons' ) ) {
                    parent.addClass( 'redux-field-init' );    
                }
                
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }
                function addIconToSelect(icon) {
                    if ( icon.hasOwnProperty( 'id' ) ) {
                        return "<span class='elusive'><i class='" + icon.id + "'></i>" + " " + icon.id + "</span>";
                    }
                }
                $('select.font-icons').select2({
                            formatResult: addIconToSelect,
                            formatSelection: addIconToSelect,
                            width: '93%',
                            triggerChange: true,
                            allowClear: true,
                        });

                el.find( '.redux-slides-remove' ).live(
                    'click', function() {
                        redux_change( $( this ) );

                        $( this ).parent().siblings().find( 'input[type="text"]' ).val( '' );
                        $( this ).parent().siblings().find( 'textarea' ).val( '' );
                        $( this ).parent().siblings().find( 'input[type="hidden"]' ).val( '' );
                        $( this ).parent().siblings().find( 'select' ).val( '' );

                        var slideCount = $( this ).parents( '.redux-container-kad_icons:first' ).find( '.redux-slides-accordion-group' ).length;

                        if ( slideCount > 1 ) {
                            $( this ).parents( '.redux-slides-accordion-group:first' ).slideUp(
                                'medium', function() {
                                    $( this ).remove();
                                }
                            );
                        } else {
                            var content_new_title = $( this ).parent( '.redux-slides-accordion' ).data( 'new-content-title' );

                            $( this ).parents( '.redux-slides-accordion-group:first' ).find( '.remove-image' ).click();
                            $( this ).parents( '.redux-container-kad_icons:first' ).find( '.redux-slides-accordion-group:last' ).find( '.redux-slides-header' ).text( content_new_title );
                        }
                    }
                );

                el.find( '.kad_redux-icon-add' ).click(
                    function() {
                        $('select.font-icons').select2("destroy");
                        var newSlide = $( this ).prev().find( '.redux-slides-accordion-group:last' ).clone( true );

                        var slideCount = $( newSlide ).find( '.slide-title' ).attr( "name" ).match( /[0-9]+(?!.*[0-9])/ );
                        var slideCount1 = slideCount * 1 + 1;

                        $( newSlide ).find( '.redux-slides-list input[type="text"], .redux-slides-list input[type="checkbox"], input[type="hidden"], select.redux-select-item, textarea' ).each(
                            function() {

                                $( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );
                                $( this ).val( '' );
                                if ( $( this ).hasClass( 'slide-sort' ) ) {
                                    $( this ).val( slideCount1 );
                                }
                            }
                        );

                        var content_new_title = $( this ).prev().data( 'new-content-title' );

                        $( newSlide ).find( '.screenshot' ).removeAttr( 'style' );
                        $( newSlide ).find( '.screenshot' ).addClass( 'hide' );
                        $( newSlide ).find( '.screenshot a' ).attr( 'href', '' );
                        $( newSlide ).find( '.remove-image' ).addClass( 'hide' );
                        $( newSlide ).find( '.redux-slides-image' ).attr( 'src', '' ).removeAttr( 'id' );
                        $( newSlide ).find( '.font-icons.select2-container' ).remove();
                        $( newSlide ).find( 'select.font-icons option' ).removeAttr('selected');
                        $( newSlide ).find( '.icon-link-target input[type="checkbox"]' ).val('');
                        $( newSlide ).find( '.icon-link-target input[type="checkbox"]' ).attr("checked", false);
                        $( newSlide ).find( 'h3' ).text( '' ).append( '<span class="redux-slides-header">' + content_new_title + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );
                        $( this ).prev().append( newSlide );
                          $('select.font-icons').select2({
	                            formatResult: addIconToSelect,
	                            formatSelection: addIconToSelect,
	                            width: '93%',
	                            triggerChange: true,
	                            allowClear: true,
	                        });
                    }
                );

                el.find( '.slide-title' ).keyup(
                    function( event ) {
                        var newTitle = event.target.value;
                        $( this ).parents().eq( 3 ).find( '.redux-slides-header' ).text( newTitle );
                    }
                );


                el.find( ".redux-slides-accordion" )
                    .accordion(
                    {
                        header: "> div > fieldset > h3",
                        collapsible: true,
                        active: false,
                        heightStyle: "content",
                        icons: {
                            "header": "ui-icon-plus",
                            "activeHeader": "ui-icon-minus"
                        }
                    }
                )
                    .sortable(
                    {
                        axis: "y",
                        handle: "h3",
                        connectWith: ".redux-slides-accordion",
                        start: function( e, ui ) {
                            ui.placeholder.height( ui.item.height() );
                            ui.placeholder.width( ui.item.width() );
                        },
                        placeholder: "ui-state-highlight",
                        stop: function( event, ui ) {
                            // IE doesn't register the blur when sorting
                            // so trigger focusout handlers to remove .ui-state-focus
                            ui.item.children( "h3" ).triggerHandler( "focusout" );
                            var inputs = $( 'input.slide-sort' );
                            inputs.each(
                                function( idx ) {
                                    $( this ).val( idx );
                                }
                            );
                        }
                    }
                );
            }
        );
    };
})( jQuery );