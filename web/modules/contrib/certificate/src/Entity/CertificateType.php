<?php

namespace Drupal\certificate\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the certificate_type entity class.
 *
 * @ConfigEntityType(
 *   id = "certificate_type",
 *   label = @Translation("Certificate type"),
 *   label_collection = @Translation("Certificate types"),
 *   label_singular = @Translation("certificate type"),
 *   label_plural = @Translation("certificate types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count certificate type",
 *     plural = "@count certificate types",
 *   ),
 *   admin_permission = "administer certificate types",
 *   config_prefix = "type",
 *   bundle_of = "certificate_template",
 *   entity_keys = {
 *     "id" = "id",
 *     "name" = "type",
 *     "label" = "label"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "name" = "type",
 *     "label" = "label",
 *     "description" = "description"
 *   },
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\certificate\Config\Entity\CertificateTypeListBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\certificate\Form\CertificateTypeForm",
 *       "edit" = "Drupal\certificate\Form\CertificateTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   links = {
 *     "add-form" = "/admin/certificates/certificate-types/add",
 *     "edit-form" = "/admin/certificates/certificate-types/manage/{certificate_type}",
 *     "delete-form" = "/admin/certificates/certificate-types/manage/{certificate_type}/delete",
 *     "collection" = "/admin/certificates/certificate-types"
 *   }
 * )
 */
class CertificateType extends ConfigEntityBundleBase {

}
