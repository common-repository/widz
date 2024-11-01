<?php
/**
 * Tabbed Widget
 *
 * @package     Widz
 * @category    Widgets
 * @author      vutuansw
 * @license     GPLv3
 */

/**
 * Widget
 * Display in wp-admin/widgets.php
 */
class Widz_Tabbed_Widget extends Widz_Widget {

	public function __construct() {

		$this->widget_cssclass = 'widz_tabbed_widget';
		$this->widget_description = __( "Display tabbed in the sidebar.", 'widz' );
		$this->widget_id = 'widz_tabbed_widget';
		$this->widget_name = __( 'Widz Tabbed', 'widz' );
		$this->fields = array(
			array(
				'name' => 'title',
				'type' => 'textfield',
				'heading' => __( 'Title:', 'widz' ),
				'value' => '',
				'desc' => '',
			),
			array(
				'name' => 'content',
				'type' => 'widget',
				'heading' => __( 'Content:', 'widz' ),
				'value' => '',
				'desc' => '',
			)
		);
		parent::__construct();

		add_action( 'wp_ajax_widz_get_widget_control', array( $this, 'ajax_widget_control' ) );
		add_action( 'wp_ajax_nopriv_widz_get_widget_control', array( $this, 'ajax_widget_control' ) );
		add_filter( 'widget_title', array( $this, 'widget_title' ), 10, 3 );
		
	}

	/**
	 * Hook Widget Title
	 */
	public function widget_title( $title, $instance, $id_base ) {
		if ( isset( $instance['hidden_title'] ) && $instance['hidden_title'] == true ) {
			return '';
		}
		return $title;
	}

	public function ajax_widget_control() {

		widz_form_field_tab_item( array(
			'widget_id' => sanitize_key( $_POST['widget_id'] ),
			'widget_name' => sanitize_text_field( $_POST['widget_name'] ),
			'widget' => sanitize_text_field( $_POST['widget'] ),
			'number' => uniqid(),
			'params' => array( 'title' => sanitize_text_field( $_POST['widget_name'] ) )
		) );

		wp_die();
	}

	/**
	 * Widget output
	 */
	public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );
		if ( !empty( $instance['content'] ) ):
			$data = json_decode( $instance['content'], 1 );
			?>
			<div class="widz_tabbed">

				<ul class="widz_tabbed__nav nav-<?php echo esc_attr( count( $data ) ) ?>">
					<?php
					$i = 0;
					foreach ( $data as $id => $arr ) {

						$active = $i == 0 ? 'active' : '';
						$icon = !empty( $arr['params']['icon'] ) ? "<i class='{$arr['params']['icon']}'></i>" : '';
						printf( '<li class="%1$s"><a href="#%2$s">%4$s %3$s</a></li>', $active, $id, $arr['params']['title'], $icon );
						$i++;
					}
					?>
				</ul>

				<!-- Tab panes -->
				<div class="widz_tabbed__content">
					<?php
					$i = 0;
					foreach ( $data as $id => $arr ) {

						$active = $i == 0 ? 'active in' : '';
						printf( '<div role="tabpanel" class="tab-pane fade %s" id="%s">', $active, $id );
						$arr['params']['hidden_title'] = true;
						the_widget( $arr['widget'], $arr['params'] );
						echo '</div>';
						$i++;
					}
					?>

				</div>
			</div>
			<?php
		endif;
		$this->widget_end( $args );
	}

}

/**
 * Init widget
 */
function widz_tabbed_widget_init() {
	register_widget( 'Widz_Tabbed_Widget' );
}

/**
 * Hook to widgets_init
 */
add_action( 'widgets_init', 'widz_tabbed_widget_init' );
