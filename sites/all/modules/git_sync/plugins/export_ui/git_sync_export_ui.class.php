<?php

/**
 * @file
 * Export UI display customizations.
 */

/**
 * CTools export UI extending class.
 */
class git_sync_export_ui extends ctools_export_ui {

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
}

/**
 * Define the preset add/edit form.
 *
 * @see git_sync_routine_form_submit()
 *
 * @ingroup forms
 */
function git_sync_routine_form(&$form, &$form_state) {
  $routine = &$form_state['item'];

  if (empty($routine->options)) {
    $routine->options = git_sync_new()->options;
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
      'exists' => 'git_sync_load',
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

  $options = array();
  $synchronizers = git_sync_synchronizers_load_all();
  foreach ($synchronizers as $type => $info) {
    $options[$type] = check_plain($info['label']);
  }

  $form['type'] = array(
    '#type' => 'radios',
    '#title' => t('Synchronization type'),
    '#options' => $options,
    '#default_value' => $routine->options['type'],
  );

  foreach ($synchronizers as $type => $info) {
    if (isset($info['settings callback'])) {
      if (function_exists($info['settings callback'])) {
        $info['settings callback']($form, $form_state, $routine);
      }
      else {
        $args = array('@function' => $info['settings callback']);
        watchdog('git_sync', 'Function does not exist: @function', $args, WATCHDOG_ERROR);
      }
    }
  }
}

/**
 * Form submission handler for git_sync_routine_form().
 */
function git_sync_routine_form_submit($form, &$form_state) {
  $routine = &$form_state['item'];
  form_state_values_clean($form_state);
  $routine->options = $form_state['values'];
  unset($routine->options['delete']);
}
