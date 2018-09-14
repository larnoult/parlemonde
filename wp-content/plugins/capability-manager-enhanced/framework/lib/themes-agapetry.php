<?php
function agp_admin_authoring( $mod_id = '' ) {
	return; // kevinB temp
?>
	<dl>
		<dt>Capability Manager</dt>
		<dd>
			<ul>
				<li><a href="http://agapetry.com" class="capsman" target="_blank"><?php _e('Plugin Homepage', 'capsman-enhanced'); ?></a></li>
				<li><a href="http://presspermit.com" class="docs" target="_blank"><?php _e('Documentation', 'capsman-enhanced'); ?></a></li>
				<li><a href="http://agapetry.net/forum" class="help" target="_blank"><?php _e('Support Forum', 'capsman-enhanced'); ?></a></li>
				<li><a href="http://agapetry.com" class="home" target="_blank"><?php _e('Author Homepage', 'capsman-enhanced')?></a></li>
				<li><a href="http://agapetry.com" class="donate" target="_blank"><?php _e('Help donating', 'capsman-enhanced')?></a></li>
			</ul>
		</dd>
	</dl>
<?php
}

function agp_admin_footer( $mod_id = '' ) {
?>
	<p class="footer"> 
	<a href="http://wordpress.org/extend/plugins/capability-manager-enhanced"><?php printf( __( 'Capability Manager Enhanced %s', 'capsman-enhanced' ), CAPSMAN_ENH_VERSION );?></a>
	&nbsp;&nbsp;|&nbsp;&nbsp;&copy; <?php _e( 'Copyright 2010 Jordi Canals', 'capsman-enhanced' );?>
	&nbsp;&nbsp;|&nbsp;&nbsp;
	<?php
	printf( __( 'Modifications &copy; Copyright %1$s %2$s', 'capsman-enhanced' ), '2012-2018', '<a href="http://agapetry.com">Kevin Behrens</a>' );?>
	</p>
	<?php
}
