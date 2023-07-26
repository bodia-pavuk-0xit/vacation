<?php

namespace Drupal\certificate\Entity;

use Drupal;
use Drupal\certificate\Entity\CertificateTemplate;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\course\Entity\Course;

/**
 * @migration
 * Update certificate_node table:
 * Update cnid to cmid
 * Drop entity_id
 * Drop entity_type
 * Update mapper to map_key
 * Update type to map_value
 * Update template to cid
 * Rename table certificate_node to certificate_mapping
 */

/**
 * Defines the credit mapping type entity class.
 *
 * @ContentEntityType(
 *   id = "certificate_mapping",
 *   label = @Translation("Certificate mapping"),
 *   label_collection = @Translation("Certificate mappings"),
 *   label_singular = @Translation("certificate mapping"),
 *   label_plural = @Translation("certificate mappings"),
 *   label_count = @PluralTranslation(
 *     singular = "@count certificate mapping",
 *     plural = "@count certificate mapping",
 *   ),
 *   admin_permission = "administer certificate mapping",
 *   permission_granularity = "bundle",
 *   base_table = "certificate_mapping",
 *   entity_keys = {
 *     "id" = "cmid",
 *     "type" = "map_key",
 *     "map_value" = "map_value",
 *     "cid" = "cid",
 *     "revision" = "revision_id",
 *     "published" = "active",
 *   },
 *   handlers = {
 *     "access" = "Drupal\certificate\Access\CertificateMappingAccessControlHandler",
 *     "permission_provider" = "Drupal\entity\UncacheableEntityPermissionProvider",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "inline_form" = "Drupal\certificate\Form\CertificateMappingsInlineEntityForm",
 *     "views_data" = "Drupal\entity\EntityViewsData",
 *   },
 *   show_revision_ui = TRUE,
 *   revision_table = "certificate_mapping_revision",
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   }
 * )
 */
class CertificateMapping extends EditorialContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['map_key'] = BaseFieldDefinition::create('list_string')
      ->setLabel('Type')
      ->setSetting('allowed_values_function', 'certificate_get_certificate_mappers')
      ->setDisplayOptions('form', [
      'label' => 'above',
      'type' => 'options_select',
      'weight' => -2,
    ]);

    $fields['map_value'] = BaseFieldDefinition::create('list_string')
      ->setLabel('Selection')
      ->setSetting('allowed_values_function', 'certificate_get_certificate_mapper_values')
      ->setDisplayOptions('form', [
      'label' => 'above',
      'type' => 'options_select',
      'weight' => -1,
    ]);

    $fields['cid'] = BaseFieldDefinition::create('list_string')
      ->setRequired(TRUE)
      ->setLabel('Template')
      ->setSetting('allowed_values_function', 'certificate_get_certificate_options')
      ->setDisplayOptions('form', [
      'type' => 'options_select',
    ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setRevisionable(TRUE)
      ->setLabel('Created');

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setRevisionable(TRUE)
      ->setLabel('Changed');

    $fields += parent::baseFieldDefinitions($entity_type);

    return $fields;
  }

  public function label() {
    $cid = $this->get('cid')->value;
    $certificate_label = $cid !== '-1' ? CertificateTemplate::load($cid)->label() : '';
    $action = $cid !== '-1' ? t('awards certificate') : ($cid < 0 ? t('prevents awarding') : '');
    $opts = [
      '%value' => $this->get('map_value')->value,
      '@action' => $action,
      '%certificate' => $certificate_label,
    ];
    $label = t('%value @action %certificate', $opts);
    return $label;
  }

  /**
   * Check if this mapping is set to a specific key value pair
   * @param string $map_key
   * @param string $map_value
   */
  public function isMatch($map_key, $map_value) {
    $isKey = $this->get('map_key')->value == $map_key;
    $isValue = $this->get('map_value')->value == $map_value;
    return $isKey && $isValue;
  }

  /**
   * Return the global certificate mappings as object
   * @return array CertificateMapping objects
   */
  public static function getGlobalCertificateMappings() {
    $response = [];
    $global_maps = Drupal::config('certificate.settings')->get('maps');
    // filter empty global settings
    $filter = [];
    foreach ($global_maps as $key => $global) {
      $filter[$key] = array_filter($global);
    }
    $clean_array = array_filter($filter);

    // Send entities to match the course configs
    foreach ($clean_array as $map_key => $maps) {
      foreach ($maps as $map_value => $template) {
        $opts = [
          'map_key' => $map_key,
          'map_value' => $map_value,
          'cid' => $template,
        ];
        $cert_map = new CertificateMapping([], 'certificate_mapping');
        foreach ($opts as $key => $val) {
          $cert_map->set($key, $val);
        }
        $response["$map_key.$map_value"] = $cert_map;
      }
    }

    return $response;
  }

  function getPlugin() {
    $map_key = $this->get('map_key')->value;
    $plugin = \Drupal::service('plugin.manager.certificate_mapper')->createInstance($map_key, ['of' => 'configuration values']);
    return $plugin;
  }

}
