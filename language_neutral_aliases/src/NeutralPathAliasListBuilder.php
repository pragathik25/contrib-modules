<?php

namespace Drupal\language_neutral_aliases;

use Drupal\Core\Language\LanguageInterface;
use Drupal\path\PathAliasListBuilder;

/**
 * Extends path_alias list builder to only display neutral aliases.
 */
class NeutralPathAliasListBuilder extends PathAliasListBuilder {

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery();

    $search = $this->currentRequest->query->get('search');
    $query->condition('langcode', LanguageInterface::LANGCODE_NOT_SPECIFIED);
    if ($search) {
      $query->condition('alias', $search, 'CONTAINS');
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }

    // Allow the entity query to sort using the table header.
    $header = $this->buildHeader();
    $query->tableSort($header);

    return $query->execute();

  }

}
