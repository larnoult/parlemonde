<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
 * tabs and accordions widget.
 * THANKS PROTEUSTHEMES!
 */
class kad_tabs_content_widget extends WP_Widget{
	private $used_IDs = array();

    public function __construct() {
        $widget_ops = array('classname' => 'kadence_tabs_content_widget', 'description' => __('Adds tabs that can hold widgets.', 'virtue'));
        parent::__construct('kadence_tabs_content_widget', __('Virtue: Tabs & Accordions', 'virtue'), $widget_ops);
    }

       public function widget($args, $instance){ 
        extract( $args ); 
        if ( ! isset( $widget_id ) ) {
      		$widget_id = $this->id;
     	}
        $instance['widget_title'] 	= empty( $instance['widget_title'] ) ? '' : $args['before_title'] . apply_filters( 'widget_title', $instance['widget_title'], $instance ) . $args['after_title'];
        $instance['display'] 		= ! empty( $instance['display'] ) ? $instance['display'] : 'tabs';
        $items                	= isset( $instance['items'] ) ? array_values( $instance['items'] ) : array();

        // Prepare items data.
		foreach ( $items as $key => $item ) {
			$items[ $key ]['builder_id'] = empty( $item['builder_id'] ) ? uniqid() : $item['builder_id'];
			$items[ $key ]['tab_id']     = $this->format_id_from_name( $item['title'] );
		}
        echo $before_widget; 
        if('accordion' == $instance['display']) {
        	?>
        	<div class="kadence-accordion-container">
				<?php if ( ! empty( $instance['widget_title'] ) ) : ?>
					<?php echo wp_kses_post( $instance['widget_title'] ); ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $items ) ) :
						$i = 0;
						$items[0]['active'] = true; // First tab should be active.
					?>
					<div class="panel-group kt-accordion" id="accordionname<?php echo esc_attr($widget_id);?>">
						<?php foreach ( $items as $item ) : 
								if ($i % 2 == 0) {
									$eo = "even";
								} else {
									$eo = "odd";
								}?>
								<div class="panel panel-default panel-<?php echo esc_attr($eo);?>">
									<div class="panel-heading">
										<a class="accordion-toggle<?php echo !empty( $item['active'] ) ? '' : '  collapsed'; ?>" data-toggle="collapse" data-parent="#accordionname<?php echo esc_attr($widget_id);?>" href="#collapse<?php echo esc_attr($widget_id.'-'.$item['tab_id']);?>">
											<h5><i class="kt-icon-minus"></i><i class="kt-icon-plus"></i><?php echo wp_kses_post( $item['title'] ); ?></h5>
										</a>
									</div>
									<div id="collapse<?php echo esc_attr($widget_id.'-'.$item['tab_id']);?>" class="panel-collapse collapse<?php echo empty( $item['active'] ) ? '' : '  in'; ?>">
										<div class="panel-body postclass">
											<?php echo siteorigin_panels_render( 'w'.$item['builder_id'], true, $item['panels_data'] ); ?>
										</div>
									</div>
								</div>
						<?php $i ++; 
						endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

