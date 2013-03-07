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
);
