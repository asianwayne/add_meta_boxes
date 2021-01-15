<?php
/////////////
//Meta box //
//         //
//         //
//         //
//         //
//         //
//Hooks    //
/////////////
	add_action('add_meta_boxes','abt_page_meta_boxes');
	add_action('save_post','abt_save_page_seo_meta');
	add_action('save_post','abt_save_contact_email_data');
	add_action('save_post','save_custom_image');
	add_action('save_post','save_post_seo_meta');
	add_action('admin_enqueue_scripts','abt_image_upload_meta_script');


		/**
		 * [abt_page_meta_boxes description]
		 * @return [type] [description]
		 */
		
		function abt_page_meta_boxes() {
			global $post;
			
			if ( $post->ID == 5 ) {
				add_meta_box( 'contact_email','User Email','abt_contact_email_callback','page','normal','high' ); //$id, $title, $callback, $screen, $context, $priority, $callback_args
			}

			add_meta_box('page_seo_keyword','Page Seo Meta','abt_page_seo_meta_callback','page','normal','default');
			add_meta_box( 'abt_post_img_meta', __('缩略图二','ablog-theme'), 'abt_post_img_meta_callback', 'post', 'side', 'default' );
			add_meta_box('abt_post_seo_meta',__('Post Seo Meta','ablog-theme'),'abt_post_seo_meta_callback','post','advanced','high');
			
		}


		///////////////////
		///////////////////////////////
		//image uploader function // //
		///////////////////////////////
		///////////////////

		function abt_image_upload_meta_script() {
			wp_enqueue_media();
			wp_enqueue_script('image-upload',get_template_directory_uri(). '/framework/js/admin-image-upload.js',array('jquery','media-upload'),'1.0.0',true);
			wp_localize_script('image-upload','customUploads',array(
				'imageData'  => get_post_meta(get_the_ID(),'custom_image_data',true),
			));  //pass data from wordpress into javascript so that we can use 
		}

		function abt_post_img_meta_callback($post) {
			wp_nonce_field( basename(__FILE__),'custom_image_nonce' );
			$image_data = get_post_meta($post->ID,'custom_image_data',true);
			 ?>

			<div id="metabox-wrapper">
				
				<img id="image-tag">
				<input type="hidden" id="img-hidden-field" name="custom_image_data">
				<input type="button" id="image-upload-button" class="button" value="Add Image">
				<input type="button" id="image-delete-button" class="button" value="Remove Image">
			</div>


			<?php
		}

		function save_custom_image($post_id) {
			 $is_autosave = wp_is_post_autosave( $post_id );
			 $is_revision = wp_is_post_revision( $post_id );
			
			$is_valid_nonce = ( isset($_POST['custom_image_nonce']) && wp_verify_nonce($_POST['custom_image_nonce'],basename( __FILE__ )));
			 if ($is_autosave || $is_revision || !$is_valid_nonce) {
			 	return;
			 }

			if (isset($_POST['custom_image_data'])) {
				$image_data = json_decode(stripslashes($_POST['custom_image_data']));
				
				 //json_decode 接受一个json编码的字符串并解码，stripslashes删除反斜杠;
				if (is_object($image_data[0])) {
					$image_data = array(
						'id'  => intval($image_data[0]->id),
						'src' => esc_url_raw( $image_data[0]->url )
					);
				} else {
					$image_data = [];
				}
				update_post_meta($post_id,'custom_image_data',$image_data);

				# code...
			}
		}

		
		function abt_post_seo_meta_callback($post) {
			wp_nonce_field(basename(__FILE__),'custom_post_meta_nonce');
			$post_keyword = get_post_meta( $post->ID,'post_seo_keyword',true );
			$post_content = get_post_meta($post->ID,'post_seo_content',true);
			$post_src = get_post_meta($post->ID,'post_src',true);
			?>
			<div class="abt-field-row">
				<label for="post-seo-keyword"><?php echo __('Seo Title','ablog-theme'); ?></label>
				<input type="text" id="post-seo-keyword" name="post_seo_keyword" value="<?php echo $post_keyword ?>" class="widefat">
			</div>
			
			<div class="abt-field-row">
				<label for="post_seo_content"><?php echo __( 'Seo Description' ); ?></label>
				<textarea name="post_seo_content" id="post_seo_content" cols="30" rows="5" class="widefat"><?php echo $post_content; ?></textarea>
				
			</div>

			<div class="abt-field-row">
				<label for="post_src"><?php echo __( '来源链接' ); ?></label>
				<input type="text" id="post_src" name="post_src" value="<?php echo $post_src ?>" class="widefat">
				
			</div>

			<?php
			
		}

		function save_post_seo_meta($post_id) {
			if (!isset($_POST['custom_post_meta_nonce'])) {
				return;
			}
			if (!wp_verify_nonce($_POST['custom_post_meta_nonce'],basename(__FILE__))) {
				return;
			}
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}
			if (!current_user_can( 'edit_post',$post_id )) {
				return;
			}

			if (!isset($_POST['post_seo_keyword'])) {
				return;
			}
			if (!isset($_POST['post_seo_content'])) {
				return;
			}
			if (!isset($_POST['post_src'])) {
				return;
			}


			$post_keyword = sanitize_text_field( $_POST['post_seo_keyword'] );
			$post_content = sanitize_textarea_field( $_POST['post_seo_content'] );
			$post_src = sanitize_text_field( $_POST['post_src'] );
			update_post_meta($post_id,'post_seo_keyword',$post_keyword);
			update_post_meta( $post_id,'post_seo_content',$post_content );
			update_post_meta( $post_id, 'post_src',$post_src );
		}

		

		////////////////////////////////////////////
		//function seo meta box for page callback //
		////////////////////////////////////////////

		function abt_page_seo_meta_callback($post) {
			wp_nonce_field('abt_save_page_seo_meta','abt_save_page_seo_meta_nonce');
			$keyword = get_post_meta($post->ID,'page_seo_keyword',true);
			$content = get_post_meta($post->ID,'page_seo_content',true); ?>

			<div class="abt-field-row">
				<label for="page-seo-keyword">Page Keyword</label>
				<input type="text" id="page-seo-keyword" name="page_seo_keyword" value="<?php echo $keyword ?>" class="widefat">
			</div>
			
			<div class="abt-field-row">
				<label for="page_seo_content">Page Description</label>
				<textarea name="page_seo_content" id="page_seo_content" cols="30" rows="5" class="widefat"><?php echo $content; ?></textarea>
				
			</div>

			<?php 
		}

		function abt_save_page_seo_meta($post_id) {
			if (!isset($_POST['abt_save_page_seo_meta_nonce'])) {
				return;
			}
			if (!wp_verify_nonce($_POST['abt_save_page_seo_meta_nonce'],'abt_save_page_seo_meta')) {
				return;
			}
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}
			if (!current_user_can( 'edit_post',$post_id )) {
				return;
			}

			if (!isset($_POST['page_seo_keyword'])) {
				return;
			}
			if (!isset($_POST['page_seo_content'])) {
				return;
			}


			$page_keyword = sanitize_text_field( $_POST['page_seo_keyword'] );
			$page_content = sanitize_textarea_field( $_POST['page_seo_content'] );
			update_post_meta($post_id,'page_seo_keyword',$page_keyword);
			update_post_meta( $post_id,'page_seo_content',$page_content );

		}


		/////////////////////////////////////////////////////
		//function contact email callback for contact page //
		/////////////////////////////////////////////////////

		function abt_contact_email_callback($post) {  //$post 从add meta box 传递
			wp_nonce_field('abt_save_contact_email_data','abt_contact_email_meta_nonce'); //生成验证的字符串
			$value = get_post_meta($post->ID,'_abt_contact_email_field',true); // 第三个bool值 是代表这个是single value 而不是array value等。
			$address = get_post_meta($post->ID,'abt_contact_text_field',true);

			echo "<label for=\"abt_contact_email_field\">User Email Add: </label>";
			echo "<input type=\"email\" id=\"_abt_contact_email_field\" name=\"_abt_contact_email_field\" value=\"".  esc_attr($value) . "\" size=\"25\"";
			echo "<br>";

			echo "<label for=\"abt_contact_text_field\">User Text Field: </label>";
			echo "<input type=\"text\" id=\"abt_contact_text_field\" name=\"abt_contact_text_field\" value=\"".  esc_attr($address) . "\" size=\"25\"";
		}

		//updata fileds
		
		function abt_save_contact_email_data($post_id) {
			if (!isset($_POST['abt_contact_email_meta_nonce'])) {
				return;
			}
			if (!wp_verify_nonce($_POST['abt_contact_email_meta_nonce'],'abt_save_contact_email_data')) {
				return;
			}
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}
			if (!current_user_can( 'edit_post',$post_id )) {
				return;
			}

			if (!isset($_POST['_abt_contact_email_field'])) {
				return;
			}
			if (!isset($_POST['abt_contact_text_field'])) {
				return;
			}

			$my_data = sanitize_text_field( $_POST['_abt_contact_email_field'] );
			$my_add = sanitize_text_field( $_POST['abt_contact_text_field'] );
			update_post_meta($post_id,'_abt_contact_email_field',$my_data);
			update_post_meta($post_id,'abt_contact_text_field',$my_add);

		}


