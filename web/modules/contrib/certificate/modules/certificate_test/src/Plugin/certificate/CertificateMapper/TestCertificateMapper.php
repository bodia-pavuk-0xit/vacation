<?php

namespace Drupal\certificate_test\Plugin\certificate\CertificateMapper;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\certificate\Plugin\CertificateMapperBase;

/**
 * @CertificateMapper(
 *  id = "firstletter",
 *  label = @Translation("Test mapper"),
 *  description = @Translation("Map based on first letter of username"),
 * )
 */
class TestCertificateMapper extends CertificateMapperBase {

  public function getMapKeys() {
    return array_combine(range('a', 'z'), range('A', 'Z'));
  }

  public function processMapping(ContentEntityInterface $entity, AccountInterface $account) {
    return [$account->getAccountName()[0]];
  }

}
