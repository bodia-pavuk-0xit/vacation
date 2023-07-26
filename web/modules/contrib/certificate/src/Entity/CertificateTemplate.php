<?php

namespace Drupal\certificate\Entity;

use Drupal;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\pdf_api\Plugin\PdfGeneratorInterface;

/**
 * Defines the Certificate Template entity class.
 *
 * @ContentEntityType(
 *   id = "certificate_template",
 *   label = @Translation("Certificate template"),
 *   label_collection = @Translation("Certificate template"),
 *   label_singular = @Translation("certificate template"),
 *   label_plural = @Translation("certificate templates"),
 *   label_count = @PluralTranslation(
 *     singular = "@count certificate template",
 *     plural = "@count certificate templates",
 *   ),
 *   bundle_label = @Translation("Certificate type"),
 *   bundle_entity_type = "certificate_type",
 *   field_ui_base_route = "entity.certificate_type.edit_form",
 *   admin_permission = "administer certificate",
 *   permission_granularity = "bundle",
 *   base_table = "certificate",
 *   fieldable = TRUE,
 *   permission_provider = "Drupal\Core\Entity\EntityAccessControlHandler",
 *   entity_keys = {
 *     "id" = "cid",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "published" = "status",
 *     "revision" = "revision_id",
 *   },
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\certificate\Config\Entity\CertificateTemplateListBuilder",
 *    "form" = {
 *       "default" = "Drupal\certificate\Form\CertificateEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "views_data" = "Drupal\entity\EntityViewsData",
 *   },
 *   links = {
 *     "canonical" = "/admin/certificates/template/{certificate_template}",
 *     "add-page" = "/admin/certificates/template/add",
 *     "add-form" = "/admin/certificates/template/add/{certificate_type}",
 *     "delete-form" = "/certificate_template/{certificate_template}/delete",
 *     "edit-form" = "/admin/certificates/template/manage/{certificate_template}",
 *     "collection" = "/admin/certificates/templates",
 *   },
 *   show_revision_ui = TRUE,
 *   revision_table = "certificate_revision",
 *   revision_metadata_keys = {
 *     "revision_default" = "revision_default",
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   }
 * )
 */
class CertificateTemplate extends EditorialContentEntityBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['cid'] = BaseFieldDefinition::create('integer');
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel('Title')
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -2,
    ]);

    // Set max_length to avoid fatal
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel('Machine name')
      ->setSetting('max_length', 128)
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
    ]);


    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setSetting('target_type', 'certificate_type');

    $fields['orientation'] = BaseFieldDefinition::create('list_string')
      ->setLabel('Orientation')
      ->setSetting('allowed_values', [PdfGeneratorInterface::PORTRAIT => 'Portrait', PdfGeneratorInterface::LANDSCAPE => 'Landscape'])
      ->setDefaultValue(PdfGeneratorInterface::PORTRAIT)
      ->setDisplayOptions('form', [
      'label' => 'above',
      'type' => 'options_buttons',
      'weight' => -1,
      'required' => TRUE,
    ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setRevisionable(TRUE)
      ->setLabel('Created');

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setRevisionable(TRUE)
      ->setLabel('Changed');

    $fields += parent::baseFieldDefinitions($entity_type);

    return $fields;
  }

  public function loadPrintableEngine() {
    // PDF Plugin
    $print_config = Drupal::config('printable.settings');
    $pdf_tool = $print_config->get('pdf_tool');

    if (empty($pdf_tool)) {
      return FALSE;
    }

    $pdf_eng = Drupal::service('plugin.manager.pdf_generator')->createInstance($pdf_tool);
    // Set the PDF binary path if provided
    if (!empty($print_config->get('path_to_binary'))) {
      $binary = $print_config->get('path_to_binary');
      $pdf_eng->configBinary($binary);
    }
    $pdf_eng->setPageOrientation($this->get('orientation')->value);

    return $pdf_eng;
  }

  /**
   * Return a token replaced certificate HTML.
   *
   * @param AccountInterface $account
   *   The account to build a certificate for.
   *
   * @param ContentEntityBase $entity
   *   The certifiable entity.
   *
   * @return string
   */
  public function renderView(AccountInterface $account, $entity) {
    if (!$entity instanceof ContentEntityBase) {
      return FALSE;
    }

    $render = $this->entityTypeManager()->getViewBuilder('certificate_template')->view($this->load($this->id()));
    $opts = [
      'user' => $account,
      $entity->getEntityTypeId() => $entity,
    ];

    $renderer = \Drupal::service('renderer');
    $text = $renderer->render($render);

    $out = \Drupal::token()->replace($text, $opts, ['clear' => TRUE, 'sanitize' => FALSE]);

    return $out;
  }

}
