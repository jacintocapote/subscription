<?php

/**
 * @file
 * Contains \Drupal\subscription\Form\SubscriptionDeleteForm.
 */

namespace Drupal\subscription\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a subscripion entity.
 *
 * @ingroup subscription
 */
class SubscriptionDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete entity %name?', array('%name' => $this->entity->label()));
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
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. logger() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->delete();

    drupal_set_message($this->t('Deleted %title successful.',
    [
        '%title' => $this->entity->name->value,
    ]));

    $this->logger('subscription')->notice('deleted %title.',
      array(
        '%title' => $this->entity->name->value,
      ));
    // Redirect to term list after delete.
    $form_state->setRedirect('entity.subscription.collection');
  }

}
