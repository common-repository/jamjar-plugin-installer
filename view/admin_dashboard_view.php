  
  <div class='jj-main-container wp-core-ui '>
	       <div class='container-fluid'>
	       	<div class='row'>
	       	<?php $this->showBanner();?>
	       	</div>
          <?php $this->admininstance->checkPluginFolderPermission(); ?>
	       	<form action="<?=admin_url( 'admin-ajax.php' )?>?action=jj_plugin_upload" class="dropzone" id='dropper'>
  <div class="fallback">
    <input name="file" type="file" multiple />
  </div>
  <div class='upload_btn_cont'>
  <button id='upload_only_btn'>Install Only</button>
  <button id='upload_activate_btn'>Install And Activate</button>
  </div>
</form>


	       	</div>
</div>