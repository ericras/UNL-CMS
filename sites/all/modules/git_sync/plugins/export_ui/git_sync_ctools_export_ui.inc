<?php

/**
 * @file
 * CTools Export UI plugin for current search block configurations.
 */

$plugin = array(
  'schema' => 'git_sync',
  'access' => 'administer git sync routines',

  'menu' => array(
    'menu prefix' => 'admin/config/system',
    'menu item' => 'git-sync',
    'menu title' => 'Git Sync',
    'menu description' => 'Configure Git repository synchronization routines.',
    'items' => array(
      'run' => array(
        'path' => 'list/%ctools_export_ui/run',
        'title' => 'Run',
        'page callback' => 'ctools_export_ui_switcher_page',
        //'page callback' => 'git_sync_run',
        'page arguments' => array('git_sync_ctools_export_ui', 'run', 5),
        'load arguments' => array('git_sync_ctools_export_ui'),
        'file' => 'includes/export-ui.inc',
        'file path' => drupal_get_path('module', 'ctools'),
        'access arguments' => array('administer git sync routines'), //TODO add perm run from gui
        'type' => MENU_CALLBACK,
      ),
    ),
  ),

  'title singular' => t('routine'),
  'title plural' => t('routines'),
  'title singular proper' => t('Git sync routine'),
  'title plural proper' => t('Git sync routines'),

  'form' => array(
    'settings' => 'git_sync_routine_form',
    'submit' => 'git_sync_routine_form_submit',
  ),

  'handler' => array(
    'class' => 'git_sync_export_ui',
    'parent' => 'ctools_export_ui',
  ),

  'allowed operations' => array(
    'run' => array('title' => t('Run'), 'ajax' => TRUE, 'token' => FALSE),
  ),
);
