<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>

<fieldset id="sf_fieldset_none" class="media_description">
  <legend><?php echo __('General') ?></legend>

  <?php if ($form->hasGlobalErrors()): ?>
    <?php echo $form->renderGlobalErrors() ?>
  <?php endif; ?>

  <?php if ('MmMultiMediaDescriptionForm' === get_class($form)): ?>
    <div class="sf_admin_form_row sf_admin_text">
      <div id="media_description_master_tags_container">
        <label for="media_description_master_tags"><?php echo __('Tags') ?></label>
        <input id="media_description_master_tags" value="" />
        <input type="button" value="<?php echo __('Assign these tags to all the medias') ?>" id="media_description_master_tags_copy_button" />
        <script type="text/javascript">
          jQuery(document).ready(function() {
            jQuery("#media_description_master_tags_copy_button").click(function() {
              var tags = jQuery('#media_description_master_tags_container input.as-values').val()
                + jQuery('#media_description_master_tags_container input.as-input').val();
              var tag_array = tags.split(',');

              if (tags != '') {
                jQuery('.media_description_form input.as-values').each(function() {
                  var values_input = $(this);
                  var org_li = $(this).parent();
                  var as_input = jQuery('input.as-input', org_li);

                  jQuery.each(tag_array, function(i, val) {
                    if ('' !== val && values_input.val().search(val+',') == -1) {
                      // add the tag in the values field
                      values_input.val(val + ',' + values_input.val());

                      // add it in the display also
                			var item = $('<li class="as-selection-item blur">'+val+'</li>').click(function(){
                				$(this).addClass("selected");
                			}).mousedown(function(){ input_focus = false; });
                			var close = $('<a class="as-close">&times;</a>').click(function(){
                				values_input.val(values_input.val().replace(val+",", ""));
                				item.remove();
                				as_input.focus();
                				return false;
                			});
                			org_li.before(item.prepend(close));
                    }
                  });
                });
                jQuery('#media_description_master_tags_container input.as-values').val('');
                jQuery('#media_description_master_tags_container input.as-input').val('');
                jQuery('#media_description_master_tags_container .as-selection-item').remove();
              }
            });
            jQuery("#media_description_master_tags")
            .autoSuggest(
              "<?php echo $autocomplete_url ?>",
              { minChars: 2, matchCase: true, retrieveLimit: 10, startText: "" },
              ""
            );

            <?php if (true === sfConfig::get('app_mediatorMediaLibraryPlugin_asynchronous', false)): ?>
              // if asynchronous, check when files are ready
                var loadVariations = setInterval(function() {
                  $.getJSON(
                    '<?php echo url_for('@mediatorMediaLibrary_variations?media_ids='.$uuids)?>?randval='+ Math.random(),
                    function(data) {
                      $.each(data, function(i, item){
                        $("#sf_admin_form_row_preview_" + i).html(item);
                      });

                      if (<?php echo count(explode(',', $uuids)) ?> <= data.length + 1) {
                        // completed all the variations, can stop ajax calls
                        clearInterval(loadVariations);
                      }
                    });
                  },
                  // every 5s.
                  5000
                );
            <?php endif; ?>
          });
        </script>
      </div>
    </div>
  <?php endif; ?>

  <div class="media_description_form_row">
  <?php foreach ($form as $name => $media_description_form): ?>
    <?php if (0 === strpos($name, 'media_')): ?>
      <div class="media_description_form">
        <div class="sf_admin_form_row sf_admin_text sf_admin_form_row_preview">
          <div id="sf_admin_form_row_preview_<?php echo substr($name, 6) ?>">
            <?php echo $form->getObject(substr($name, 6))->getDisplay(array('size' => 'medium')) ?>
          </div>
        </div>
        <div class="sf_admin_form_row sf_admin_text">
          <div>
            <?php echo $media_description_form['title']->renderError() ?>
            <?php echo $media_description_form['title']->renderLabel() ?>
            <?php echo $media_description_form['title']->render() ?>
            <div class="help"><?php echo $media_description_form['title']->renderHelp() ?></div>
          </div>
        </div>
        <div class="sf_admin_form_row sf_admin_text">
          <div>
            <?php echo $media_description_form['body']->renderError() ?>
            <?php echo $media_description_form['body']->renderLabel() ?>
            <?php echo $media_description_form['body']->render() ?>
            <div class="help"><?php echo $media_description_form['body']->renderHelp() ?></div>
          </div>
        </div>
        <div class="sf_admin_form_row sf_admin_text">
          <div>
            <?php echo $media_description_form['tags']->renderError() ?>
            <?php echo $media_description_form['tags']->renderLabel() ?>
            <?php echo $media_description_form['tags']->render() ?>
            <div class="help"><?php echo $media_description_form['tags']->renderHelp() ?></div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  </div>

  <?php echo $form->renderHiddenFields() ?>
</fieldset>

<ul class="sf_admin_actions">
  <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('save') ?>" /></li>
</ul>