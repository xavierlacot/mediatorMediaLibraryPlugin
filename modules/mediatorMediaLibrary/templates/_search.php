<?php use_javascript('/sfFormExtraPlugin/js/jquery.autocompleter.js') ?>
<?php use_stylesheet('/sfFormExtraPlugin/css/jquery.autocompleter.css') ?>

<div class="mediator_media_library_box" id="mediator_media_library_search">
  <h2><?php echo __('Search') ?></h2>

  <form action="<?php echo url_for('mediatorMediaLibrary/search') ?>" method="post" id="mediator_media_library_search_form">
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

