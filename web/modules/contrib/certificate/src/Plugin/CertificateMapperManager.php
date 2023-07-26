<?php

namespace Drupal\certificate\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Traversable;

/**
 * Provides the Certificate mapper plugin manager.
 */
class CertificateMapperManager extends DefaultPluginManager {

  /**
   * Constructs a new CertificateMapperManager object.
   *
   * @param Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/certificate/CertificateMapper', $namespaces, $module_handler, 'Drupal\certificate\Plugin\CertificateMapperBase', 'Drupal\certificate\Annotation\CertificateMapper');

    $this->alterInfo('certificate_certificate_mapper_info');
    $this->setCacheBackend($cache_backend, 'certificate_certificate_mapper_plugins');
  }

}
