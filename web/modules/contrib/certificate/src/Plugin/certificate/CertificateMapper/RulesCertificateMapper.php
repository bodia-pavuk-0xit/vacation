<?php

namespace Drupal\certificate\Plugin\certificate\CertificateMapper;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal;
use Drupal\certificate\Plugin\CertificateMapperBase;
use Drupal\Core\Session\AccountInterface;

/**
 * @CertificateMapper(
 *  id = "rules",
 *  label = @Translation("Rules component"),
 *  description = @Translation("Uses rules components"),
 *  required = {"rules"}
 * )
 */
class RulesCertificateMapper extends CertificateMapperBase {

  public function getMapped(ContentEntityInterface $course, AccountInterface $account) {
    return [];
  }

  public function getMapKeys() {
    $options = [];
    $credit_types = Drupal::entityTypeManager()->getStorage('rules_reaction_rule')->loadMultiple();
    foreach ($credit_types as $key => $type) {
      $options[$key] = $type->label();
    }
    return $options;
  }

  public function processMapping(ContentEntityInterface $entity, AccountInterface $account) {

  }

}
