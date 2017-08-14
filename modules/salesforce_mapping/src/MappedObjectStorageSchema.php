<?php

namespace Drupal\salesforce_mapping;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the mapped object schema handler in order to add some unique keys.
 */
class MappedObjectStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {
    $schema = parent::getEntitySchema($entity_type, $reset);
    // backwards compatibility for salesforce_mapping_update_8001
    // key is too long if length is 255, so we have to wait until the db update
    // fires to avoid WSOD
    if (\Drupal::database()->schema()->fieldExists('salesforce_mapped_object', 'drupal_entity__target_id')) {
      $schema['salesforce_mapped_object']['unique keys'] += [
        'entity__mapping' => [
          'drupal_entity__target_type',
          'salesforce_mapping',
          'drupal_entity__target_id',
          'drupal_entity__target_id_int'
        ]
      ];
    }
    $schema['salesforce_mapped_object']['fields']['entity_type_id']['length'] =
    $schema['salesforce_mapped_object']['fields']['salesforce_mapping']['length'] =
    $schema['salesforce_mapped_object_revision']['fields']['entity_type_id']['length'] =
    $schema['salesforce_mapped_object_revision']['fields']['salesforce_mapping']['length'] =
      EntityTypeInterface::ID_MAX_LENGTH;

    return $schema;
  }
}
