<?php

namespace Drupal\certificate\Controller;

use Drupal;
use Drupal\certificate\Entity\CertificateMapping;
use Drupal\certificate\Entity\CertificateSnapshot;
use Drupal\certificate\Entity\CertificateTemplate;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function count;
use function render;

class CertificateController extends ControllerBase {

  /**
   * Get the certificate enabled entity from the route.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   */
  public function getEntityFromRoute() {
    $param = current(\Drupal::routeMatch()->getParameters());
    if (current($param) instanceof EntityInterface) {
      $entity = current($param);
    }

    if (!isset($entity)) {
      $route_match = Drupal::routeMatch();
      $route = $route_match->getRouteObject();
      if ($certificate_param = $route->getOption('certificate_param')) {
        $params = $route_match->getParameters();
        $entity_id = $params->get($certificate_param);
        $entity = Drupal::entityTypeManager()->getStorage($certificate_param)->load($entity_id);
      }
    }

    return $entity ?? NULL;
  }

  /**
   * @param EntityInterface $entity
   *   The entity this belongs to
   * @param AccountInterface $account
   * The user account to check
   * @return \Drupal\Core\Access\AccessResultInterface
   *   An access result
   */
  public function accessTab(EntityInterface $entity = NULL, AccountInterface $user = NULL) {
    $access = AccessResult::forbidden('No certificate access by default');
    if (!$entity = $this->getEntityFromRoute()) {
      return AccessResult::neutral()->setCacheMaxAge(0);
    }

    $currentUser = \Drupal::currentUser();
    $requestedUser = $user ?? $currentUser;

    $admin = $currentUser->hasPermission('administer certificate');
    $view_all = $currentUser->hasPermission('view all user certificates');

    if (!$requestedUser->id()) {
      return AccessResult::forbidden('Not a valid user.')->setCacheMaxAge(0);
    }

    if ($currentUser->id() !== $requestedUser->id() && !($admin || $view_all)) {
      return AccessResult::forbidden('Not an admin user.')->setCacheMaxAge(0);
    }

    // Check that the user can access the certificate on this entity.
    return $entity->access('certificate', $requestedUser, TRUE)->setCacheMaxAge(0);
  }

