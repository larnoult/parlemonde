<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
*
*/
class LLMS_Metabox_Color_Field extends LLMS_Metabox_Field implements Meta_Box_Field_Interface {

	/**
	 * Class constructor
	 * @param array $_field Array containing information about field
	 */
	function __construct( $_field ) {

		$this->field = $_field;
	}

	/**
	 * outputs the Html for the given field
	 * @return HTML
	 */
	public function output() {

		global $post;

		parent::output();

		if ( ! $this->meta ) {
			$this->meta = $this->field['value'];
		}
		?>
		<input class="color-picker" type="text" name="<?php echo $this->field['id']; ?>" id="<?php echo $this->field['id']; ?>" value="<?php echo $this->meta; ?>" data-default-color="<?php echo $this->field['value']; ?>"/>
			<br /><span class="description"><?php echo $this->field['desc']; ?></span>
		<?php
		parent::close_output();
	}
}

