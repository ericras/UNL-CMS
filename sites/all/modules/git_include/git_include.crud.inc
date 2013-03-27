<?php

/**
 * @file
 * CRUD functions for Git include configurations.
 */

/**
 * Gets all search engine configurations.
 *
 * @return array
 *   An associative array keyed by the machine name of the routine to the
 *   configuration object.
 */
function git_include_routine_load_all() {
  $routines = &drupal_static(__FUNCTION__);
  if (NULL === $routines) {
    ctools_include('export');
    $routines = ctools_export_crud_load_all('git_include');
  }
  return $routines;
}

/**
 * Returns an empty Git include routine configuration object with defaults.
 *
 * @return stdClass
 */
function git_include_new() {
  ctools_include('export');
  $routine = ctools_export_crud_new('git_include');
  $routine->options = array(
    'label' => '',
    'source_repo' => '',
    'dest_repo' => '',
    'type' => 'mirror',
    'ssh' => variable_get('git_wrapper_connect_via_ssh', 0),
    'private_key' => variable_get('git_wrapper_private_key', ''),
    'port' => variable_get('git_wrapper_ssh_port', 22),
  );


  return $routine;
}

/**
 * Loads a Git include routine configuration settings.
 *
 * @param string $name
 *   The machine name of the routine.
 *
 * @return array|FALSE
 *   The configuration options, FALSE if the routine doesn't exist.
 */
function git_include_load($name) {
  $routines = &drupal_static(__FUNCTION__, array());
  if (!isset($routines[$name])) {
    ctools_include('export');
    $routines[$name] = ctools_export_crud_load('git_include', $name);
  }
  return $routines[$name] ?: FALSE;
}

/**
 * Saves a Git include routine's configuration settings.
 *
 * @param stdClass $routine
 *   The Git include routine configuration being saved.
 *
 * @return boolean
 */
function git_include_save($routine) {
  $sucess = ctools_export_crud_save('git_include', $routine);
  $args = array('@name' => $routine->name);
  if ($sucess) {
    drupal_static_reset('git_include_load');
    watchdog('git_include', 'Git include routine saved: @name', $args);
  }
  else {
    watchdog('git_include', 'Error saving Git include routine: @name', $args, WATCHDOG_ERROR);
  }
  return $sucess;
}

/**
 * Deletes a Git include routine's configuration settings.
 *
 * @param stdClass $routine
 *   The Git include routine configuration being deleted.
 *
 * @return boolean
 */
function git_include_delete($routine) {
  // @todo CTools doesn't return the status. Figure out how to get it.
  ctools_export_crud_delete('git_include', $routine);
  drupal_static_reset('git_include_load');
  $args = array('@name' => $routine->name);
  watchdog('git_include', 'Git include routine deleted: @name', $args);
  return TRUE;
}
