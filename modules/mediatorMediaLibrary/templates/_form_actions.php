<?php use_helper('mediatorMediaLibrary'); ?>
<ul class="sf_admin_actions">
  <li class="sf_admin_action_list"><?php echo cml_link_to(__('Cancel'), 'mediatorMediaLibrary/list?path='.$mm_media_folder->getRawValue()->getAbsolutePath()) ?></li>
  <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('Save') ?>" /></li>
</ul>