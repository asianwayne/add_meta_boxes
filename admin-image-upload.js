jQuery(document).ready(function($){
	//////////////////////////////
	//admin option image upload //
	//////////////////////////////
	///
	var mediaUploader = wp.media({
			title:'Choose a picuter',
			button:{
				text:'choose picture'
			},
			multiple:false
		});

	$('#upload_flogo_button').on('click',function(e){
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			
		}
		
		mediaUploader.on('select',function(){
			attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#abt_flogo_entry').val(attachment.url);
			$('#flogo').val(attachment.url);
		});
		mediaUploader.open();

	});

	$('#upload_home_avatar_button').on('click',function(e){
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			
		}
		
		mediaUploader.on('select',function(){
			attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#abt_home_avatar_entry').val(attachment.url);
			$('#home_avatar').val(attachment.url);
		});
		mediaUploader.open();

	});

	//微信二维码

	$('#upload_modal_wx_img_button').on('click',function(e){
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			
		}
		
		mediaUploader.on('select',function(){
			attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#abt_modal_wx_img_entry').val(attachment.url);
			$('#modal_wx_img').val(attachment.url);
		});
		mediaUploader.open();

	});

	//支付宝二维码

	$('#upload_modal_ali_img_button').on('click',function(e){
		e.preventDefault();
		if (mediaUploader) {
			mediaUploader.open();
			
		}
		
		mediaUploader.on('select',function(){
			attachment = mediaUploader.state().get('selection').first().toJSON();
			$('#abt_modal_ali_img_entry').val(attachment.url);
			$('#modal_ali_img').val(attachment.url);
		});
		mediaUploader.open();

	});

	

});


////////////////////////////
// post meta image upload //
////////////////////////////


var addButton = document.getElementById('image-upload-button');
var deleteButton = document.getElementById('image-delete-button');
var img = document.getElementById('image-tag');
var hidden = document.getElementById('img-hidden-field');
var customUploader = wp.media({
	title:'Select an image',
	button:{
		text:'use this image'
	},
	multiple:false
});


//post meta image upload

var toggleVisibility = function(action) {
	if ('ADD' === action ) {
		addButton.style.display = 'none',
		deleteButton.style.display = '';
		img.setAttribute('style','width:100%;');
	}
	if ('DELETE' === action ) {
		addButton.style.display = '',
		deleteButton.style.display = 'none';
		img.removeAttribute('style');
	}
}

addButton.addEventListener('click',function(){
	if (customUploader) {
		customUploader.open();
	}
});

customUploader.on('select',function(){
	var attachment = customUploader.state().get('selection').first().toJSON();
	img.setAttribute('src',attachment.url);
	img.setAttribute('style','width:100%');
	hidden.setAttribute('value',JSON.stringify([{
		id:attachment.id,
		url:attachment.url,
	}]));  //set value to josn array 
	toggleVisibility('ADD');
});

deleteButton.addEventListener('click',function(){
	img.removeAttribute('src');
	img.removeAttribute('style');
	hidden.removeAttribute('value');
	toggleVisibility('DELETE');
});

//文章页图片自定义字段，当页面刷新时图片依然存在
window.addEventListener('DOMContentLoaded',function(){
	if ( "" === customUploads.imageData || 0 === customUploads.imageData.lenth ) {
		toggleVisibility('DELETE');
	} else {
	img.setAttribute('src',customUploads.imageData.src);
	hidden.setAttribute('value',JSON.stringify([customUploads.imageData])); //JSON.stringify 将js值转换为JSON 
	toggleVisibility('ADD');
}
});

