<?php

/**
 * @file
 * CTools Export UI plugin for current repo configurations.
 */

$plugin = array(
  'schema' => 'git_include',
  'access' => 'add new git include',

  'menu' => array(
    'menu prefix' => 'admin/config/development',
    'menu item' => 'git-include',
    'menu title' => 'Git include (ctools)',
    'menu description' => 'Configure Git repository clone/pull routines.',
    'items' => array(
      'run' => array(
        'path' => 'list/%ctools_export_ui/run',
        'title' => 'Run',
        'page callback' => 'ctools_export_ui_switcher_page',
        //'page callback' => 'git_include_run',
        'page arguments' => array('git_include_ctools_export_ui', 'run', 5),
        'load arguments' => array('git_include_ctools_export_ui'),
        'file' => 'includes/export-ui.inc',
        'file path' => drupal_get_path('module', 'ctools'),
        'access arguments' => array('execute git include pull'),
        'type' => MENU_CALLBACK,
      ),
    ),
  ),

  'title singular' => t('repository'),
  'title plural' => t('repositories'),
  'title singular proper' => t('Git include repository'),
  'title plural proper' => t('Git include repositories'),

  'form' => array(
    'settings' => 'git_include_routine_form',
    'submit' => 'git_include_routine_form_submit',
  ),

  'handler' => array(
    'class' => 'git_include_export_ui',
    'parent' => 'ctools_export_ui',
  ),

  'allowed operations' => array(
    'run' => array('title' => t('Run'), 'ajax' => TRUE, 'token' => FALSE),
  ),
);
