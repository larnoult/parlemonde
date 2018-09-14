<a id="insert-user-shortcode-plus" class="button thickbox" href="#TB_inline?width=100%&height=100%&inlineId=user-shortcode-plus-thickbox" data-editor="content">
    <span class="kbj-user-shortcodes-plus-icon"></span>
    <?php echo $button_text; ?>
</a>
<div id="user-shortcode-plus-thickbox" style="display:none;">

    <h2>Add User Shortcode</h2>

    <p>
        <label for="kbj-user-shortcode-user">Choose User</label>
        <select id="kbj-user-shortcode-user" class="widefat">
            <option value="">Current User</option>
            <?php foreach( $users as $user ): ?>
                <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="kbj-user-shortcode-tag">Choose Attribute</label>
        <select id="kbj-user-shortcode-tag" name="kbj-user-shortcode-tag" class="widefat">
            <?php foreach( $shortcodes as $shortcode ): ?>
                <?php if( isset( $shortcode[ 'is_alias' ] ) && $shortcode[ 'is_alias' ] ) continue; ?>
                <option value="<?php echo $shortcode[ 'tag' ]; ?>"><?php echo $shortcode[ 'label' ]; ?></option>
            <?php endforeach; ?>
            <option value="user_meta"><?php _e( 'Custom User Meta' ); ?></option>
        </select>
    </p>

    <p id="kbj-user-shortcode-meta--wrapper" class="kbj-user-shortcode-meta--wrapper">
        <label for="kbj-user-shortcode-meta">Custom User Meta</label>
        <input type="text" id="kbj-user-shortcode-meta" name="kbj-user-shortcode-meta" class="widefat">
    </p>

    <p>
        <button id="add-user-shortcode-plus" class="button button-primary">Add Shortcode</button>
    </p>

</div>