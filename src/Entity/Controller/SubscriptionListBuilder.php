<?php

/**
 * @file
 * Contains \Drupal\subscription\Entity\Controller\SubscriptionListBuilder.
 */

namespace Drupal\subscription\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for subscription entity.
 *
 * @ingroup dictionary
 */
class SubscriptionListBuilder extends EntityListBuilder {

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;


  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('url_generator')
    );
  }

  /**
   * Constructs a new SubscriptionListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type term.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entity_query = $this->storage
      ->getQuery();

    $entity_query
      ->pager(10);

    $ids = $entity_query
      ->execute();
    return $this->storage
      ->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('Content Entity Example implements a Subscription model.')
    ];
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the Subscription list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {

    $header['name'] = $this->t('Name');
    $header['surname'] = $this->t('Surname');
    $header['mail'] = $this->t('E-mail');
    $header['dni'] = $this->t('DNI');
    $header['birthday'] = $this->t('Birthday');
    $header['status'] = $this->t('Status');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    /* @var $entity \Drupal\subscription\Entity\Subscription */
    $row['name'] = $entity->name->value;
    $row['surname'] = $entity->surname->value;
    $row['mail'] = $entity->mail->value;
    $row['dni'] = $entity->dni->value;
    $row['birthday'] = $entity->birthday->value;
    $row['status'] = $entity->status->value;

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    if ($entity
      ->access('update') && $entity
      ->hasLinkTemplate('approve-form')) {
      $operations['approve'] = [
        'title' => $this
          ->t('Approve'),
        'weight' => 11,
        'url' => $entity
          ->urlInfo('approve-form'),
      ];
      $operations['deny'] = [
        'title' => $this
          ->t('Deny'),
        'weight' => 12,
        'url' => $entity
          ->urlInfo('deny-form'),
      ];
    }

    return $operations;
  }

}
