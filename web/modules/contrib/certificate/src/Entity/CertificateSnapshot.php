<?php

namespace Drupal\certificate\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Certificate Snapshot entity class.
 *
 * @ContentEntityType(
 *   id = "certificate_snapshot",
 *   label = @Translation("Certificate snapshot"),
 *   label_collection = @Translation("Certificate snapshot"),
 *   label_singular = @Translation("certificate snapshot"),
 *   label_plural = @Translation("certificate snapshots"),
 *   label_count = @PluralTranslation(
 *     singular = "@count certificate snapshot",
 *     plural = "@count certificate snapshots",
 *   ),
 *   admin_permission = "administer certificate",
 *   base_table = "certificate_snapshot",
 *   fieldable = TRUE,
 *   permission_provider = "Drupal\Core\Entity\EntityAccessControlHandler",
 *   show_revision_ui = FALSE,
 *   entity_keys = {
 *     "id" = "csid",
 *     "published" = "published",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\certificate\Config\Entity\CertificateSnapshotListBuilder",
 *    "form" = {
 *       "default" = "Drupal\certificate\Form\CertificateEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "views_data" = "Drupal\entity\EntityViewsData",
 *   },
 *   links = {
 *     "canonical" = "/admin/certificates/snapshot/{certificate_snapshot}",
 *     "add-page" = "/admin/certificates/snapshot/add",
 *     "add-form" = "/admin/certificates/snapshot/add/{certificate_type}",
 *     "delete-form" = "/certificate_snapshot/{certificate_snapshot}/delete",
 *     "edit-form" = "/admin/certificates/snapshot/manage/{certificate_snapshot}",
 *     "collection" = "/admin/certificates/snapshots",
 *   }
 * )
 */
class CertificateSnapshot extends EditorialContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'user');
    $fields['entity_id'] = BaseFieldDefinition::create('integer');
    $fields['entity_type'] = BaseFieldDefinition::create('string');
    $fields['cid'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'certificate_template');

    $fields['snapshot'] = BaseFieldDefinition::create('text_long')
      ->setRevisionable(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setRevisionable(TRUE)
      ->setLabel('Created');

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setRevisionable(TRUE)
      ->setLabel('Changed');

    $fields += parent::baseFieldDefinitions($entity_type);

    return $fields;
  }

}
