<?php

/**
 * @file
 * Contains \Drupal\salesforce_mapping\SalesforceMappingListController.
 */

namespace Drupal\salesforce_mapping;

use Drupal\Component\Utility\String;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\Entity\DraggableListController;
use Drupal\Core\Entity\EntityControllerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the filter format list controller.
 */
class SalesforceMappingListController extends DraggableListController implements EntityControllerInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'salesforce_mapping_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = array();
    $header['label'] = t('Label');
    $header['drupal_entity_type'] = t('Drupal Entity');
    $header['drupal_bundle'] = t('Drupal Bundle');
    $header['salesforce_object_type'] = t('Salesforce Object');
    $header['status'] = t('Salesforce Object');
    return $header + parent::buildHeader();
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = array();
    $row['label'] = $entity->label();
    return $row + parent::buildRow($entity);
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit']['#value'] = t('Save changes');
    return $form;
  }

}
