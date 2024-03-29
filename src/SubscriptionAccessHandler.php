<?php

/**
 * @file
 * Contains \Drupal\subscription\SubscriptionAccessHandler.
 */

namespace Drupal\subscription;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the subscription entity.
 *
 * @see \Drupal\subscription\Entity\Subscription.
 */
class SubscriptionAccessHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit subscription entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete subscription entity');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add subscription entity');
  }

}

