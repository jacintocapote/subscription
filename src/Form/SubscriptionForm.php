<?php
/**
 * @file
 * Contains \Drupal\subscription\Form\SubscriptionForm.
 */

namespace Drupal\subscription\Form;

use Drupal\Core\Entity\ContentEntityForm;
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
 * Form controller for the subscription entity edit forms.
 *
 * @ingroup subscription.
 */
class SubscriptionForm extends ContentEntityForm {

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
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\dictionary\Entity\Term */
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $date = new \DateTime($form_state->getValue('birthday')[0]['value']->format('Y-m-d H:i:s'));
    $now = new \DateTime();
    $interval = $now->diff($date);
    $current_age = $interval->y;

    $config = $this->configFactory->get('subscription.settings');


    // Validate min_age.
    if ($current_age <= $config->get('min_age')) {
      $form_state->setErrorByName('birthday', t('Please insert a valid birthday. The min age is %min_age', ['%min_age' => $config->get('min_age')]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->save();
    $this->notify($entity);
    drupal_set_message($this->t('Created %title successful.',
      [
        '%title' => $this->entity->name->value,
      ]));
    $this->logger('subscription')->notice('Created %title successful.',
      [
        '%title' => $this->entity->name->value,
      ]);
  }

  private function notify($entity) {
    $language = $this->languageManager->getCurrentLanguage();
    $config = $this->configFactory->get('subscription.settings');
    $options = ['absolute' => TRUE];
    $link = Url::fromRoute('entity.subscription.collection', [], $options);

    $module = 'subscription';
    $key = 'create_subscription';
    $to = $config->get('notify_mail');
    $params['full_name'] = $entity->name->value . ' ' . $entity->name->surname;
    $params['mail'] = $entity->mail->value;
    $params['link'] = $link;

    $result = $this->mailManager->mail($module, $key, $to, $language, $params, NULL, TRUE);
    if (!$result) {
      drupal_set_message($this->t('Error sending notification'), 'error');
    }
  }

}
