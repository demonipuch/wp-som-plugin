<?php

/**
 * Adds Recent_Releases_Widget widget.
 */
class Recent_Releases_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'recent_releases_widget', // Base ID
			__('Recent Releases', 'seasonofmist'), // Name
			array( 'description' => __( 'The most recent releases.', 'seasonofmist' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

        echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		// WP_Query arguments
		$args = array (
			'post_type'              => 'release',
			'post_status'            => 'publish',
			'posts_per_page'         => $instance['number'],
			'order'                  => 'DESC',
			'meta_query'             => array(
				'relation'           => 'OR',
				array(
					'key'            => 'release_date',
					'value'          => date(Ymd),
					'compare'        => '<=',
				),
				array(
					'key'            => 'release_date_us',
					'value'          => date(Ymd),
					'compare'        => '<=',
				),
			),
		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) { ?>
			<ul>
			<?php while ( $query->have_posts() ) {
				$query->the_post();
			?>
				<li>
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?><?php the_title(); ?></a><br/>
				</li>
			<?php } ?>
			</ul>
		<?php }

		// Restore original Post Data
		wp_reset_postdata();

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Recent Releases', 'seasonofmist' );
		}

	if ( isset( $instance['number'] ) ) {
		$number = $instance['number'];
	}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php __( 'Title:', 'seasonofmist' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p><p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php __( 'Number of releases to show:', 'seasonofmist' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';

		return $instance;
	}

} // class Recent_Releases_Widget

// register Recent_Releases_Widget widget
function register_recent_releases_widget() {
    register_widget( 'Recent_Releases_Widget' );
}
add_action( 'widgets_init', 'register_recent_releases_widget' )

?>
