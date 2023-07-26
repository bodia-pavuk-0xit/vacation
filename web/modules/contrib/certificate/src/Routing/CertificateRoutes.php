<?php

namespace Drupal\certificate\Routing;

use Symfony\Component\Routing\Route;
use function certificate_get_entity_types;

/**
 * Defines dynamic routes.
 */
class CertificateRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = [];
    foreach (certificate_get_entity_types() ?? [] as $entity_type) {
      $options = [
        'no_cache' => 'TRUE',
        'certificate_param' => $entity_type,
      ];

      // Entity tab
      // @todo it is not always entity_type/id/certificate, we need to pull from
      // the entity type's canonical URL.
      $tab_route = new Route("$entity_type/{{$entity_type}}/certificate");
      $tab_route->setDefault('_controller', '\Drupal\certificate\Controller\CertificateController::certificatePage');
      $tab_route->setDefault('_title', 'Certificate');
      $tab_route->setOptions($options);
      $tab_route->setRequirements(['_custom_access' => '\Drupal\certificate\Controller\CertificateController::accessTab']);
      $routes["certificate.$entity_type"] = $tab_route;

      $pdf_route = new Route("$entity_type/{{$entity_type}}/certificate/{user}/{certificate_template}/pdf");
      $pdf_route->setDefault('_controller', '\Drupal\certificate\Controller\CertificateController::certificateDownload');
      $pdf_route->setDefault('_title', 'Download');
      $pdf_route->setOptions($options);
      $pdf_route->setOption('params', [
        'user' => 'type: entity:user',
        'certificate_template' => 'type: entity:certificate_template',
      ]);
      $pdf_route->setRequirements(['_custom_access' => '\Drupal\certificate\Controller\CertificateController::accessPdf']);
      $routes["certificate.$entity_type.pdf"] = $pdf_route;
    }

    return $routes;
  }

}
