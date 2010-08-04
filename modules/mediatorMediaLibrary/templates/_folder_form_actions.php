<?php use_helper('mediatorMediaLibrary'); ?>
<ul class="sf_admin_actions">
  <li class="sf_admin_action_list"><?php echo cml_link_to(__('Cancel'), 'mediatorMediaLibrary/list?path='.urlencode(urlencode($mm_media_folder->getFolderPath()))) ?></li>
  <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('Save') ?>" /></li>
</ul>