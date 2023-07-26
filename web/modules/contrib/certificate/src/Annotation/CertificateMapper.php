<?php

namespace Drupal\certificate\Annotation;

use Drupal\certificate\Plugin\CertificateMapperManager;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

/**
 * Defines a Certificate mapper item annotation object.
 *
 * @see CertificateMapperManager
 * @see plugin_api
 *
 * @Annotation
 */
class CertificateMapper extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The plugin description.
   *
   * @var string
   */
  public $description;

  /**
   * A list of required modules
   *
   * @var array
   */
  public $required;

}
