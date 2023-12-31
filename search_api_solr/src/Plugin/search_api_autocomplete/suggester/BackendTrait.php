<?php

namespace Drupal\search_api_solr\Plugin\search_api_autocomplete\suggester;

use Drupal\search_api_solr_autocomplete\Plugin\search_api_autocomplete\suggester\BackendTrait as BackendTraitOriginal;

@trigger_error('The ' . __NAMESPACE__ . '\BackendTrait is deprecated in 4.2.4 and is removed from 4.3.0. Instead use \Drupal\search_api_solr_autocomplete\Plugin\search_api_autocomplete\suggester\BackendTrait. See https://www.drupal.org/node/3254186', E_USER_DEPRECATED);

/**
 * Provides a helper method for loading the search backend.
 *
 * @deprecated in search_api_solr:4.2.4 and is removed from
 *   search_api_solr:4.3.0. Use the
 *   \Drupal\search_api_solr_autocomplete\Plugin\search_api_autocomplete\suggester\BackendTrait
 *   instead.
 *
 * @see https://www.drupal.org/node/3254186
 * @see \Drupal\search_api_solr_autocomplete\Plugin\search_api_autocomplete\suggester\BackendTrait
 */
trait BackendTrait {
  use BackendTraitOriginal;

}
