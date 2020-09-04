<?php

namespace Drupal\salesforce_mapping_ui\Tests;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests for salesforce admin settings.
 *
 * @group salesforce_mapping
 */
class SalesforceMappingCrudFormTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Default theme required for D9.
   *
   * @var string
   */
  protected $defaultTheme  = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'salesforce',
    'salesforce_test_rest_client',
    'salesforce_mapping',
    'salesforce_mapping_ui',
    'salesforce_mapping_test',
    'user',
    'link',
    'dynamic_entity_reference',
    'taxonomy',
  ];

  /**
   * Admin user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminSalesforceUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Admin salesforce user.
    $this->adminSalesforceUser = $this->drupalCreateUser(['administer salesforce mapping']);
  }

  /**
   * Tests webform admin settings.
   */
  public function testMappingCrudForm() {
    global $base_path;
    $mappingStorage = \Drupal::entityTypeManager()->getStorage('salesforce_mapping');
    $this->drupalLogin($this->adminSalesforceUser);

    /* Salesforce Mapping Add Form */
    $mapping_name = 'mapping' . rand(100, 10000);
    $post = [
      'id' => $mapping_name,
      'label' => $mapping_name,
      'drupal_entity_type' => 'node',
      'drupal_bundle' => 'salesforce_mapping_test_content',
      'salesforce_object_type' => 'Contact',
    ];
    $this->drupalPostForm('admin/structure/salesforce/mappings/add', $post, $this->t('Save'));
    $newurl = parse_url($this->getUrl());

    // Make sure the redirect was correct (and therefore form was submitted
    // successfully).
    $this->assertEqual($newurl['path'], $base_path . 'admin/structure/salesforce/mappings/manage/' . $mapping_name . '/fields');
    $mapping = $mappingStorage->load($mapping_name);
    // Make sure mapping was saved correctly.
    $this->assertEqual($mapping->id(), $mapping_name);
    $this->assertEqual($mapping->label(), $mapping_name);

    /* Salesforce Mapping Edit Form */
    // Need to rebuild caches before proceeding to edit link.
    drupal_flush_all_caches();
    $post = [
      'label' => $this->randomMachineName(),
      'drupal_entity_type' => 'node',
      'drupal_bundle' => 'salesforce_mapping_test_content',
      'salesforce_object_type' => 'Contact',
    ];
    $this->drupalPostForm('admin/structure/salesforce/mappings/manage/' . $mapping_name, $post, $this->t('Save'));
    $this->assertFieldByName('label', $post['label']);

    // Test simply adding a field plugin of every possible type. This is not
    // great coverage, but will at least make sure our plugin definitions don't
    // cause fatal errors.
    $mappingFieldsPluginManager = \Drupal::service('plugin.manager.salesforce_mapping_field');
    $field_plugins = $mappingFieldsPluginManager->getDefinitions();

    $post = [];
    $i = 0;
    foreach ($field_plugins as $definition) {
      if (call_user_func([$definition['class'], 'isAllowed'], $mapping)) {
        // Add a new field:
        $post['field_type'] = $definition['id'];
        $this->drupalPostForm('admin/structure/salesforce/mappings/manage/' . $mapping_name . '/fields', $post, $this->t('Add a field mapping to get started'));
        // Confirm that the new field shows up:
        $this->assertText($definition['label']);

        // Add all components of this field plugin to our post array to build up the mapping.
        $this->assertField("field_mappings[$i][config][drupal_field_value]");
        $this->assertField("field_mappings[$i][config][salesforce_field]");
        $this->assertField("field_mappings[$i][config][description]");
        $this->assertSession()->hiddenFieldExists("field_mappings[$i][drupal_field_type]");
        $this->assertField("field_mappings[$i][config][direction]");
//        $post[$field] = '';
//        $i++;
      }
    }

  }

}
