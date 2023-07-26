<?php

use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityLastInstalledSchemaRepositoryInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Make certificate revisionable.
 */
function certificate_post_update_make_certificates_revisionable(&$sandbox) {

  $definition_update_manager = Drupal::entityDefinitionUpdateManager();
  /** @var EntityLastInstalledSchemaRepositoryInterface $last_installed_schema_repository */
  $last_installed_schema_repository = Drupal::service('entity.last_installed_schema.repository');

  $entity_type = $definition_update_manager->getEntityType('certificate_template');
  $field_storage_definitions = $last_installed_schema_repository->getLastInstalledFieldStorageDefinitions('certificate_template');

  // Remove these old revision fields that got accidentally added.
  if ($definition = $definition_update_manager->getFieldStorageDefinition('revision_created', 'certificate_template')) {
    $definition_update_manager->uninstallFieldStorageDefinition($definition);
  }
  if ($definition = $definition_update_manager->getFieldStorageDefinition('revision_user', 'certificate_template')) {
    $definition_update_manager->uninstallFieldStorageDefinition($definition);
  }
  if ($definition = $definition_update_manager->getFieldStorageDefinition('revision_log_message', 'certificate_template')) {
    $definition_update_manager->uninstallFieldStorageDefinition($definition);
  }

  // Drop old table that wasn't used, will be recreated.
  Database::getConnection()
    ->schema()
    ->dropTable('certificate_revision');

  // Update the entity type definition.
  $entity_keys = $entity_type->getKeys();
  $entity_keys['revision'] = 'revision_id';
  $entity_type->set('entity_keys', $entity_keys);
  $entity_type->set('revision_table', 'certificate_revision');
  $revision_metadata_keys = [
    'revision_default' => 'revision_default',
    'revision_user' => 'revision_user',
    'revision_created' => 'revision_created',
    'revision_log_message' => 'revision_log_message',
  ];
  $entity_type->set('revision_metadata_keys', $revision_metadata_keys);

  // Revision metadata
  $field_storage_definitions['revision_id'] = BaseFieldDefinition::create('integer')
    ->setName('revision_id')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision ID'))
    ->setReadOnly(TRUE)
    ->setSetting('unsigned', TRUE);

  $field_storage_definitions['revision_default'] = BaseFieldDefinition::create('boolean')
    ->setName('revision_default')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(t('Default revision'))
    ->setDescription(t('A flag indicating whether this was a default revision when it was saved.'))
    ->setRevisionable(TRUE)
    // We cannot tell whether existing revisions were default or not when
    // they were created, but since we did not support creating non-default
    // revisions in any core stable UI so far, we default to TRUE.
    ->setInitialValue(TRUE);

  $field_storage_definitions['revision_created'] = BaseFieldDefinition::create('created')
    ->setName('revision_created')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision create time'))
    ->setDescription(new TranslatableMarkup('The time that the current revision was created.'))
    ->setRevisionable(TRUE);

  $field_storage_definitions['revision_user'] = BaseFieldDefinition::create('entity_reference')
    ->setName('revision_user')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision user'))
    ->setDescription(new TranslatableMarkup('The user ID of the author of the current revision.'))
    ->setSetting('target_type', 'user')
    ->setRevisionable(TRUE);

  $field_storage_definitions['revision_log_message'] = BaseFieldDefinition::create('string_long')
    ->setName('revision_log_message')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision log message'))
    ->setDescription(new TranslatableMarkup('Briefly describe the changes you have made.'))
    ->setRevisionable(TRUE)
    ->setDefaultValue('');

  $definition_update_manager->updateFieldableEntityType($entity_type, $field_storage_definitions, $sandbox);
}

/**
 * Make certificate revisionable.
 */
function certificate_post_update_make_certificate_mappings_revisionable(&$sandbox) {
  // Workaround to remove a stray vid entity key so it can be reinstalled.
  /** @var Drupal\Core\Entity\ContentEntityType $kvitem */
  $kvitem = \Drupal::keyValue('entity.definitions.installed')->get('certificate_mapping.entity_type');
  $entity_keys = $kvitem->getKeys();
  $entity_keys['revision'] = NULL;
  $kvitem->set('entity_keys', $entity_keys);
  \Drupal::keyValue('entity.definitions.installed')->set('certificate_mapping.entity_type', $kvitem);

  $definition_update_manager = Drupal::entityDefinitionUpdateManager();
  /** @var EntityLastInstalledSchemaRepositoryInterface $last_installed_schema_repository */
  $last_installed_schema_repository = Drupal::service('entity.last_installed_schema.repository');

  $entity_type = $definition_update_manager->getEntityType('certificate_mapping');
  $field_storage_definitions = $last_installed_schema_repository->getLastInstalledFieldStorageDefinitions('certificate_mapping');

  // Update the entity type definition.
  $entity_keys = $entity_type->getKeys();
  $entity_keys['revision'] = 'revision_id';
  $entity_type->set('entity_keys', $entity_keys);
  $entity_type->set('revision_table', 'certificate_mapping_revision');
  $revision_metadata_keys = [
    'revision_default' => 'revision_default',
    'revision_user' => 'revision_user',
    'revision_created' => 'revision_created',
    'revision_log_message' => 'revision_log_message',
  ];
  $entity_type->set('revision_metadata_keys', $revision_metadata_keys);

  // Revision metadata
  $field_storage_definitions['revision_id'] = BaseFieldDefinition::create('integer')
    ->setName('revision_id')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision ID'))
    ->setReadOnly(TRUE)
    ->setSetting('unsigned', TRUE);

  $field_storage_definitions['revision_default'] = BaseFieldDefinition::create('boolean')
    ->setName('revision_default')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(t('Default revision'))
    ->setDescription(t('A flag indicating whether this was a default revision when it was saved.'))
    ->setRevisionable(TRUE)
    // We cannot tell whether existing revisions were default or not when
    // they were created, but since we did not support creating non-default
    // revisions in any core stable UI so far, we default to TRUE.
    ->setInitialValue(TRUE);

  $field_storage_definitions['revision_created'] = BaseFieldDefinition::create('created')
    ->setName('revision_created')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision create time'))
    ->setDescription(new TranslatableMarkup('The time that the current revision was created.'))
    ->setRevisionable(TRUE);

  $field_storage_definitions['revision_user'] = BaseFieldDefinition::create('entity_reference')
    ->setName('revision_user')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision user'))
    ->setDescription(new TranslatableMarkup('The user ID of the author of the current revision.'))
    ->setSetting('target_type', 'user')
    ->setRevisionable(TRUE);

  $field_storage_definitions['revision_log_message'] = BaseFieldDefinition::create('string_long')
    ->setName('revision_log_message')
    ->setTargetEntityTypeId($entity_type->id())
    ->setTargetBundle(NULL)
    ->setLabel(new TranslatableMarkup('Revision log message'))
    ->setDescription(new TranslatableMarkup('Briefly describe the changes you have made.'))
    ->setRevisionable(TRUE)
    ->setDefaultValue('');

  $definition_update_manager->updateFieldableEntityType($entity_type, $field_storage_definitions, $sandbox);
}
