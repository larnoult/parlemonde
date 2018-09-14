<?php

function my_custom_register_msg_title() {

	echo '
	Pour inscrire votre classe au voyage de Pelico, merci de remplir ce formulaire.
	<ul class="listeRouge">
	<li>Vous recevrez immédiatement un premier mail afin de confirmer votre adresse mail (ce mail peut se trouver dans votre onglet "promotions" pour les utilisateurs de gmail). </li>
	<li>La confirmation de votre inscription vous sera communiquée dans un second mail, avec vos identifiants de connexion. Vous recevrez également la facture dans ce même mail.</li>
	</ul>
	<p>
	À très bientôt !
	L\'équipe Pelico
	</p>
	<p>
	En cas de problème avec le formulaire ou si vous ne recevez aucun mail de notre part, contactez-nous : <a href="maito:inscription@parlemonde.org">inscription@parlemonde.org</a></p>';

	
}
add_action( 'bp_before_register_page', 'my_custom_register_msg_title' );

/* To use WP-better-mail instead of BP template */

add_filter( 'bp_email_use_wp_mail', '__return_true' );


function bpfr_my_directory_setup_nav() {
	echo'
	<li>
		<a href="http://pelico.parlemonde.org/tous-les-pelicopains-2018-19/type/les-classes-grand-explorateur">Grands explorateurs</a>
	</li>
	<li>
		<a href="http://pelico.parlemonde.org/tous-les-pelicopains-2018-19/type/les-classes-explorateur-en-herbe">Explorateurs en herbe</a>
	</li>
	<li>
		<a href="http://pelico.parlemonde.org/tous-les-pelicopains-2018-19/type/l-equipe-par-le-monde">Équipe Par Le Monde</a>
	</li>'; 
}
add_action( 'bp_members_directory_member_types', 'bpfr_my_directory_setup_nav' );


function display_map() {
	echo '	<div style="margin:0 auto">[mapsmarker layer="1"]</div>';
}
add_action('bp_before_directory_members_page', 'display_map');


/**
 * Display the member type of the displayed user
 */
function using_mt_member_header_display() {
	$member_type = bp_get_member_type( bp_displayed_user_id() );

	if ( empty( $member_type ) ) {
		return;
	}

	$member_type_object = bp_get_member_type_object( $member_type );
	?>
	<p class="member_type"><?php echo esc_html( $member_type_object->labels['singular_name'] ); ?></p>
	<?php
}
add_action( 'bp_before_member_header_meta', 'using_mt_member_header_display' );


function using_mt_get_member_type_singular_name( $name = '' ) {
	if ( empty( $name ) ) {
		return false;
	}
	$output = '';
	if ( is_array( $name ) ) {
		$member_types_objects = bp_get_member_types( array(), 'objects' );
		$output_array = array();
		foreach ( $name as $name_type ) {
			if ( empty( $member_types_objects[ $name_type ]->labels['singular_name'] ) ) {
				continue;
			}
			$output_array[] = $member_types_objects[ $name_type ]->labels['singular_name'];
		}
		$output = join( ', ', $output_array );
	} else {
		$member_type_object = bp_get_member_type_object( $name );
	
		if ( ! empty( $member_type_object->labels['singular_name'] ) )  {
			$output = $member_type_object->labels['singular_name'];
		}
	}
	return $output;
}


/**
 * Display in loop
 * 
 * If a member has more than one member types, they will be comma separated
 */ 

function using_mt_in_members_loop() {
	$member_type = bp_get_member_type( bp_get_member_user_id(), false );
	echo esc_html( using_mt_get_member_type_singular_name( $member_type ) );
}
add_action( 'bp_directory_members_item', 'using_mt_in_members_loop' );

/* Adding Members tabs to Buddypress profile */ 

function buddypress_tab() {
  global $bp;
  bp_core_new_nav_item( array( 
        'name' => __( 'Annuaire', 'annuaire' ), 
        'slug' => 'to-members', 
        'position' => 30,
        'screen_function' => 'toMembers',
        'show_for_displayed_user' => true,
        'item_css_id' => 'ibenic_budypress_recent_posts'
  ) );
  
}
add_action( 'bp_setup_nav', 'buddypress_tab', 1000 );

function toMembers(){
	header('Location: http://pelico.parlemonde.org/tous-les-pelicopains-2018-19/');
}


?>

