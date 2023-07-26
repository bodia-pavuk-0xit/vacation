<?php

namespace Drupal\certificate\Access;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\entity\UncacheableEntityAccessControlHandler;

class CertificateMappingAccessControlHandler extends UncacheableEntityAccessControlHandler {

  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    if ($account->hasPermission('assign certificates')) {
      return \Drupal\Core\Access\AccessResultAllowed::allowed();
    }

    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

  function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($account->hasPermission('assign certificates')) {
      return \Drupal\Core\Access\AccessResultAllowed::allowed();
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
