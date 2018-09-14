 <div id="content-protector<?php echo $identifier; ?>" class="content-protector-access-form">
        <form id="content-protector-access-form<?php echo $identifier; ?>" method="post" action="" autocomplete="off">
			<?php
			// Error message on unsuccessful attempt. Check $_POST['content-protector-ident'] to make sure
			// we're showing the error message on the right Content Protector access form
			if ( ( isset( $_POST['content-protector-ident'] ) ) && ( $_POST['content-protector-ident'] == $identifier ) ) {
				?>
                <div id="content-protector-incorrect-password<?php echo $identifier; ?>"
                     class="content-protector-incorrect-password"><?php echo $incorrect_password_message; ?></div>
			<?php } ?>
            <input name="content-protector-password" id="content-protector-password<?php echo $identifier; ?>"
                   class="content-protector-password" type="<?php echo $password_field_type; ?>"
                   placeholder="<?php echo $placeholder; ?>"
                   value="" size="<?php echo $password_field_length; ?>"
                   maxlength="<?php echo $password_field_length; ?>"/>
			<?php if ( strlen( trim( $cookie_expires ) ) > 0 ) { ?>
                <input name="content-protector-expires" id="content-protector-expires<?php echo $identifier; ?>"
                       type="hidden" value="<?php echo $cookie_expires; ?>"/>
			<?php } ?>
            <input name="content-protector-token" id="content-protector-token<?php echo $identifier; ?>" type="hidden"
                   value="<?php echo $password_hash; ?>"/>
            <input name="content-protector-ident" id="content-protector-ident<?php echo $identifier; ?>" type="hidden"
                   value="<?php echo $identifier; ?>"/>
            <input name="content-protector-submit" id="content-protector-submit<?php echo $identifier; ?>"
                   class="content-protector-form-submit" type="submit"
                   value="<?php echo $form_submit_label; ?>"/>
        </form>