<?php

/**
 * @file
 * Drush hook implementations for the Composer Manager module.
 */

/**
 * Implements hook_drush_command().
 */
function git_sync_drush_command() {
  $items = array();

  $items['git-sync'] = array(
    'description' => 'Synchronizes a source repository to a destination repository.',
    'arguments' => array(
      'name' => dt('The machine name of the sync routine.'),
    ),
  );

  return $items;
}

/**
 * Runs a sync routine.
 *
 * @param string $name
 *   The machine name of the sync routine.
 */
function drush_git_sync($name) {
  try {
    git_sync($name);
  }
  catch (Exception $e) {
    drush_set_error('git_sync', $e->getMessage());
  }
}
