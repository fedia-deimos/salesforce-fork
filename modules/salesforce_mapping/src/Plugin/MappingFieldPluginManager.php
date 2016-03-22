<?php

/**
 * @file
 * Contains \Drupal\salesforce_mapping\Plugin\MappingFieldPluginManager.
 */

namespace Drupal\salesforce_mapping\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;
// use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Plugin type manager for all views plugins.
 */
class MappingFieldPluginManager extends DefaultPluginManager {

  /**
   * Constructs a MappingFieldPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations,
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, LanguageManager $language_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/salesforce/MappingField', $namespaces, $module_handler, 'Drupal\Component\Annotation\Plugin');

    $this->alterInfo($module_handler, 'salesforce_mapping_fields_info');
    $this->setCacheBackend($cache_backend, 'salesforce_mapping_field', array('salesforce_mapping_plugins'));
  }

}
