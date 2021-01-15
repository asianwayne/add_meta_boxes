# add_meta_boxes
add_meta_boxes
不用插件自己添加文章或者页面自定义字段的方法
首先在主题下面创建inc或者framework的文件夹，
把functions_metxboxes.php和admin-upload-image.js放进去，文件名字可以自己随便取。

然后在functions.php里面require进来：
require get_template_directory() . '/framework/function_metaboxes.php';
functions_metxboxes.php已经包含了一切必要的钩子注册和引入了admin-upload-image.js，
注意文件夹位置的引用。

要新增字段的话，首先要在第一个主要函数abt_page_meta_boxes（）里面添加metaboxes，比如：

add_meta_box('abt_post_seo_meta',__('Post Seo Meta','ablog-theme'),'abt_post_seo_meta_callback','post','advanced','high');

添加的只是一个区域的盒子，盒子里面你可以添加多个字段。
首先你创建他的callback函数，创建input输入框：注意里面nonce创建，很重要，用来保存时的验证

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
    
    然后创建验证函数，来保存和更新Postmeta：
    
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
    
    注意get_post_meta里面的第二个值meta_key和update_post_meta里面第二个值meta_key均为你创建的input输入框的name值。
    
    wp已经安排好了一切。
    

注意要用到图片上传字段的话要用到Js来调用wp.media的图片上传输入框，js文件在admin-upload.js里面。通过js将图片选择信息导入到json里面去。


