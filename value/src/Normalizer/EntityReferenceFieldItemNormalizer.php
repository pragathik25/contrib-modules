<?php

namespace Drupal\value\Normalizer;

use Drupal\Core\Config\Entity\EntityBundleWithPluralLabelsInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\field\Entity\FieldConfig;

class EntityReferenceFieldItemNormalizer extends NormalizerBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = [EntityReferenceItem::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $attributes = [];

    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    if ($entity = $object->get('entity')->getValue()) {
      $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
      if ($entity instanceof TranslatableInterface && $entity->hasTranslation($langcode)) {
        $entity = $entity->getTranslation($langcode);
      }
      $attributes['label'] = $entity->label();
      if ($entity instanceof EntityBundleWithPluralLabelsInterface) {
        $attributes['label_singular'] = $entity->getSingularLabel();
        $attributes['label_plural'] = $entity->getPluralLabel();
      }
      $attributes['target_type'] = $entity->getEntityTypeId();
      $attributes['target_uuid'] = $entity->uuid();
      $attributes['target_id'] = $entity->id();
      $attributes['target_bundle'] = $entity->bundle();
    }

    return $attributes;
  }

}
