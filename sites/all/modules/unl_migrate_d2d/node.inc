<?php

/**
 * Common mappings for the node migrations.
 */
abstract class NodeMigration extends DrupalNode7Migration {
  public function __construct(array $arguments) {
    parent::__construct($arguments);

    // Set the Group.
    $this->addFieldMapping('og_group_ref')->defaultValue(1);
    // Set a log message.
    $this->addFieldMapping('log')->defaultValue('Created via migration.');
  }
}

/**
 * Basic page-specifc mappings and handling.
 */
class BasicPageMigration extends NodeMigration {
  public function __construct(array $arguments) {
    parent::__construct($arguments);
  }
}
