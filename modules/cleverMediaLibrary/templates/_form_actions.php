<?php use_helper('cleverMediaLibrary'); ?>
<ul class="sf_admin_actions">
  <li class="sf_admin_action_list"><?php echo cml_link_to(__('Cancel'), 'cleverMediaLibrary/list?path='.$cc_media_folder->getRawValue()->getAbsolutePath()) ?></li>
  <li class="sf_admin_action_save"><input type="submit" value="Save" /></li>
</ul>