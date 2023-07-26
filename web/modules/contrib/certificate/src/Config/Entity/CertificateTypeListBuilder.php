<?php

namespace Drupal\certificate\Config\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

class CertificateTypeListBuilder extends ConfigEntityListBuilder {

  public function buildHeader() {
    $header['title'] = $this->t('Label');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity) {
    $row['title'] = $entity->toLink(NULL, 'edit-form');
    $row['description'] = $entity->get('description');
    return $row + parent::buildRow($entity);
  }

}
