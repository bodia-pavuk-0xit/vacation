<?php

/**
 * @file
 * Administrative pages for Certificate module.
 */

/**
 * Theme the form as a table.
 */
function theme_certificate_history_form($form) {
  // TODO: Should this theme certificate_history_form be declared in hook_theme()?
  $rows = array();
  $header = array(t('User'), t('Course'), t('Operations'));
  $rows = array(
    // Simple row
    array('Fred Flintstone', 'Course 101', 'View | Edit | Email'),
    array('Fred Flintstone', 'Course 102', 'View | Edit | Email'),
    array('Joan Smith', 'Course 102', 'View | Edit | Email'),
  );

  $caption = t('');
  $output = theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => 'certificates-history'), 'caption' => $caption));
  $output .= \Drupal::service('renderer')->render($form);

  return $output;
}

/**
 * Form to clear certificate snapshots.
 */
function certificate_admin_clear_form($form, &$form_state) {
  $form = array();

  $header = array(
    array(),
    array(
      'data' => 'Title',
      'field' => 'n.title',
    ),
    array(
      'data' => 'Count',
      'field' => 'count',
    ),
  );

  $sql = "SELECT *, count(cs.uid) AS count FROM {certificate_snapshots} cs
  INNER JOIN {node} n ON (cs.nid = n.nid)
  GROUP BY cs.nid";

  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
  $result = \Drupal::database()->query($sql);
  $nids = array();
  while ($row = $result->fetch()) {
    $nids[$row->nid] = '';
    $form['cs']['title'][$row->nid]['#markup'] = $row->title;
    $form['cs']['count'][$row->nid]['#markup'] = $row->count;
  }

  $form['cs']['nids'] = array(
    '#type' => 'checkboxes',
    '#options' => $nids,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Clear',
  );

  return $form;
}

/**
 * Theme certificate_admin_clear_form.
 */
function theme_certificate_admin_clear_form($variables) {
  $form = $variables['form'];
  // TODO Please change this theme call to use an associative array for the $variables parameter.
  $header = array(
    theme('table_select_header_cell'),
    array(
      'data' => 'Title',
      'field' => 'n.title',
    ),
    array(
      'data' => 'Count',
      'field' => 'cs_count',
    ),
  );
  $rows = array();
  if (!empty($form['cs']['title'])) {
    foreach (element_children($form['cs']['title']) as $key) {
      $rows[] = array(
        \Drupal::service('renderer')->render($form['cs']['nids'][$key]),
        \Drupal::service('renderer')->render($form['cs']['title'][$key]),
        \Drupal::service('renderer')->render($form['cs']['count'][$key]),
      );
    }
  }

  return theme('table', array('header' => $header, 'rows' => $rows, 'empty' => t('No snapshots to clear.'))) . drupal_render_children($form);
}

/**
 * Delete selected certificate snapshots.
 */
function certificate_admin_clear_form_submit(&$form, &$form_state) {
  $nids = array();

  foreach ($form_state['values']['nids'] as $nid => $delete) {
    if ($delete) {
      $nids[] = $nid;
    }
  }

  if (count($nids)) {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
    \Drupal::database()->delete('certificate_snapshots')
      ->condition('nid', $nids, 'IN')
      ->execute();
    \Drupal::messenger()->addStatus(t('Cleared certificate snapshots.'));
  }
  else {
    \Drupal::messenger()->addError(t('No items selected.'));
  }
}
