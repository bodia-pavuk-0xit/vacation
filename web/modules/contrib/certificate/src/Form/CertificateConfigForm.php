<?php

namespace Drupal\certificate\Form;

use Drupal;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CertificateConfigForm.
 */
class CertificateConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['certificate.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'certificate_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->config('certificate.settings');
    $certificate_mappers = Drupal::service('plugin.manager.certificate_mapper');
    $mapper_definitions = $certificate_mappers->getDefinitions();

    // Certificate Snapshots
    $form['certificate']['snapshot_fieldset'] = array(
      '#title' => $this->t('Certificate Snapshots'),
      '#type' => 'fieldset',
    );
    $form['certificate']['snapshot_fieldset']['snapshots'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#description' => $this->t('Certificates will only be generated once per node/user'),
      '#default_value' => $settings->get("snapshots"),
    );

    // Global mappings
    $form['certificate']['maps'] = array(
      '#title' => $this->t('Global Certificate Mappings'),
      '#type' => 'fieldset',
      '#tree' => TRUE,
    );
    // Get certs and prep options
    foreach ($mapper_definitions as $map_type => $map) {
      $plugin = $certificate_mappers->createInstance($map_type, ['of' => 'configuration values']);
      $form['certificate']['maps'][$map_type] = array(
        '#title' => Html::escape($map['label']),
        '#type' => 'details',
        '#group' => TRUE,
        '#description' => Xss::filterAdmin($map['description']),
        '#open' => FALSE,
      );

      if ($plugin->hasDependencies()) {
        $keys = $plugin->getMapKeys();
        if (!empty($keys)) {
          foreach ($keys as $key => $title) {
            $form['certificate']['maps'][$map_type][$key] = array(
              '#type' => 'select',
              '#title' => Xss::filter($title),
              '#options' => $this->getCertificateTemplateOptions(),
              '#default_value' => $settings->get("maps.$map_type.$key") ?? '',
            );
          }
        }
        else {
          $form['certificate']['maps'][$map_type]['empty'] = array(
            '#markup' => '<p>' . $this->t('There are no mappings available for %title.', array('%title' => $map['label'])) . '</p>',
          );
        }
      }
      else {

        foreach ($map['required'] as $module) {
          $modules[$module] = \Drupal::moduleHandler()->getName($module);
        }

        $form['certificate']['maps'][$map_type]['#description'] = $this->t('The following modules allow use of %title mappings.', array('%title' => $map['label']));
        $form['certificate']['maps'][$map_type][] = array(
          '#theme' => 'item_list',
          '#list_type' => 'ul',
          '#items' => $modules,
        );
      }
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('certificate.settings');
    $form_state->cleanValues();
    $vals = $form_state->getValues();
    foreach ($vals as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Return a list of certificate templates suitable as options in a select list
   */
  public static function getCertificateTemplateOptions() {
    $options = ['' => t('- select -'), '-1' => t('- prevent certificate -'),];
    $certificates = Drupal::entityTypeManager()->getStorage('certificate_template')->loadMultiple();
    foreach ($certificates as $cid => $cert_ent) {
      $options[$cert_ent->get('cid')->value] = $cert_ent->label();
    }
    return $options;
  }

}
