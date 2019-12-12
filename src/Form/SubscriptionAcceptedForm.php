<?php

/**
 * @file
 * Contains \Drupal\subscription\Form\SubscriptionAcceptedForm.
 */

namespace Drupal\subscription\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for Accept a subscripion entity.
 *
 * @ingroup subscription
 */
class SubscriptionAcceptedForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to approve entity %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   *
   * If the delete command is canceled, return to the subscription list.
   */
  public function getCancelUrl() {
    return new Url('entity.subscription.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Accept');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. logger() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->set('status', 'accepted');
    $entity->save();

    $this->logger('subscription')->notice('Accepted %title.',
      array(
        '%title' => $this->entity->name->value,
      ));
    // Redirect to term list after delete.
    $form_state->setRedirect('entity.subscription.collection');
  }

}
