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
    foreach ($this->rows as $name => $row) {

      // @todo Make a theme function.
      $label = check_plain($item->options['label']);
      $machine_name = '<small>' . t('(Machine name: @name)', array('@name' => $item->name)) . '</small>';
      $this->rows[$name]['data'][0]['data'] = array('#markup' => $label . ' ' . $machine_name);

      $this->rows[$name]['data'][2]['data'] = array(
        '#theme' => 'links__node_operations',
        '#links' => $operations,
        '#attributes' => array('class' => array('links', 'inline')),
      );
    }
  }

  function list_header($form_state) {
    if (isset($form_state['input']['test_result'])) {
      return $form_state['input']['test_result'];
    }
  }


  function run_page($js, $input, $item) {
    //$input['test_result'] = '1';

    watchdog('asdf2', $item->name . ' is going to run');
    try {
      git_include($idftem->name);
    }
    catch (Exception $e) {
      drupal_set_message(t('Git include failed, see log for more details.'), 'error');
      watchdog_exception('git_include', $e);
    }
    watchdog('asdf2', $item->name . ' has run');

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
    '#description' => t('The human-readable name of the synchronization routine.'),
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

  $form['dest_repo'] = array(
    '#type' => 'textfield',
    '#title' => t('Destination repository URL'),
    '#default_value' => $routine->options['dest_repo'],
    '#description' => t('The Git URL of the destination repository being synchronized to.'),
    '#required' => TRUE,
  );

  $form['ssh'] = array(
    '#type' => 'checkbox',
    '#title' => t('Connect to repositories via SSH'),
    '#default_value' => $routine->options['ssh'],
  );

  $form['private_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to private key'),
    '#default_value' => $routine->options['private_key'],
    '#description' => t('Optionally specify a path to a private key used to authenticate against the server hosting the repositories.'),
    '#size' => 90,
    '#states' => array(
      'visible' => array(
        ':input[name="ssh"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['port'] = array(
    '#type' => 'textfield',
    '#title' => t('SSH Port'),
    '#default_value' => $routine->options['port'],
    '#description' => t('The port that the SSH server hosting the source repository listens on.'),
    '#size' => 6,
    '#states' => array(
      'visible' => array(
        ':input[name="ssh"]' => array('checked' => TRUE),
      ),
    ),
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
