<?php use_helper('cleverMediaLibrary'); ?>
<input type="hidden" name="cc_media_librart_widget_fieldname" id="cc_media_librart_widget_fieldname" value="<?php echo $sf_request->getParameter('fieldname') ?>" />

<ul class="clever_media_library_list">
  <?php if (!$cc_media_folder->getRawValue()->getNode()->isRoot()): ?>
    <li class="directory parent">
      <span>
        <?php
        echo cml_link_to('<span>'.$cc_media_folder->getRawValue()->getNode()->getParent()->getName().'</span>',
                     '@cleverMediaLibrary?action=chooseImage&path='.$cc_media_folder->getRawValue()->getNode()->getParent()->getAbsolutePath(),
                     array('rel' => 'facebox'));
        ?>
      </span>
    </li>
  <?php endif; ?>
  <?php foreach ($directories as $directory): ?>
    <li class="directory">
      <span>
        <?php
        echo cml_link_to('<span>'.$directory->getRawValue()->getName().'</span>',
                     '@cleverMediaLibrary?action=chooseImage&path='.$directory->getRawValue()->getAbsolutePath(),
                     array('rel' => 'facebox'));
        ?>
      </span>
    </li>
  <?php endforeach; ?>
  <?php foreach ($files as $cc_media): ?>
    <li>
      <span>
        <?php if ($cc_media->getType() != ''): ?>
          <?php
          echo cml_link_to('<span>'.$cc_media->getTitle().'</span>'.image_tag(sfConfig::get('app_cleverMediaLibraryPlugin_media_root', 'media')
                                 .'/'.cleverMediaLibraryToolkit::getDirectoryForSize('small')
                                 .'/'.$cc_media->getCcMediaFolder()->getAbsolutePath()
                                 .'/'.$cc_media->getThumbnailFilename()),
                       '@cleverMediaLibrary?action=view&path='.$cc_media->getAbsoluteFilename(),
                       array(
                         'class' => 'file '.$cc_media->getType(),
                         'id'    => $cc_media->getPrimaryKey()
                      ));
          ?>
        <?php else: ?>
          <?php
          echo cml_link_to('<span>'.$cc_media->getTitle().'</span>',
                       '@cleverMediaLibrary?action=view&path='.$cc_media->getAbsoluteFilename(),
                       array(
                         'class' => 'file '.$cc_media->getType(),
                         'id'    => $cc_media->getPrimaryKey()
                       ));
          ?>
        <?php endif; ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
  jQuery('a[rel*=facebox]').facebox();

});
//]]>
</script>