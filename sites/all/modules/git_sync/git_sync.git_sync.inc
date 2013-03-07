<?php

/**
 * @file
 * Git Sync hook implementations and callbacks.
 */

use GitSync\GitFork;
use GitSync\GitMirror;
use GitWrapper\GitWorkingCopy;

/**
 * Implements hook_git_synchronizers().
 */
function git_sync_git_synchronizers() {
  return array(
    'mirror' => array(
      'label' => t('Mirror the source repository'),
      'description' => t('Mirror the source repository to the destination repository.'),
      'sync callback' => 'git_sync_mirror',
    ),
    'fork' => array(
      'label' => t('Synchronize the forked (destination) repository with the source'),
      'description' => t('Pull changes from the source repository into the destination repository.'),
      'sync callback' => 'git_sync_fork',
      'settings callback' => 'git_sync_form_settings',
      'default settings' => array(
        'skip_master' => 0,
      ),
    ),
  );
}

/**
 * Sync callback; Mirrors a repository.
 */
function git_sync_mirror(GitWorkingCopy $git, $routine, array $synchronizer) {
  $mirror = new GitMirror($git, $routine->options['source_repo']);
  $mirror->sync($routine->options['dest_repo']);
}

/**
 * Sync callback; Pulls changes into a forked repository.
 */
function git_sync_fork(GitWorkingCopy $git, $routine, array $synchronizer) {
  $fork = new GitFork($git, $routine->options['dest_repo']);
  $fork->skipMaster($routine->options['skip_master']);
  $fork->sync($routine->options['source_repo']);
}

/**
 * Settings callback:
 */
function git_sync_form_settings(&$form, &$form_state, $routine) {
  $form['skip_master'] = array(
    '#type' => 'checkbox',
    '#title' => t('Skip synchronizing the master branch'),
    '#default_value' => $routine->options['skip_master'],
    '#description' => t('the master branch cannot be rebased in cases where the destination repository is not bare or cannot be accessed to run <code>git config receive.denyCurrentBranch ignore</code>. Skipping the master branch will allow all other branches to be synced without throwing fatal errros.'),
    '#states' => array(
      'visible' => array(
        ':input[name="type"]' => array('value' => 'fork'),
      )
    ),
  );
}
