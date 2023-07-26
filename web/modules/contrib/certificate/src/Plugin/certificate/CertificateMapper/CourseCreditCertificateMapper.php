<?php

namespace Drupal\certificate\Plugin\certificate\CertificateMapper;

use Drupal;
use Drupal\certificate\Plugin\CertificateMapperBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\course_credit\Entity\CourseCreditType;

/**
 * @CertificateMapper(
 *  id = "course_credit_awarded",
 *  label = @Translation("Awarded course credit"),
 *  description = @Translation("Using this mapping will award a certificate based on the credit type the user claimed after completing a course. You may set up eligibility for credit types on the credit types page."),
 *  required = {"course_credit"}
 * )
 */
class CourseCreditCertificateMapper extends CertificateMapperBase {

  /**
   * {@inheritdoc}
   */
  public function getMapKeys() {
    $options = [];
    $credits = CourseCreditType::loadMultiple();
    $credit_types = Drupal::entityTypeManager()->getStorage('course_credit_type')->loadByProperties(['status' => 1]);

    foreach ($credit_types as $key => $type) {
      $options[$key] = $type->label();
    }
    return $options;
  }

  /**
   * Check if the learner is eligible based on awarded credit in the course
   *
   * {@inheritdoc}
   */
  public function processMapping(ContentEntityInterface $entity, AccountInterface $account) {
    if (Drupal::moduleHandler()->moduleExists('course_credit')) {
      $options = [];
      $enrollment = $entity->getEnrollment($account);
      $awarded = $enrollment->get('course_credit_awarded')->referencedEntities();

      foreach ($awarded as $credit) {
        $credit_type = $credit->get('type')->referencedEntities()[0];
        $options[] = $credit_type->id();
      }

      return $options;
    }
  }

}
