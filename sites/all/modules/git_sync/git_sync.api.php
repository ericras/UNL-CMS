<?php

/**
 * @file
 * Hooks defined by the Git Sync module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Defines synchronizers, which defines a callback that performs the operation.
 */
function hook_git_synchronizers() {
  return array(
    'mirror' => array(
      'label' => t('Mirror the source repository'),
      'description' => t('Mirror the source repository to the destination repository.'),
      'sync callback' => 'git_sync_mirror',
    ),
    'fork' => array(
      'label' => t('Synchronize the forked (destination) repository'),
      'description' => t('Pull changes from the source repository into the destination repository.'),
      'sync callback' => 'git_sync_fork',
    ),
  );
}

/**
 * @} End of "addtogroup hooks".
 */
