<?php

namespace Drupal\certificate\Plugin;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\course\Entity\Course;

/**
 * Base class for Certificate mapper plugins.
 */
abstract class CertificateMapperBase extends PluginBase {

  /**
   * Get the map keys that the user is eligible for.
   *
   * @return array
   */
  abstract function processMapping(ContentEntityInterface $entity, AccountInterface $account);

  /**
   * Get a list of map keys.
   *
   * @return array
   */
  abstract function getMapKeys();

  public function hasDependencies() {
    $enabled = TRUE;
    $definition = $this->getPluginDefinition();
    foreach ($definition['required'] ?? [] as $module) {
      if (!Drupal::moduleHandler()->moduleExists($module)) {
        $enabled = FALSE;
        break;
      }
    }
    return $enabled;
  }

}
