<?php

namespace Drupal\certificate\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use function certificate_get_entity_types;

/**
 * Form for certificate template.
 */
class CertificateEntityForm extends ContentEntityForm {

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['name']['widget'][0]['value']['#type'] = 'machine_name';

    $form['name']['widget'][0]['value']['#machine_name'] = [
      'source' => ['title', 'widget', '0', 'value'],
      'exists' => [$this, 'exists'],
    ];

    // Add the token tree UI.
    $form['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => array_merge(['user'], certificate_get_entity_types()),
      '#global_types' => FALSE,
    ];

    return $form;
  }

  function exists($name) {
    $entities = \Drupal::entityTypeManager()->getStorage('certificate_template')->loadByProperties([
      'name' => $name,
    ]);
    return (bool) $entities;
  }

  public function save(array $form, FormStateInterface $form_state) {
    $certificate = $this->entity;
    $insert = $certificate->isNew();

    parent::save($form, $form_state);

    $t_args = [':title' => $certificate->label()];

    if ($insert) {
      $this->messenger()->addStatus($this->t(':title has been created.', $t_args));
      //$form_state->setRedirect('course.outline', ['course' => $this->entity->id()]);
    }
    else {
      $this->messenger()->addStatus($this->t(':title has been updated.', $t_args));
    }
  }

}
