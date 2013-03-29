<?php

/**
 * @file
 * Export UI display customizations.
 */

/**
 * CTools export UI extending class.
 */
class git_include_export_ui extends ctools_export_ui {

  /**
   * Overrides ctools_export_ui::list_form().
   *
   * Simplifies the form similar to how the Context module does it.
   */
  function list_form(&$form, &$form_state) {
    parent::list_form($form, $form_state);
    $form['top row']['submit'] = $form['bottom row']['submit'];
    $form['top row']['reset'] = $form['bottom row']['reset'];
    $form['bottom row']['#access'] = FALSE;
    return;
  }

  /**
   * Overrides ctools_export_ui::list_build_row().
   *
   * Removes the drop button in favor of a horizontal list.
   */
  function list_build_row($item, &$form_state, $operations) {
    parent::list_build_row($item, $form_state, $operations);
      // @todo Make a theme function.
      $label = check_plain($item->options['label']);
      $machine_name = '<small>' . t('(Machine name: @name)', array('@name' => $item->name)) . '</small>';
      $this->rows[$item->name]['data'][0]['data'] = array('#markup' => $label . ' ' . $machine_name);

      $this->rows[$item->name]['data'][2]['data'] = array(
        '#theme' => 'links__node_operations',
        '#links' => $operations,
        '#attributes' => array('class' => array('links', 'inline')),
      );
  }

  /**
   * Overrides ctools_export_ui::list_header()
   *
   * Render a header to go before the list.
   *
   * This will appear after the filter/sort widgets.
   */
  function list_header($form_state) {
    if (isset($form_state['input']['test_result'])) {
      return $form_state['input']['test_result'];
    }
  }

  /**
   * Overrides ctools_export_ui::edit_save_form()
   *
   * Called to save the final product from the edit form.
   */
  function edit_save_form($form_state) {
    $item = &$form_state['item'];
    $export_key = $this->plugin['export']['key'];

    $result = ctools_export_crud_save($this->plugin['schema'], $item);

    if ($result) {
      drupal_static_reset('git_include_load');
      drupal_static_reset('git_include_load_all');
      $message = str_replace('%title', check_plain($item->{$export_key}), $this->plugin['strings']['confirmation'][$form_state['op']]['success']);
      drupal_set_message($message);
    }
    else {
      $message = str_replace('%title', check_plain($item->{$export_key}), $this->plugin['strings']['confirmation'][$form_state['op']]['fail']);
      drupal_set_message($message, 'error');
    }
  }

  /**
   * Overrides ctools_export_ui::delete_form_submit()
   *
   * Deletes exportable items from the database.
   */
  function delete_form_submit(&$form_state) {
    $item = $form_state['item'];
    ctools_export_crud_delete($this->plugin['schema'], $item);
    drupal_static_reset('git_include_load');
    drupal_static_reset('git_include_load_all');
    $export_key = $this->plugin['export']['key'];
    $message = str_replace('%title', check_plain($item->{$export_key}), $this->plugin['strings']['confirmation'][$form_state['op']]['success']);
    drupal_set_message($message);
  }

  /**
   * Main entry point to perform git clone/pull command for an item.
   */
  function run_page($js, $input, $item) {
    watchdog('git_include', 'Running ' . $item->name);

    try {
      git_include($item->name);
      $input['test_result'] = theme_status_messages(array('display' => NULL));
    }
    catch (Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
      watchdog_exception('git_include', $e);
      $input['test_result'] = theme_status_messages(array('display' => NULL));
    }

    watchdog('git_include', 'Finished running ' . $item->name);

    if (!$js) {
      drupal_goto(ctools_export_ui_plugin_base_path($this->plugin));
    }
    else {
      return $this->list_page($js, $input);
    }
  }
}

/**
 * Define the preset add/edit form.
 *
 * @see git_include_routine_form_submit()
 *
 * @ingroup forms
 */
function git_include_routine_form(&$form, &$form_state) {
  $routine = &$form_state['item'];

  if (empty($routine->options)) {
    $routine->options = git_include_new()->options;
  }

  $form['info']['label'] = array(
    '#id' => 'edit-label',
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#default_value' => $routine->options['label'],
    '#description' => t('The human-readable name of the repo.'),
    '#required' => TRUE,
    '#maxlength' => 255,
    '#size' => 30,
  );

  $form['info']['name'] = array(
    '#type' => 'machine_name',
    '#default_value' => $routine->name,
    '#maxlength' => 32,
    '#machine_name' => array(
      'exists' => 'git_include_load',
      'source' => array('info', 'label'),
    ),
    '#disabled' => ('clone' != $form_state['form type'] && !empty($routine->name)),
    '#description' => t('The machine readable name of the synchronization routine. This value can only contain letters, numbers, and underscores.'),
  );

  $form['source_repo'] = array(
    '#type' => 'textfield',
    '#title' => t('Source repository URL'),
    '#default_value' => $routine->options['source_repo'],
    '#description' => t('The Git URL of the source repository being synchronized.'),
    '#required' => TRUE,
  );
}

/**
 * Form submission handler for git_include_routine_form().
 */
function git_include_routine_form_submit($form, &$form_state) {
  $routine = &$form_state['item'];
  form_state_values_clean($form_state);
  $routine->options = $form_state['values'];
  unset($routine->options['delete']);
}