  /**
   * Full tab
   * @param $entity
   * @param AccountInterface $account
   * @return type
   */
  function certificatePage(AccountInterface $account = NULL) {
    if (!$entity = $this->getEntityFromRoute()) {
      return AccessResult::neutral();
    }
    $account = \Drupal::currentUser();

    // Get all templates for this entity combo
    $render = [];
    $valid_certs = [];
    $global_certs = CertificateMapping::getGlobalCertificateMappings();
    $certificate_mappers = Drupal::service('plugin.manager.certificate_mapper');
    $map_defs = $certificate_mappers->getDefinitions();
    /* @todo add find field name function */
    foreach ($entity->getFields() as $field) {
      if ($field->getFieldDefinition()->getType() == 'entity_reference' && $field->getSetting('target_type') == 'certificate_mapping') {
        $certs = $field->referencedEntities();
      }
    }

    //Default to load a page
    $render['info']['#markup'] = '';
    foreach ($map_defs as $map_key => $maps) {
      $plugin = $certificate_mappers->createInstance($map_key, ['of' => 'configuration values']);
      $matches = $plugin->processMapping($entity, $account) ?? [];

      foreach ($matches as $match) {
        foreach ($certs as $local) {
          if ($local->isMatch($map_key, $match)) {
            $valid_certs["$map_key.$match"][] = $local->get('cid')->value;
          }
        }

        // If local is not set, check the global mappings
        if (!isset($valid_certs["$map_key.$match"])) {
          foreach ($global_certs as $global) {
            if ($global->isMatch($map_key, $match) && $global->get('cid')->value !== '-1') {
              $valid_certs["$map_key.$match"][] = $global->get('cid')->value;
            }
          }
        }
        // Remove when prevented
        elseif ($valid_certs["$map_key.$match"] == '-1') {
          unset($valid_certs["$map_key.$match"]);
        }
      }
    }

    if (empty($valid_certs)) {
      $render['info']['#markup'] = t('Sorry, there is no certificate available.');
      return $render;
    }

    // Return single certs right away
    if (count($valid_certs) === 1 && count(reset($valid_certs)) === 1) {
      $template = CertificateTemplate::load(current(reset($valid_certs)));
      return $this->certificateDownload($entity, $account, $template);
    }

    // Return markup if we need to present messages
    $render['info']['#markup'] = t('You are eligible for multiple certificates.');
    $render['table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Type'),
        $this->t('Download'),
      ]
    ];
    foreach ($valid_certs as $cert_name => $vals) {
      foreach ($vals as $val) {
        $params = [
          $entity->getEntityTypeId() => $entity->id(),
          'user' => $account->id(),
          'certificate_template' => $val];
        $render['table'][$val] = [
          'type' => ['#markup' => $cert_name],
          'download' => Link::createFromRoute(t('Download certificate'), "certificate.{$entity->getEntityTypeId()}.pdf", $params)->toRenderable(),
        ];
      }
    }

    return $render;
  }

  /**
   * Downloads
   *
   * @param $entity
   * @param AccountInterface $account
   * @param CertificateTemplate $template
   * @return type
   */
  function accessPdf(EntityInterface $entity = NULL, AccountInterface $user, CertificateTemplate $certificate_template) {
    if (!$entity = $this->getEntityFromRoute()) {
      return AccessResult::neutral();
    }
    return $this->accessTab($entity, $user);
  }

  /**
   * Stream a PDF to the browser.
   *
   * @param $entity
   * @param AccountInterface $user
   * @param CertificateTemplate $certificate_template
   * @param boolean $preview Send to browser instead
   *
   * @return binary
   */
  function certificateDownload(EntityInterface $entity = NULL, AccountInterface $user, CertificateTemplate $certificate_template, $preview = FALSE) {
    if (!$entity = $this->getEntityFromRoute()) {
      return AccessResult::neutral();
    }

    $snapshots_enabled = (boolean) Drupal::config('certificate.settings')->get('snapshots');
    $snapshot_params = ['entity_id' => $entity->id(), 'entity_type' => $entity->bundle(), 'uid' => $user->id()];
    if ($snapshots_enabled) {
      $snapshot_search = Drupal::entityTypeManager()->getStorage('certificate_snapshot')->loadByProperties($snapshot_params);
      $html = !empty($snapshot_search) ? current($snapshot_search)->get('snapshot')->value : NULL;
    }

    // If no snapshot HTML found, load the entity
    if (empty($html)) {
      $renderView = $certificate_template->renderView($user, $entity);
      $html = render($renderView);
    }

    // Add base HREF so images work.
    $base = \Drupal::request()->getSchemeAndHttpHost();
    $html = "<base href=\"$base\">" . $html;

    // Save a new snapshot if none exists
    if ($snapshots_enabled && empty($snapshot_search)) {
      $snapshot_params['snapshot'] = $html;
      CertificateSnapshot::create($snapshot_params)->save();
    }

    if (\Drupal::currentUser()->hasPermission('administer certificate') && isset($_GET['preview'])) {
      print $html;
      exit;
    }

    // Get the PDF engine.
    $pdf_gen = $certificate_template->loadPrintableEngine();

    // Check for a PDF engine
    if ($pdf_gen === FALSE || $pdf_gen->getStderr()) {
      $current_user = Drupal::currentUser();
      $msg = t('Current site configuration does not allow PDF file creation. Please contact an administrator.');

      if ($current_user->hasPermission('administer printable')) {
        $url = Url::fromRoute('printable.format_configure_pdf');
        $link = Link::createFromRoute('configure a PDF library', 'printable.format_configure_pdf');
        $msg = t('Please @link to print certificates. Error: @error', ['@link' => $link->toString(), '@error' => $pdf_gen ? $pdf_gen->getStderr() : '']);
      }

      return [
        '#markup' => $msg,
      ];
    }

    // Engine is configured, proceed!
    // Add the page
    $pdf_gen->addPage($html);


    // Create that pdf and send it!
    $tmp = Drupal::service('file_system')->getTempDirectory();
    // Unique-ish file name?
    $title = strtolower(str_replace(' ', '_', mb_strimwidth($entity->label(), 0, 128, '...')));
    $file = "$tmp/{$title}.pdf";
    $pdf_gen->save($file);
    // Always send a file to download, except when we say not to =-D
    $response = BinaryFileResponse::create($file);
    $response->setFile($file, $preview ? 'attachment' : 'inline');
    return $response;
  }

}
