<?php
/**
 * @file Drupal 8.8 class stubs
 *
 * When the module updated to release ^2 as the same time as Drupal is bumped
 * to release ^8.8 then classes in the new path_alias module are not available
 * for autoloading during database updates.
 *
 * This causes a class not found error. This produce a sub for these classes
 * which can be used in the process. Once the new classes are available and the
 * site is ready for use the stubs will be ignored.
 */

namespace Drupal\path_alias {
  if (!class_exists('Drupal\path_alias\AliasRepository')) {
    class AliasRepository extends \Drupal\Core\Path\AliasRepository {}
  }
}
