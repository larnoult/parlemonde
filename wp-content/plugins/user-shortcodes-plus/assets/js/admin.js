jQuery( document ).ready( function( $ ) {

    $( document ).on( 'click', '#add-user-shortcode-plus', function( e ) {
        e.preventDefault();

        var tag = $( '#kbj-user-shortcode-tag' ).val();
        var userID = $( '#kbj-user-shortcode-user' ).val();
        var metaKey = $( '#kbj-user-shortcode-meta' ).val();


        var shortcode = '[' + tag;
        if( 0 != userID ) shortcode += ' id="' + userID + '"';
        if( 'user_meta' == tag ) shortcode += ' key="' + metaKey + '"';
        shortcode += ']';

        window.parent.send_to_editor( shortcode );
    });

    $( document ).on( 'change', '#kbj-user-shortcode-tag', function( e ) {

        var customMetaWrapper = $( '#kbj-user-shortcode-meta--wrapper' );

        if( 'user_meta' == $( this ).val() ){
            customMetaWrapper.show();
        } else {
            customMetaWrapper.hide();
        }

    });

});
