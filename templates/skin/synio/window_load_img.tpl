<div class="modal modal-image-upload" id="window_upload_img">
	<header class="modal-header">
		<h3>{$aLang.uploadimg}</h3>
		<a href="#" class="close jqmClose"></a>
	</header>
	
	<div class="modal-content">
		<ul class="nav nav-pills nav-pills-tabs">
			<li class="active js-block-upload-img-item" data-type="pc"><a href="#">{$aLang.uploadimg_from_pc}</a></li>
			<li class="js-block-upload-img-item" data-type="link"><a href="#">{$aLang.uploadimg_from_link}</a></li>
		</ul>
	
		<form method="POST" action="" enctype="multipart/form-data" id="block_upload_img_content_pc" onsubmit="return false;" class="tab-content js-block-upload-img-content" data-type="pc">
			<p><label for="img_file">{$aLang.uploadimg_file}:</label>
			<input type="file" name="img_file" id="img_file" value="" class="input-text input-width-full" /></p>
			
			{hook run="uploadimg_source"}
			
			<p><label for="form-image-title">{$aLang.uploadimg_title}:</label>
			<input type="text" name="title" id="form-image-title" value="" class="input-text input-width-full" /></p>
			
			{hook run="uploadimg_additional"}
			
			<button type="submit"  class="button button-primary main-upl-btn" onclick="ls.tools.uploadImg('block_upload_img_content_pc','{$sToLoad}');">{$aLang.uploadimg_submit}</button>
			<button type="submit"  class="button jqmClose">{$aLang.uploadimg_cancel}</button>
		</form>
		
		
		<form method="POST" action="" enctype="multipart/form-data" id="block_upload_img_content_link" onsubmit="return false;" style="display: none;" class="tab-content js-block-upload-img-content" data-type="link">
			<p><label for="img_file">{$aLang.uploadimg_url}:</label>
			<input type="text" name="img_url" id="img_url" value="http://" class="input-text input-width-full" /></p>

			<p><label for="form-image-url-title">{$aLang.uploadimg_title}:</label>
				<input type="text" name="title" id="form-image-url-title" value="" class="input-text input-width-full" /></p>

			{hook run="uploadimg_link_additional"}

			<button type="submit"  class="button button-primary" onclick="ls.tools.uploadImg('block_upload_img_content_link','{$sToLoad}');">{$aLang.uploadimg_link_submit_load}</button>
			<button type="submit"  class="button jqmClose">{$aLang.uploadimg_cancel}</button>
		</form>
	</div>
</div>
