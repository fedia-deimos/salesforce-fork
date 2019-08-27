<?php

namespace Drupal\Tests\salesforce_jwt\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\simpletest\WebTestBase;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\key\Functional\KeyTestTrait;

/**
 * Test JWT Auth.
 *
 * @group salesforce_jwt
 */
class SalesforceJwtTest extends WebDriverTestBase {

  use KeyTestTrait;

  public static $modules = ['key', 'typed_data', 'dynamic_entity_reference', 'salesforce', 'salesforce_test_rest_client', 'salesforce_jwt'];

  /**
   * Admin user to test form.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * Id of shared cert key.
   */
  const KEY_ID = 'salesforce_jwt_test_key';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser(['authorize salesforce']);
    $this->drupalLogin($this->adminUser);
    $this->createTestKey(self::KEY_ID);
  }

  /**
   * Test that saving mapped nodes enqueues them for push to Salesforce.
   */
  public function testJwtAuth() {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('admin/config/salesforce/authorize/add');
    $page->fillField('provider', 'jwt');
    $assert_session->assertWaitOnAjaxRequest();
    $page->fillField('label', $this->randomString());
    $page->fillField('label', $this->randomString());
    $edit = [
      'provider_settings[consumer_key]' => 'foo',
      'provider_settings[login_user]' => 'bar',
      'provider_settings[login_url]' => 'zee',
      'provider_settings[encrypt_key]' => self::KEY_ID,
    ];
    foreach ($edit as $key => $value) {
      $page->fillField($key, $value);
    }
    $page->pressButton('Save');
//    $this->assertUrl('admin/config/salesforce/authorize/add');
//    $this->assertTrue(FALSE);
    $assert_session->addressEquals('foo');
    $assert_session->addressEquals('bar');

    //    $this->assertText('Salesforce consumer key field is required.');
//    $this->assertText('Salesforce login user field is required.');
//    $this->assertText('Private Key field is required.');
    //    $edit = [
    //      'label' => $this->randomString(),
    //      'id' => strtolower($this->randomMachineName()),
    //      'provider' => 'jwt',
    //      'save_default' => 1,
    //    ];
//    $edit['provider_settings'] += [
//      'consumer_key' => 'foo',
//      'login_user' => 'bar',
//      'login_url' => 'zee',
//      'encrypt_key' => self::KEY_ID,
//    ];
//    $this->drupalPostForm('admin/config/salesforce/authorize/add', $edit, 'submit');
//    $this->assertUrl('admin/config/salesforce/authorize/add');
////    $this->assertTrue(FALSE);
//    $this->assertUrl('admin/config/salesforce/authorize/list');
//    $this->drupalGet('admin/config/salesforce/authorize/list');

//    /** @var \Drupal\salesforce_push\PushQueue $queue */
//    $queue = \Drupal::service('queue.salesforce_push');
//    $this->assertEquals(0, $queue->numberOfItems());
//
//    Node::create([
//        'type' => 'salesforce_mapping_test_content',
//        'title' => 'Test Example',
//      ]
//    )->save();
//    $this->assertEquals(1, $queue->numberOfItems());
//
//    Node::create([
//        'type' => 'salesforce_mapping_test_content',
//        'title' => 'Test Example 2',
//      ]
//    )->save();
//    $ this->assertEquals(2, $queue->numberOfItems());

  }

}