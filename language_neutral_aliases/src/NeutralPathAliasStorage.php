<?php

namespace Drupal\language_neutral_aliases;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\path_alias\PathAliasStorage;

/**
 * Extend PathAliasStorage to save aliases with neutral language.
 */
class NeutralPathAliasStorage extends PathAliasStorage {

  /**
   * {@inheritdoc}
   */
  public function create(array $values = []) {
    $entity = parent::create($values);

    $langKey = $entity->getEntityType()->getKey('langcode');

    $entity->{$langKey} = LanguageInterface::LANGCODE_NOT_SPECIFIED;

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(EntityInterface $entity) {
    $langKey = $entity->getEntityType()->getKey('langcode');

    if ($langKey && $entity->{$langKey} != LanguageInterface::LANGCODE_NOT_SPECIFIED) {
      $entity->{$langKey} = LanguageInterface::LANGCODE_NOT_SPECIFIED;
    }

    return parent::save($entity);
  }

}
