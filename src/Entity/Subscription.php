<?php
/**
 * @file
 * Contains \Drupal\subscription\Entity\Subscription.
 */

namespace Drupal\subscription\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;

/**
 * Defines the Subscription entity.
 *
 * @ingroup subscription
 *
 *
 * @ContentEntityType(
 *   id = "subscription",
 *   label = @Translation("Subscription entity"),
 *   handlers = {
 *     "list_builder" = "Drupal\subscription\Entity\Controller\SubscriptionListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\subscription\Form\SubscriptionForm",
 *       "edit" = "Drupal\subscription\Form\SubscriptionForm",
 *       "delete" = "Drupal\subscription\Form\SubscriptionDeleteForm",
 *       "approve" = "Drupal\subscription\Form\SubscriptionAcceptedForm",
 *       "deny" = "Drupal\subscription\Form\SubscriptionDeniedForm",
 *     },
 *     "access" = "Drupal\subscription\SubscriptionAccessHandler",
 *   },
 *   list_cache_contexts = { "user" },
 *   base_table = "subscriptions",
 *   admin_permission = "administer subscription entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "created" = "created",
 *     "changed" = "changed",
 *     "name" = "name",
 *     "surname" = "surname",
 *     "dni" = "dni",
 *     "mail" = "mail",
 *     "birthday" = "birthday",
 *     "status" = "status"
 *   },
 *   links = {
 *     "edit-form" = "/subscription/{subscription}/edit",
 *     "delete-form" = "/subscription/{subscription}/delete",
 *     "approve-form" = "/subscription/{subscription}/approve",
 *     "deny-form" = "/subscription/{subscription}/deny",
 *     "collection" = "/subscriptions/list"
 *   }
 * )
 */
class Subscription extends ContentEntityBase {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * Define fields.
   *
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // ID of subscription.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Subscription entity.'))
      ->setReadOnly(TRUE);

    // UUID of subscription
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Subscription entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('Name.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -5,
    ]);

    $fields['surname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Surname'))
      ->setDescription(t('Surname.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4,
    ]);

    $fields['dni'] = BaseFieldDefinition::create('string')
      ->setLabel(t('DNI'))
      ->setDescription(t('DNI with format (11111111J.'))
      ->addConstraint('UniqueField')
      ->addConstraint('ValidDNI')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -3,
    ]);

    $fields['mail'] = BaseFieldDefinition::create('email')
      ->setLabel(t('E-mail'))
      ->setDescription(t('E-mail.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
      'type' => 'email_default',
      'weight' => -2,
    ]);;

    $fields['birthday'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Birthday'))
      ->setDescription(t('Birthday.'))
      ->setRequired(TRUE)
      ->setSettings([
        'datetime_type' => 'date'
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -1,
      ]);

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('status'))
      ->setDescription(t('Status.'))
      ->setRequired(TRUE)
      ->setDefaultValue('pending')
      ->setSettings([
        'allowed_values' => [
          'pending' => t('Pending'),
          'accepted' => t('Accepted'),
          'denied' => t('Denied')
        ],
        'allowed_values_function' => ''
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setRequired(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'))
      ->setRequired(TRUE);

    return $fields;
  }

}
