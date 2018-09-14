<?php
$langs = array (array('xx', 'Auto (browser)'), array('en', 'English (US)'), array('ar', 'Arabic'), array('bg', 'Bulgarian'), array('ca', 'Catalan'), array('zh-CN', 'Chinese (simplified)'), array('zh-TW', 'Chinese (Traditional)'), array('hr', 'Croation'), array('cs', 'Czech'), array('da', 'Danish'), array('nl', 'Dutch'), array('en-GB', 'English (UK)'), array('fil', 'Filipino'), array('fr', 'French'), array('fr-CA', 'French (Canadian)'), array('de', 'German'), array('de-AT', 'German (Austria)'), array('de-CH', 'German (Switzerland)'), array('el', 'Greek'), array('iw', 'Hebrew'), array('hi', 'Hindi'), array('hu', 'Hungarian'), array('it', 'Italian'), array('ja', 'Japanese'), array('ko', 'Korean'), array('lv', 'Latvian'), array('lt', 'Lithuanian'), array('no', 'Norwegian'), array('fa', 'Persian'), array('pl', 'Polish'), array('pt', 'Portuguese'), array('pt-BR', 'Portuguese (Brazil)'), array('pt-PT', 'Portuguese (Portugal)'), array('ro', 'Romanian'), array('ru', 'Russian'), array('sr', 'Serbian'), array('sl', 'Slovak'), array('es', 'Spanish'), array('es-419', 'Spanish (Latin America)'), array('sv', 'Swedish'), array('th', 'Thai'), array('tr', 'Turkish'), array('uk', 'Ukrainian'), array('vi', 'Vietnamese') );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>BuddyPress No Captcha reCaptcha</h2>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'mmbpcapt' ); ?>
		<p>If you don't already have your Google reCaptcha API private and public keys, click <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a> to get them.</p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="mmbpcapt_public">reCAPTCHA Site Key:</label></th>
				<td><input type="text" id="mmbpcapt_public" name="mmbpcapt_public" value="<?php echo get_option('mmbpcapt_public'); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mmbpcapt_private">reCAPTCHA Secret Key:</label></th>
				<td><input type="text" id="mmbpcapt_private" name="mmbpcapt_private" value="<?php echo get_option('mmbpcapt_private'); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mmbpcapt_theme">Theme:</label></th>
				<td><select id="mmbpcapt_theme" name="mmbpcapt_theme" value=" <?php
					echo get_option('mmbpcapt_theme'); ?>">
					<option value="light" <?php
					if (get_option('mmbpcapt_theme') == light) echo 'selected="selected"'; ?>>Light</option>
					<option value="dark" <?php
					if (get_option('mmbpcapt_theme') == dark) echo 'selected="selected"'; ?>>Dark</option>
				</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mmbpcapt_type">Type:</label></th>
				<td><select id="mmbpcapt_type" name="mmbpcapt_type" value=" <?php
					echo get_option('mmbpcapt_type'); ?>">
					<option value="image" <?php
					if (get_option('mmbpcapt_type') == image) echo 'selected="selected"'; ?>>Image</option>
					<option value="audio" <?php
					if (get_option('mmbpcapt_type') == audio) echo 'selected="selected"'; ?>>Audio</option>
				</select></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mmbpcapt_lang">Language:</label></th>
				<td>
					<select id="mmbpcapt_lang" name="mmbpcapt_lang" value=" <?php
						echo get_option('mmbpcapt_lang'); ?>">
						<?php foreach ($langs as $lang) : ?>
							<option value="<?php echo $lang[0]; ?>" <?php echo get_option('mmbpcapt_lang')==$lang[0]?'selected ':''; ?>><?php echo $lang[1]; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="mmbpcapt_style">reCAPTCHA container CSS style:</label></th>
				<td><input type="text" id="mmbpcapt_style" name="mmbpcapt_style" value="<?php echo get_option('mmbpcapt_style'); ?>" /></td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	<span>More Wordpress fun at <a target="_blank" href="http://www.functionsphp.com/">functionsphp.com</a></span>
</div>