        	<?php 
        } else {
            ?>
			<div class="kadence-tabs-container">
				<?php if ( ! empty( $instance['widget_title'] ) ) : ?>
					<?php echo wp_kses_post( $instance['widget_title'] ); ?>
				<?php endif; ?>

				<?php
				if ( ! empty( $items ) ) :
						$items[0]['active'] = true; // First tab should be active.
					?>
					<ul class="nav nav-tabs sc_tabs kt-tabs kt-sc-tabs kt-tabs-style-1" role="tablist">
						<?php foreach ( $items as $item ) : ?>
							<li class="<?php echo empty( $item['active'] ) ? '' : '  active'; ?>">
								<a href="#sctab<?php echo esc_attr( $item['tab_id']  ); ?>" data-toggle="tab" role="tab">
								<?php echo wp_kses_post( $item['title'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="kt-tab-content tab-content postclass">
					<?php foreach ( $items as $item ) : ?>
						<div class="tab-pane clearfix<?php echo empty( $item['active'] ) ? '' : '  active'; ?>" id="sctab<?php echo esc_attr( $item['tab_id']  ); ?>" role="tabpanel">
							<?php echo siteorigin_panels_render( 'w'.$item['builder_id'], true, $item['panels_data'] ); ?>
						</div>
					<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php
		}
        echo $after_widget;

    }

    private function format_id_from_name( $tab_title ) {

			// To lowercase.
			$tab_id = strtolower( $tab_title );
			// Clean up multiple dashes or whitespaces.
			$tab_id = preg_replace( '/[\s-]+/', ' ', $tab_id );
			// Convert whitespaces and underscore to dash.
			$tab_id = preg_replace( '/[\s_]/', '-', $tab_id );
			// Remove all specials characters that are not unicode letters, numbers or dashes.
			$tab_id = preg_replace( '/[^\p{L}\p{N}-]+/u', '', $tab_id );

			// Add suffix if there are multiple identical tab titles.
			if ( array_key_exists( $tab_id, $this->used_IDs ) ) {
				$this->used_IDs[ $tab_id ] ++;
				$tab_id = $tab_id . '-' . $this->used_IDs[ $tab_id ];
			}
			else {
				$this->used_IDs[ $tab_id ] = 0;
			}

			// Return unique ID.
			return $tab_id;
		}
    public function update($new_instance, $old_instance) {
        $instance = array();

        $instance['widget_title'] = sanitize_text_field( $new_instance['widget_title'] );
        $instance['display'] = sanitize_text_field( $new_instance['display'] );

        if ( ! empty( $new_instance['items'] )  ) {
			foreach ( $new_instance['items'] as $key => $item ) {
				$instance['items'][ $key ]['id']          = sanitize_key( $item['id'] );
				$instance['items'][ $key ]['title']       = sanitize_text_field( $item['title'] );
				$instance['items'][ $key ]['builder_id']  = uniqid();
				$instance['items'][ $key ]['panels_data'] = is_string( $item['panels_data'] ) ? json_decode( $item['panels_data'], true ) : $item['panels_data'];
			}
		}
        // Sort items by ids, because order might have changed.
		usort( $instance['items'], array( $this, 'sort_by_id' ) );

        return $instance;
    }

    function sort_by_id( $a, $b ) {
		return $a['id'] - $b['id'];
	}


  	public function form($instance){

  			$widget_title = ! empty( $instance['widget_title'] ) ? $instance['widget_title'] : '';
	    	$display = ! empty( $instance['display'] ) ? $instance['display'] : 'tabs';
			$items        = isset( $instance['items'] ) ? $instance['items'] : array();

  			$display_array = array();
  			$display_options = array(array("slug" => "tabs", "name" => __('Tabs', 'virtue')), array("slug" => "accordion", "name" => __('Accordion', 'virtue')));
  			foreach ($display_options as $display_option) {
		      	if ($display == $display_option['slug']) { $selected=' selected="selected"';} else { $selected=""; }
		      	$display_array[] = '<option value="' . $display_option['slug'] .'"' . $selected . '>' . $display_option['name'] . '</option>';
		    }

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php esc_html_e( 'Widget title:', 'virtue' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><?php esc_html_e( 'Display as:', 'virtue' ); ?></label>
			<select id="<?php echo $this->get_field_id('display'); ?>" style="width:100%; max-width:230px;" name="<?php echo $this->get_field_name('display'); ?>"><?php echo implode('', $display_array);?></select>
		</p>

		<hr>

		<h3><?php esc_html_e( 'Panes:', 'virtue' ); ?></h3>

		<script type="text/template" id="js-kadence-tab-<?php echo esc_attr( $this->current_widget_id ); ?>">
			<div class="kadence-tabs-widget  ui-widget  ui-widget-content  ui-helper-clearfix  ui-corner-all">
				<div class="kadence-tabs-widget-header  ui-widget-header  ui-corner-all">
					<span class="dashicons  dashicons-sort"></span>
					<span><?php esc_html_e( 'Pane', 'virtue' ); ?> - </span>
					<span class="kadence-tabs-widget-header-title">{{title}}</span>
					<span class="kadence-tabs-widget-toggle  dashicons  dashicons-minus"></span>
				</div>
				<div class="kadence-tabs-widget-content">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title"><?php echo __( 'Pane title:', 'virtue' ); ?></label>
						<input class="widefat  js-kadence-tabs-widget-title" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
					</p>

					<label><?php echo __( 'Pane content:', 'virtue' ); ?></label>
					<div class="siteorigin-page-builder-widget siteorigin-panels-builder siteorigin-panels-builder-kadence-tabs" id="siteorigin-page-builder-widget-{{builder_id}}" data-builder-id="{{builder_id}}" data-type="layout_widget">
						<p>
							<a href="#" class="button-secondary siteorigin-panels-display-builder" ><?php _e('Open Builder', 'virtue') ?></a>
						</p>

						<input type="hidden" data-panels-filter="json_parse" value="{{panels_data}}" class="panels-data" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][panels_data]" />
					</div>

					<p>
						<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" class="js-kadence-tab-id" type="hidden" value="{{id}}" />
						<a href="#" class="kadence-remove-tab  js-kadence-remove-tab"><span class="dashicons dashicons-dismiss"></span> <?php echo __( 'Remove pane', 'virtue' ); ?></a>
					</p>
				</div>
			</div>
		</script>

		<div class="kadence-widget-tabs" id="tabs-<?php echo esc_attr( $this->current_widget_id ); ?>">
			<div class="tabs  js-kadence-sortable-tabs"></div>
			<p>
				<a href="#" class="button  js-kadence-add-tab"><?php echo  __( 'Add new pane', 'virtue' ); ?></a>
			</p>
		</div>

		<script type="text/javascript">
			(function( $ ) {
				var tabsJSON = <?php echo wp_json_encode( $items ) ?>;

				// Get the right widget id and remove the added < > characters at the start and at the end.
				var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

				if ( _.isFunction( KTTabs.Utils.repopulateTabs ) ) {
					KTTabs.Utils.repopulateTabs( tabsJSON, widgetId );
				}

				// Make tabs settings sortable.
				$( '.js-kadence-sortable-tabs' ).sortable({
					items: '.kadence-widget-single-tab',
					handle: '.kadence-tabs-widget-header',
					cancel: '.kadence-tabs-widget-toggle',
					placeholder: 'kadence-tabs-widget-placeholder',
					stop: function( event, ui ) {
						$( this ).find( '.js-kadence-tab-id' ).each( function( index ) {
							$( this ).val( index );
						});
					}
				});
			})( jQuery );
		</script>

		<?php
		}
}