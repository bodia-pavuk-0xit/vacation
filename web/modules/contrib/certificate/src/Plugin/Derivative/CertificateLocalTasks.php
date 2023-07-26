<?php

namespace Drupal\certificate\Plugin\Derivative;

use Drupal;
use Drupal\Component\Plugin\Derivative\DeriverBase;
use function certificate_get_entity_types;

/**
 * Derive certificate tabs based on entity references to certificate mappings.
 */
class CertificateLocalTasks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    /* @var $fields Drupal\field\Entity\FieldConfig[] */
    $entity_types = certificate_get_entity_types();

    foreach ($entity_types ?? [] as $type) {
      $this->derivatives["certificate.$type.certificate_tab"]['title'] = 'Certificate';
      $this->derivatives["certificate.$type.certificate_tab"]['route_name'] = "certificate.$type";
      $this->derivatives["certificate.$type.certificate_tab"]['id'] = "entity.$type.certificate_tab";
      $this->derivatives["certificate.$type.certificate_tab"]['base_route'] = "entity.$type.canonical";
      $this->derivatives["certificate.$type.certificate_tab"]['weight'] = 50;
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
