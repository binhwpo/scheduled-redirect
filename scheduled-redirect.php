<?php
/**
 * Plugin Name: Scheduled Redirect
 * Plugin URI:  https://wpos.org/#scheduled-redirect
 * Description: This plugin provides several redirection action for Scheduled Content Actions plugin.
 * Version:     1.2
 * Author:      Binh Nguyen
 * Author URI:  http://www.binh.vn/
 * Text Domain: scheduled-redirect
 */

/* Add redirect to the list of actions */

add_filter( 'sca_get_actions', function( $actions ) {
	$actions = array( 'redirect' => __( 'Redirect', 'scheduled-redirect' ) ) + $actions;
	return $actions;
});

/* Show action settings in post */
function sr_load_additional_form_data( $type ) {

	// check what form data we should load
	if ( $type == 'redirect' ) {
		sr_load_redirect_form();
	}
}
add_action( 'sca_load_additional_form_data', 'sr_load_additional_form_data', 10, 1 );

/* Enqueue javascript to handle the form data */
function sr_admin_scripts( $scripts ) {

	$scripts['sr_redirect'] = array(
		'src'       => plugin_dir_url( __FILE__ ) . 'scheduled-redirect.js',
		'deps'      => array( 'sca-backend' ),
		'version'   => SCRIPT_DEBUG === TRUE ? time() : '1.0',
		'in_footer' => TRUE,
	);

	return $scripts;
}
add_filter( 'sca_get_admin_scripts', 'sr_admin_scripts', 10, 1 );


/**
 * Form inputs for the redirect called at sca_load_additional_form_data()
 *
 * @return	void
 */
function sr_load_redirect_form() {
	$pages = get_pages( array( 'child_of' => $_REQUEST[ 'post_id' ] ) ) ;
	?>
	<p>
		<label for="sr_redirect">
			<select class="large-text" name="redirect_url" id="sr_redirect">
				<option value=""><?php _e( 'Choose a Page', 'scheduled-redirect' ); ?></option>
				<?php foreach ( $pages as $page ) : ?>
					<option value="<?php echo get_permalink($page->ID); ?>"><?php echo $page->post_title; ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>
	<?php
	exit;
}

/* Save action */

function sr_save_redirect_action( $action, $aRequestData ) {
	$sActionType = $aRequestData[ 'type' ];
	if ( $sActionType == 'redirect' ) {
		$action['url'] = $aRequestData['url'];
	}
	return $action;
} 
add_filter( 'sca_add_action', 'sr_save_redirect_action', 10, 2 );

/* Handle redirect action */

function sr_redirect( $action ) {
	wp_redirect( $action['url'] );
	exit;
}
