<?php

namespace Drupal\certificate\Plugin\certificate\CertificateMapper;

use Drupal\certificate\Plugin\CertificateMapperBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * @CertificateMapper(
 *  id = "manual",
 *  label = @Translation("Manual"),
 *  description = @Translation("Select a single certificate to award to the user"),
 * )
 */
class ManualCertificateMapper extends CertificateMapperBase {

  public function getMapKeys() {
    return ['manual' => 'Manual'];
  }

  public function processMapping(ContentEntityInterface $entity, AccountInterface $account) {
    return ['manual'];
  }

}
