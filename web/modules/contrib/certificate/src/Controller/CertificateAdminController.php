<?php

namespace Drupal\certificate\Controller;

use Drupal\system\Controller\SystemController;

/**
 * Description of CertificateAdminController
 *
 * @author rcascella
 */
class CertificateAdminController extends SystemController {

  /**
   * {@inheritdoc}
   */
  public function overview($link_id = 'certificate.admin') {
    $build['blocks'] = parent::overview($link_id);
    return $build;
  }

}
