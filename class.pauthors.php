<?php

class Pauthors {
	
	/**
     * Register actions & Filters
     */    
	public function register()
	{
		add_action( 'add_meta_boxes', array( $this, 'pauthors_list_meta_box' ) );
		add_action( 'save_post', array( $this, 'pauthors_save_meta_box' ) );
		add_filter( 'the_content',  array( $this, 'pauthors_display_before_post' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' )  );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueuefront' )  );
	}
	
	/**
     * Ad meta box in the post
     */
	public function pauthors_list_meta_box() 
	{
		$list = 'post' ;
		add_meta_box( 'author-list', __( 'Select Contributors', 'textdomain' ), array( $this, 'pauthors_list_metabox_callback' ), 'post' );

	}
	
	/**
     * meta box xall back function to generate form fields
     */
	public function pauthors_list_metabox_callback( $post ) 
	{
		wp_nonce_field( 'pauthors_nonce', 'pauthors_nonce' );
		
		$value = get_post_meta( $post->ID, 'pauthorst', true );
		
		//get author list
		$authors_list =  get_users( 'role=author' );
		
		$value = get_post_meta( $post->ID, 'pauthorst', true );
		
		if( !empty( $authors_list ) ){
			foreach ( $authors_list as $k=>$v ){
				$each_user = $v->data;
				$check = '';
				if ( is_array ($value) && in_array( $each_user->ID, $value ) )
					$check = 'checked';
				echo '<span class="pauthor_each_opt"><input '. $check .'  type="checkbox" name="pauthorst[]" value="'.$each_user->ID.'" />'.$each_user->display_name.'</span><br/>';
			}	
		}else{
			echo "No authors found in the site";
		}
	}
	
	/**
     * Save metabox fields
     */
	public function pauthors_save_meta_box( $post_id ) 
	{
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['pauthors_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['pauthors_nonce'], 'pauthors_nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		}
		else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/*if ( ! isset( $_POST['pauthorst'] ) ) {
			return;
		}*/

		
		$save_data = $_POST['pauthorst'];
		
		// Update the meta field in the database.
		update_post_meta( $post_id, 'pauthorst', $save_data );
	}
	
	/**
     * Display metabox field values in front end
     */
	function pauthors_display_before_post( $content ) 
	{
		global $post;
		
		// retrieve the global notice for the current post
		$pauthors_list =  get_post_meta( $post->ID, 'pauthorst', true ) ;
		if ( !empty( $pauthors_list ) ){
			
			$list = '<div id="contPlist"><p>Contributors</p>';
		
			foreach ( $pauthors_list as $k => $v ) {
				
				$each_user = get_userdata( $v );// print_r($each_user);
				$burl = get_author_posts_url( $each_user->data->ID ) ;
				$user_email = $each_user->data->user_email;
				$list .= '<div class="contPlistEachAv"><p class="my-author-gravatar">' . get_avatar( get_the_author_meta($user_email) , 90 ) ;		
				$list .= '<a target ="_blank" href="'. $burl .'" class="contPlistEachname">'.$each_user->data->display_name.'</a></p></div>';
			}
			$list .= '</div>';
		}		
		
		return $content . $list;

	}
	
	/** 
     * Plugin activation code
     */
	public static function activate()
	{
		flush_rewrite_rules();
	}
	
	/** 
     * Plugin deactivation 
     */
	public static function deactivate()
	{
		flush_rewrite_rules();
	}
	
	/** 
     * Load admin end script and styles
     */
	public static function enqueue()
	{
		//enqueue all our scripts
		wp_register_style( 'pstyle.css', plugin_dir_url( __FILE__ ) . 'assets/pstyle.css', array(), NALERT_VERSION, 'all');
		wp_enqueue_style( 'pstyle.css');
		
		wp_register_script( 'pauthor.js', plugin_dir_url( __FILE__ ) . 'assets/pauthor.js', array('jquery'), NALERT_VERSION );
		wp_enqueue_script( 'pauthor.js' );
	}
	
	/** 
     * Load front end script and styles
     */
	public static function enqueuefront()
	{
		//enqueue all our scripts
		wp_register_style( 'pstyle.css', plugin_dir_url( __FILE__ ) . 'assets/pstyle.css', array(), NALERT_VERSION, 'all');
		wp_enqueue_style( 'pstyle.css');
		
		wp_register_script( 'pauthor.js', plugin_dir_url( __FILE__ ) . 'assets/pauthor.js', array('jquery'), NALERT_VERSION );
		wp_enqueue_script( 'pauthor.js' );
	}
	
	
}

	 
