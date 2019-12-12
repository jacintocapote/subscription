<?php

namespace Drupal\subscription\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class SubscriptionSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'subscription.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'subscription_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['notify_mail'] = [
      '#type' => 'email',
      '#title' => $this->t('Mail to notify subscription'),
      '#default_value' => $config->get('notify_mail'),
    ];

    // Use range 1, to 100 probably we should be adjust.
    $form['min_age'] = [
      '#type' => 'select',
      '#title' => $this->t('Minimum Age'),
      '#options' => range(1, 100),
      '#default_value' => $config->get('min_age'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('notify_mail', $form_state->getValue('notify_mail'))
      // You can set multiple configurations at once by making
      // multiple calls to set().
      ->set('min_age', $form_state->getValue('min_age'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
