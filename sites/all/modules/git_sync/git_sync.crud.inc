<?php

/**
 * @file
 * CRUD functions for Git Sync configurations.
 */

/**
 * Gets all sync routine configurations.
 *
 * @return array
 *   An associative array keyed by the machine name of the routine to the
 *   configuration object.
 */
function git_sync_routine_load_all() {
  $routines = &drupal_static(__FUNCTION__);
  if (NULL === $routines) {
    ctools_include('export');
    $routines = ctools_export_crud_load_all('git_sync');
  }
  return $routines;
}

/**
 * Returns an empty Git sync routine configuration object with defaults.
 *
 * @return stdClass
 */
function git_sync_new() {
  ctools_include('export');
  $routine = ctools_export_crud_new('git_sync');
  $routine->options = array(
    'label' => '',
    'source_repo' => '',
    'dest_repo' => '',
    'type' => 'mirror',
    'ssh' => variable_get('git_wrapper_connect_via_ssh', 0),
    'private_key' => variable_get('git_wrapper_private_key', ''),
    'port' => variable_get('git_wrapper_ssh_port', 22),
  );

  $synchronizers = git_sync_synchronizers_load_all();
  foreach ($synchronizers as $info) {
    if (isset($info['default settings'])) {
      $routine->options += $info['default settings'];
    }
  }

  return $routine;
}

/**
 * Loads a Git sync routine configuration settings.
 *
 * @param string $name
 *   The machine name of the routine.
 *
 * @return array|FALSE
 *   The configuration options, FALSE if the routine doesn't exist.
 */
function git_sync_load($name) {
  $routines = &drupal_static(__FUNCTION__, array());
  if (!isset($routines[$name])) {
    ctools_include('export');
    $routines[$name] = ctools_export_crud_load('git_sync', $name);
  }
  return $routines[$name] ?: FALSE;
}

/**
 * Saves a Git sync routine's configuration settings.
 *
 * @param stdClass $routine
 *   The Git sync routine configuration being saved.
 *
 * @return boolean
 */
function git_sync_save($routine) {
  $sucess = ctools_export_crud_save('git_sync', $routine);
  $args = array('@name' => $routine->name);
  if ($sucess) {
    drupal_static_reset('git_sync_load_all');
    watchdog('git_sync', 'Git sync routine saved: @name', $args);
  }
  else {
    watchdog('git_sync', 'Error saving Git sync routine: @name', $args, WATCHDOG_ERROR);
  }
  return $sucess;
}

/**
 * Deletes a Git sync routine's configuration settings.
 *
 * @param stdClass $routine
 *   The Git sync routine configuration being deleted.
 *
 * @return boolean
 */
function git_sync_delete($routine) {
  // @todo CTools doesn't return the status. Figure out how to get it.
  ctools_export_crud_delete('git_sync', $routine);
  drupal_static_reset('git_sync_load_all');
  $args = array('@name' => $routine->name);
  watchdog('git_sync', 'Git sync routine deleted: @name', $args);
  return TRUE;
}
