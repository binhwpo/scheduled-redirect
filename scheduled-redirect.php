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
	$actions = array( 'redirect' => __( 'Redirect', 'scheduled-redirect' ) );
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
	/* Remove the Scheduled Content Actions javascript file */
	unset( $scripts['sca-backend'] ); 
	
	/* Add Scheduled Redirect javascript file */
	$scripts['sr_redirect'] = array(
		'src'       => plugin_dir_url( __FILE__ ) . 'scheduled-redirect.js',
		'deps'      => array( 'jquery' ),
		'version'   => '',
		'in_footer' => TRUE,
		'localize'  => array(
			'sca_vars' => array(
				'label_taxonomy'   => __( 'Taxonomy', 'scheduled-content-actions' ),
				'label_term'       => __( 'Term', 'scheduled-content-actions' ),
				'label_meta_name'  => __( 'Meta Name', 'scheduled-content-actions' ),
				'label_meta_value' => __( 'Meta Value', 'scheduled-content-actions' ),
				'label_title'      => __( 'Change Title', 'scheduled-content-actions' ),
			)
		),
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
	$pages = get_pages( ) ;
	?>
	<p>
		<label for="sr_subpage_url">
			<?php _e( 'Choose a page', 'scheduled-redirect' ); ?>
		</label>
		<select class="large-text" id="sr_subpage_url">
			<option value=""><?php _e( 'No page', 'scheduled-redirect' ); ?></option>
			<?php foreach ( $pages as $page ) : ?>
				<option value="<?php echo get_permalink($page->ID); ?>"><?php echo $page->post_title; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="sr_redirect_url">
			<?php _e( 'Redirect URL', 'scheduled-redirect' ); ?>			
		</label>
		<input type="text" class="large-text" name="redirect_url" id="sr_redirect_url" />
	</p>
	<?php
	exit;
}

/* Save action */

function sr_save_redirect_action( $action, $aRequestData ) {
	if ( $aRequestData[ 'type' ] == 'redirect' ) {
		$action['redirect_url'] = $aRequestData['redirect_url'];
	}
	return $action;
} 
add_filter( 'sca_add_action', 'sr_save_redirect_action', 10, 2 );

/* Handle redirect action */

function sr_redirect( $action ) {
	if ( $action['post_id'] == get_the_ID() ) {
		wp_redirect( $action['redirect_url'] );
		exit;
	}
}

/* Handle redirect using jQuery */

function sr_js_redirect( $action, $actionTime ) {
	if ( $action['post_id'] == get_the_ID() ) { 
		$timeOut = 1000 * ( $actionTime - current_time( 'timestamp' ) ) ;
		?>
		<script type="text/javascript">
			setTimeout(function () {
			   window.location.href = "<?php echo $action['redirect_url']; ?>";
			}, <?php echo $timeOut ?>);
		</script>
<?	}
}

/* Handle the scheduled actions */

function sr_scheduler() {

	$aCurrentActions = get_option( '_sca_current_actions' );
	if ( empty( $aCurrentActions ) )
		return;

	foreach ( $aCurrentActions as $iPostId => $aTiming ) {
		foreach ( $aTiming as $iTime => $aActions ) {
			foreach ( $aActions as $aAction ) {
				$aAction[ 'post_id' ] = $iPostId;

				if ( $aAction[ 'type' ] == 'redirect' ) { 
					/* Deal with redirect action */
					if ( $iTime > current_time( 'timestamp' ) ) {
						sr_js_redirect( $aAction, $iTime );
					} else {
						sr_redirect( $aAction );
					}
					/* Would not delete the redirect action for now */
				} else if ( $iTime <= current_time( 'timestamp' ) ) {
					/* Deal with other actions */
					do_action( 'sca_do_' . $aAction[ 'type' ], $aAction );
					sca_delete_action( $aAction[ 'post_id' ], $aAction[ 'type' ], $iTime );
				}
			}
		}
	}
}

/* Replace action of Scheduled Content Actions with Scheduled Redirect */

function sr_init() {
	remove_action( 'wp_loaded', 'sca_scheduler' );
	add_action( 'wp', 'sr_scheduler' );
}
add_action( 'plugins_loaded', 'sr_init' );


