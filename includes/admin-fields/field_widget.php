<?php
/**
 * Repeater
 * 
 * @package   Widz/Corefields
 * @category  Functions
 * @author    vutuansw
 * @license   GPLv3
 * @version   1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Field Widget
 *
 * @param $settings
 * @param string $value
 *
 * @since 1.0.0
 * @return string - html string.
 */
function widz_form_widget( $settings, $value ) {
	$settings = wp_parse_args( $settings, array(
		'id' => '',
		'name' => '',
		'css_class' => '',
		'value' => ''
			) );
	$uniqid = uniqid( 'widz-field-' );

	ob_start();
	?>

	<div class="widz-field widz-widget <?php echo esc_attr( $uniqid ) . ' ' . esc_attr( $settings['css_class'] ) ?>">
		<ul class="widz-widget-selected">
			<?php
			if ( !empty( $value ) ) {

				$data_list = json_decode( stripslashes( $value ), true );

				foreach ( $data_list as $k => $item ) {
					widz_form_field_tab_item( $item );
				}
			}
			?>
		</ul>
		<div class="widz-widget-controls">
			<?php
			global $wp_registered_widgets;
			$flags = array();
			?>
			<select>
				<?php
				echo '<option value="">-- ' . __( 'Select a widget to list', 'widz' ) . '--</option>';

				foreach ( $wp_registered_widgets as $k => $v ) {

					$k = preg_replace( '/-(\d+)/', '', $k );

					if ( $k != 'widz_tabbed_widget' ) {

						if ( !isset( $flags[$k] ) ) {//Check if duplicate
							$flags[$k] = true;

							if ( is_object( $v['callback'][0] ) ) {
								printf( '<option value="%s" data-widget="%s">%s</option>', $k, get_class( $v['callback'][0] ), $v['name'] );
							}
						}
					}
				}
				?>
			</select>
			<span class="spinner"></span>
		</div>
		<input type="hidden"  class="widz_value" id="<?php echo esc_attr( $settings['id'] ) ?>" name="<?php echo esc_attr( $settings['name'] ) ?>" value="<?php echo esc_textarea( $value ); ?>"/>
	</div>


	<?php
	return ob_get_clean();
}

/**
 * Item controls in Widget firld
 */
function widz_form_field_tab_item( $item ) {
	?>
	<li id="<?php echo esc_attr( $item['widget_id'] . '-' . $item['number'] ) ?>" class="widz-widget-item" 
		data-number="<?php echo esc_attr( $item['number'] ) ?>" 
		data-widget_id="<?php echo esc_attr( $item['widget_id'] ) ?>"
		data-widget="<?php echo esc_attr( $item['widget'] ) ?>">

		<div class='widz-widget'>	
			<div class='widz-widget-top'>
				<div class='widz-widget-title-action'>
					<a class='cmd edit' title='<?php echo esc_attr__( 'Edit', 'widz' ) ?>'><i class="fa fa-pencil"></i></a>
					<a class='cmd remove' title='<?php echo esc_attr__( 'Remove', 'widz' ) ?>'><i class='fa fa-trash-o'></i></a>
				</div>
				<div class='widz-widget-title ui-sortable-handle'><h4><i class="fa fa-ellipsis-v"></i><span><?php echo esc_attr( $item['params']['title'] ) ?></span></h4></div>
			</div>
			<div class='widz-widget-inside'>
				<?php widz_init_widget_control( $item ); ?>
			</div> 
		</div>

	</li>
	<?php
}

/**
 * Init Widget Control in parent widget
 * @param array $args Widget Settings
 * @since 1.0
 */
function widz_init_widget_control( $args ) {
	if ( class_exists( $args['widget'] ) ) {

		//Init widget
		$widget = new $args['widget'];

		//Prepend icon to widget form
		$icon_value = isset( $args['params']['icon'] ) ? sanitize_text_field( $args['params']['icon'] ) : '';
		printf( '<div class="widz_widget_row"><div class="col-label"><label for="%1$s">%2$s</label></div>', $widget->get_field_id( 'icon' ), __( 'Icon:', 'widz' ) );
		
		echo '<div class="col-field">';
		echo widz_form_icon_picker( array(
			'name' => $widget->get_field_name( 'icon' ),
			'id' => $widget->get_field_id( 'icon' ),
			'type' => 'icon_picker'
				), $icon_value );
		echo '</div>';

		//Call widget form
		$widget->form( $args['params'] );
	} else {
		echo "\t\t<p>" . __( 'There are no options for this widget.', 'widz' ) . "</p>\n";
	}
}
