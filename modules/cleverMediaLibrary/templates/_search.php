<?php use_helper('Form'); ?>
<?php use_javascript('/sfFormExtraPlugin/js/jquery.autocompleter.js') ?>
<?php use_stylesheet('/sfFormExtraPlugin/css/jquery.autocompleter.css') ?>

<div class="clever_media_library_box" id="clever_media_library_search">
  <h2><?php echo __('Search') ?></h2>

  <?php echo form_tag('cleverMediaLibrary/search', array('id' => 'clever_media_library_search_form')) ?>
    <fieldset>
      <div class="form-row">
        <?php echo $search_form['name']->renderLabel() ?>
        <div class="content">
          <?php echo $search_form['name']->renderError() ?>
          <?php echo $search_form['name']->render(array('class' => 'text required')) ?>
          <input type="submit" value="<?php echo __('ok') ?>" />
        </div>
      </div>
    </fieldset>
  </form>
</div>

