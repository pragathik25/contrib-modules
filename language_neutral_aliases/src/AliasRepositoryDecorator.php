<?php

namespace Drupal\language_neutral_aliases;

use Drupal\Core\Database\Connection;
use Drupal\Core\Language\LanguageInterface;
use Drupal\path_alias\AliasRepository;

/**
 * Alias repository service decorator.
 *
 * Makes the repository only consider neutral language aliases.
 */
class AliasRepositoryDecorator extends AliasRepository {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The real alias repository service.
   *
   * @var \Drupal\path_alias\AliasRepository
   */
  protected $repository;

  /**
   * Constructs an AliasRepositoryDecorator.
   *.
   * @param \Drupal\path_alias\AliasRepository $repository
   *   The alias repository to decorate.
   *   This is intentionally *not* typed as a different class is used when
   *   updating from Drupal <8.8.
   * @param \Drupal\Core\Database\Connection
   *   The database connection used to retrieve aliases.
   */
  public function __construct($repository, Connection $connection) {
    parent::__construct($connection);
    $this->repository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public function preloadPathAlias($preloaded, $langcode) {
    return $this->repository->preloadPathAlias($preloaded, LanguageInterface::LANGCODE_NOT_SPECIFIED);
  }

  /**
   * {@inheritdoc}
   */
  public function lookupBySystemPath($path, $langcode) {
    return $this->repository->lookupBySystemPath($path, LanguageInterface::LANGCODE_NOT_SPECIFIED);
  }

  /**
   * {@inheritdoc}
   */
  public function lookupByAlias($alias, $langcode) {
    return $this->repository->lookupByAlias($alias, LanguageInterface::LANGCODE_NOT_SPECIFIED);
  }

  /**
   * {@inheritdoc}
   */
  public function pathHasMatchingAlias($initial_substring) {
    // Have to override as we can't pass a language code to the original.
    $query = $this->getBaseQuery();
    $query->addExpression(1);

    return (bool) $query
      ->condition('base_table.path', $this->connection->escapeLike($initial_substring) . '%', 'LIKE')
      ->condition('base_table.langcode', LanguageInterface::LANGCODE_NOT_SPECIFIED)
      ->range(0, 1)
      ->execute()
      ->fetchField();
  }

}
