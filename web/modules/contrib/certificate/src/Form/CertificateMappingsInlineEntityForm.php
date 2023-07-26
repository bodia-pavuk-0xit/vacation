<?php

namespace Drupal\certificate\Form;

use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * Class CertificateConfigForm.
 */
class CertificateMappingsInlineEntityForm extends EntityInlineForm {

  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    $entity_form = parent::entityForm($entity_form, $form_state);
    $settings = Drupal::config('certificate.settings');
    /*
      $mappers = certificate_get_certificate_mappers();

      $credits = array_column($course_credit, $label, $type);
      $form_display = $this->getFormDisplay($entity_form['#entity'], $entity_form['#form_mode']);

      // Load parent and add dynamic options
      foreach ($mappers as $key => $map) {
      $entity_form[$key]['fieldset'] = [
      '#type' => 'details',
      '#title' => $map,
      ];
      $form_display->buildForm($entity_form['#entity'], $entity_form[$key]['fieldset'], $form_state);
      $entity_form[$key]['fieldset']['entity_id']['widget'][0]['target_id']['#default_value'] = $course;
      }
     */
    return $entity_form;
  }

  public function entityFormSubmit(array &$entity_form, FormStateInterface $form_state) {
    parent::entityFormSubmit($entity_form, $form_state);
  }

  public function entityFormValidate(array &$entity_form, FormStateInterface $form_state) {
    parent::entityFormValidate($entity_form, $form_state);
  }

  public function delete($ids, $context) {
    parent::delete($ids, $context);
  }

}
