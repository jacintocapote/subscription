<?php

/**
 * @file
 * Contains \Drupal\subscription\Form\SubscriptionAcceptedForm.
 */

namespace Drupal\subscription\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Url;

/**
 * Provides a form for Denied a subscripion entity.
 *
 * @ingroup subscription
 */
class SubscriptionDeniedForm extends ContentEntityConfirmFormBase {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;


  /**
   * Constructor.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, ConfigFactoryInterface $configFactory, MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager) {
    $this->entityRepository = $entity_repository;
    $this->entityTypeBundleInfo = $entity_type_bundle_info ?: \Drupal::service('entity_type.bundle.info');
    $this->time = $time ?: \Drupal::service('datetime.time');
    $this->configFactory = $configFactory;
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('config.factory'),
      $container->get('plugin.manager.mail'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to denied entity %name?', ['%name' => $this->entity->name->value]);
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
    return $this->t('Deny');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. logger() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->set('status', 'denied');
    $entity->save();

    $this->logger('subscription')->notice('Denied %title.',
      [
        '%title' => $this->entity->name->value,
      ]);
    // Redirect to term list after delete.
    $form_state->setRedirect('entity.subscription.collection');
  }

  private function notify($entity) {
    $language = $this->languageManager->getCurrentLanguage();
    $config = $this->configFactory->get('subscription.settings');
    $options = ['absolute' => TRUE];
    $link = Url::fromRoute('entity.subscription.collection', [], $options);

    $module = 'subscription';
    $key = 'deny_subscription';
    $to = $entity->mail->value;
    $params['full_name'] = $entity->name->value . ' ' . $entity->name->surname;
    $params['mail'] = $entity->mail->value;
    $params['link'] = $link;

    $result = $this->mailManager->mail($module, $key, $to, $language, $params, NULL, TRUE);
    if (!$result) {
      drupal_set_message($this->t('Error sending notification'), 'error');
    }
  }

}
