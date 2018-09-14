/* global redux, setting */

(function( $ ) {  //This functions first parameter is named $
    'use strict';

    redux.advanced_customizer = redux.advanced_customizer || {};

    $( document ).ready(
        function() {
            redux.advanced_customizer.init();
        }
    );
    redux.advanced_customizer.init = function() {
        $( '.accordion-section.redux-section h3, .accordion-section.redux-panel h3' ).click(
            function() {
                redux.advanced_customizer.resize( $( this ).parent() );
            }
        )
        $( '.control-panel-back, .customize-panel-back' ).click(function() {
            $( document ).find( 'form#customize-controls' ).removeAttr( 'style' );
            $( document ).find( '.wp-full-overlay' ).removeAttr( 'style' );
        });


        $( '.control-section-back, .customize-section-back' ).click(
            function() {
                redux.advanced_customizer.resize( $( this ).parents( '.redux-panel:first' ) );
            }
        );
    };
    redux.advanced_customizer.resize = function( el ) {
        var width = el.attr( 'data-width' );
        if ( $( 'body' ).width() < 640 ) {
            width = "";
        }
        if ( el.hasClass( 'open' ) || el.hasClass( 'current-panel' ) || el.hasClass( 'current-section' ) ) {
            if ( width != "" ) {
                $( document ).find( 'form#customize-controls' ).attr(
                    'style', 'width:' + width + ';'
                );
                $( document ).find( '.wp-full-overlay' ).attr(
                    'style', 'margin-left:' + width + ';'
                );
            }
        } else {
            var width = el.parents( '.redux-panel:first' ).attr( 'data-width' );
            if ( !width ) {
                $( document ).find( 'form#customize-controls' ).removeAttr( 'style' );
                $( document ).find( '.wp-full-overlay' ).removeAttr( 'style' );
            } else {
                $( document ).find( 'form#customize-controls' ).attr(
                    'style', 'width:' + width + ';'
                );
                $( document ).find( '.wp-full-overlay' ).attr(
                    'style', 'margin-left:' + width + ';'
                );
            }
        }
    }
})( jQuery );