<?php include_partial('mediatorMediaLibrary/flash'); ?>
<?php include_component('mediatorMediaLibrary', 'list', array('mm_media_folder' => $mm_media_folder, 'action' => isset($action) ? $action : 'list')); ?>