<?php

namespace Drupal\Tests\certificate\Functional;

use Drupal\certificate\Entity\CertificateMapping;
use Drupal;
use Drupal\certificate_test\Entity\CertificateTestEntity;
use Drupal\Tests\BrowserTestBase;
use function entity_create;

/**
 * Tests for Certificate.
 *
 * @group Certificate
 */
class CertificateTest extends BrowserTestBase {

  protected $strictConfigSchema = FALSE;
  protected $defaultTheme = 'stark';
  protected $admin_user = NULL;
  protected $certified_user = NULL;
  protected $uncertified_user = NULL;
  protected $portrait_certificate = NULL;
  protected $landscape_certificate = NULL;
  protected $content_type = 'certificate_test_entity';
  // @todo remove node as dep, something in PDF requiring it
  public static $modules = ['certificate', 'certificate_test', 'node'];

  public function setUp() {
    parent::setUp();
    $this->admin_user = $this->createUser(array('administer certificate'));

    // Create a complete and incomplete for testing. The email used triggers
    // "completion" in certificate_test.
    $this->certified_user = $this->createUser(['view certificate_test_entity'], 'certified', FALSE, ['mail' => 'certified@example.com']);
    $this->uncertified_user = $this->createUser(['view certificate_test_entity'], 'uncertified');

    // Create two certificates.
    $this->portrait_certificate = Drupal::entityTypeManager()->getStorage('certificate_template')->create([
      'title' => 'Portrait certificate title',
      'type' => 'certificate',
      'orientation' => 'portrait',
      'certificate_body' => 'Portrait certificate body',
    ]);
    $this->portrait_certificate->save();

    $this->landscape_certificate = Drupal::entityTypeManager()->getStorage('certificate_template')->create([
      'title' => 'Landscape certificate title',
      'type' => 'certificate',
      'orientation' => 'landscape',
      'certificate_body' => 'Landscape certificate body',
    ]);
    $this->landscape_certificate->save();
  }

  /**
   * Test the certificate access check.
   *
   * A user that is allowed to receive a certificate should be able to see the
   * certificate tab.
   */
  public function testCertificateAccess() {
    // Create an activity.
    /* @var  $activity CertificateTestEntity */
    $activity = Drupal::entityTypeManager()->getStorage($this->content_type)->create();
    $activity->save();

    // Set certificates to appear.
    $access_test_no_cert = $activity->access('certificate', $this->uncertified_user);
    $this->assertFalse($access_test_no_cert, 'Unqualified user cannot access certificate.');

    // Set certificates to appear.
    $access_test_cert = $activity->access('certificate', $this->certified_user);
    $this->assertTrue($access_test_cert, 'Qualified user can access certificate.');
  }

  /**
   * Test that the user receives the correct certificate.
   */
  public function testCertificateMapping() {
    $activity = Drupal::entityTypeManager()->getStorage($this->content_type)->create();
    $activity->save();

    // We give them the permission because we have to preview it here.
    $u1 = $this->createUser(['administer certificate', 'view certificate_test_entity'], 'certified2', FALSE, ['mail' => 'certified@example.com']);
    $firstletter = $u1->getAccountName()[0];

    $this->drupalLogin($u1);

    $this->drupalGet("certificate_test_entity/{$activity->id()}/certificate", array('query' => array('certificate_ok' => 1, 'preview' => TRUE)));
    $this->assertSession()->statusCodeNotEquals(403, 'Did not get access denied.');
    $this->assertNoText('Custom access denied message.', 'Did not find module provided access denied message on certificate page.');
    $this->assertText('Sorry, there is no certificate available.', 'Found no certificate available text.');

    // Map the first letter of the user's name to the certificate.
    $mapping = CertificateMapping::create([
        'map_key' => 'firstletter',
        'map_value' => $firstletter,
        'cid' => $this->landscape_certificate->id(),
    ]);
    $mapping->save();
    $activity->get('certificate_mapping')->appendItem($mapping->id());
    $activity->save();

    $this->drupalGet("certificate_test_entity/{$activity->id()}/certificate", array('query' => array('certificate_ok' => 1, 'preview' => TRUE)));
    $this->assertSession()->statusCodeNotEquals(403, 'Did not get access denied.');
    $this->assertNoText('Custom access denied message.', 'Did not find module provided access denied message on certificate page.');
    $this->assertNoText('Sorry, there is no certificate available.', 'User received certificate.');
    $this->assertText("Landscape certificate body", "Saw certificate body.");
  }

  /**
   * Test that global mappings correctly populate courses and local overrides
   * are retained.
   */
  public function testCertificateGlobalMapping() {
    $activity = Drupal::entityTypeManager()->getStorage($this->content_type)->create();
    $activity->save();

    // We give them the permission because we have to preview it here.
    $u1 = $this->createUser(['administer certificate', 'view certificate_test_entity'], 'certified2', FALSE, ['mail' => 'certified@example.com']);
    $firstletter = $u1->getAccountName()[0];
    $this->drupalLogin($u1);

    // Set globals
    $config = \Drupal::configFactory()->getEditable('certificate.settings');
    $config->set('maps', [
      'firstletter' => [
        $firstletter => $this->landscape_certificate->id(),
      ],
    ]);
    // Turn off snapshots because we have to award a different certificate.
    $config->set('snapshots', 0);
    $config->save();

    $this->drupalGet("certificate_test_entity/{$activity->id()}/certificate", array('query' => array('certificate_ok' => 1, 'preview' => TRUE)));
    $this->assertText("Landscape certificate body", "Saw certificate body.");

    // Map locally to a different certificate.
    // Map the first letter of the user's name to the certificate.
    $mapping = CertificateMapping::create([
        'map_key' => 'firstletter',
        'map_value' => $firstletter,
        'cid' => $this->portrait_certificate->id(),
    ]);
    $mapping->save();
    $activity->get('certificate_mapping')->appendItem($mapping->id());
    $activity->save();

    $this->drupalGet("certificate_test_entity/{$activity->id()}/certificate", array('query' => array('certificate_ok' => 1, 'preview' => TRUE)));
    $this->assertText('Portrait certificate body', "Saw certificate body.");
  }

  /**
   * Test the token replacement inside of certificates.
   */
  function testCertificateTemplates() {
    $activity = Drupal::entityTypeManager()->getStorage($this->content_type)->create();
    $activity->set('name', 'My test certifiable type');
    $activity->save();

    // We give them the permission because we have to preview it here.
    $account = $this->createUser(['administer certificate', 'view certificate_test_entity'], 'certified2', FALSE, ['mail' => 'certified@example.com']);
    $firstletter = $account->getAccountName()[0];
    $this->drupalLogin($account);

    $this->landscape_certificate->set('certificate_body', 'activity title: [certificate_test_entity:name] user name: [user:name]')->save();

    // Set globals
    $config = \Drupal::configFactory()->getEditable('certificate.settings');
    $config->set('maps', [
      'firstletter' => [
        $firstletter => $this->landscape_certificate->id(),
      ],
    ]);
    // Turn off snapshots because we have to award a different certificate.
    $config->set('snapshots', 0);
    $config->save();

    $this->drupalGet("certificate_test_entity/{$activity->id()}/certificate", array('query' => array('certificate_ok' => 1, 'preview' => TRUE)));
    $this->assertText("activity title: {$activity->label()} user name: {$account->getAccountName()}", "Saw certificate body.");
  }

}
