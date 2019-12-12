<?php

namespace Drupal\subscription\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Subscription Create Block' block.
 *
 * @Block(
 *   id = "subscription_create_block",
 *   admin_label = @Translation("Subscription Create Block"),
 * )
 */

class SubscriptionCreateBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = \Drupal\subscription\Entity\Subscription::create();
    $form = \Drupal::service('entity.form_builder')->getForm($entity, 'add');

    return $form;
  }

}
