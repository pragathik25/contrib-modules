<?php

namespace Drupal\Tests\language_neutral_aliases\Kernel;

use Drupal\Core\Language\LanguageInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Test language neutral aliases.
 *
 * @group language_neutral_aliases
 */
class LanguageNeutralAliasesTest extends KernelTestBase {

  /**
   * Table name for path_alias.
   *
   * There's probably a more proper way to figure it out, but this will suffice
   * for the moment.
   */
  const TABLE = 'path_alias';

  /**
   * The source/path field name.
   *
   * @var string
   */
  protected $field;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['language_neutral_aliases', 'path_alias'];

  /**
   * Setup test.
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('path_alias');

    $database = $this->container->get('database');

    // Create some test data.
    $storage = $this->container->get('entity_type.manager')->getStorage('path_alias');

    $paths = [
      '/node/1' => '/path/first',
      '/node/2' => '/path/second',
      '/node/3' => '/path/third',
    ];
    foreach ($paths as $path => $alias) {
      $values = [
        'path' => $path,
        'alias' => $alias,
      ];
      $alias = $storage->create($values);

      $storage->save($alias);
    }

    // Change the language of some aliases.
    $database->update(self::TABLE)
      ->fields(['langcode' => 'de'])
      ->condition('path', '/node/1')
      ->execute();
    $database->update(self::TABLE)
      ->fields(['langcode' => 'da'])
      ->condition('path', '/node/2')
      ->execute();

    $database->update(self::TABLE . '_revision')
      ->fields(['langcode' => 'de'])
      ->condition('path', '/node/1')
      ->execute();
    $database->update(self::TABLE . '_revision')
      ->fields(['langcode' => 'da'])
      ->condition('path', '/node/2')
      ->execute();
  }

  /**
   * Test that new aliases gets saved with language neutral.
   */
  public function testSave() {
    $storage = $this->container->get('entity_type.manager')->getStorage('path_alias');

    // A new alias with a language code should be saved as neutral.
    $values = [
      'path' => '/node/4',
      'alias' => '/path/fourth',
      'langcode' => 'de',
    ];
    $alias = $storage->create($values);

    $storage->save($alias);

    $actual = $storage->load($alias->id());
    $this->assertEquals(4, $actual->id());
    $this->assertEquals('/node/4', $actual->getPath());
    $this->assertEquals('/path/fourth', $actual->getAlias());
    $this->assertEquals(LanguageInterface::LANGCODE_NOT_SPECIFIED, $actual->get('langcode')->value);

    // Non-neutral aliases should be updated. This is not by design, but a side
    // effect of the move to entities. We can't return a clone when saving. But
    // as PathFieldItemList loads the alias through the repository, existing
    // non-neutral aliases should be hidden, and thus never saved.
    $alias = $storage->load(1);
    $alias->setAlias('/path/fifth');
    $storage->save($alias);

    $actual = $storage->load($alias->id());
    $this->assertEquals(1, $actual->id());
    $this->assertEquals('/node/1', $actual->getPath());
    $this->assertEquals('/path/fifth', $actual->getAlias());
    $this->assertEquals(LanguageInterface::LANGCODE_NOT_SPECIFIED, $actual->get('langcode')->value);

    // Ensure that language neutral aliases can be updated.
    $alias = $storage->load(4);
    $alias->setAlias('/path/sixth');

    $actual = $storage->load(4);
    $this->assertEquals(4, $actual->id());
    $this->assertEquals('/node/4', $actual->getPath());
    $this->assertEquals('/path/sixth', $actual->getAlias());
    $this->assertEquals(LanguageInterface::LANGCODE_NOT_SPECIFIED, $actual->get('langcode')->value);
  }

  /**
   * Test that preloadPathAlias() only returns language neutral aliases.
   */
  public function testPreloadPathAlias() {
    $repository = $this->container->get('path_alias.repository');

    $this->assertEquals(['/node/3' => '/path/third'], $repository->preloadPathAlias(['/node/1', '/node/3'], LanguageInterface::LANGCODE_NOT_SPECIFIED));

    $this->assertEquals(['/node/3' => '/path/third'], $repository->preloadPathAlias(['/node/1', '/node/3'], 'de'));
  }

  /**
   * Test that lookupBySystemPath() only returns language neutral aliases.
   */
  public function testLookupBySystemPath() {
    $repository = $this->container->get('path_alias.repository');

    $this->assertEquals([
      'id' => 3,
      'path' => '/node/3',
      'alias' => '/path/third',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ], $repository->lookupBySystemPath('/node/3', LanguageInterface::LANGCODE_NOT_SPECIFIED));

    $this->assertEquals([
      'id' => 3,
      'path' => '/node/3',
      'alias' => '/path/third',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ], $repository->lookupBySystemPath('/node/3', 'de'));

    // Check that non-neutral aliases aren't returned.
    $this->assertNull($repository->lookupBySystemPath('/node/1', LanguageInterface::LANGCODE_NOT_SPECIFIED));

    $this->assertNull($repository->lookupBySystemPath('/node/1', 'de'));
  }

  /**
   * Test that lookupByAlias() only returns language neutral aliases.
   */
  public function testLookupByAlias() {
    $repository = $this->container->get('path_alias.repository');

    $this->assertEquals([
      'id' => 3,
      'path' => '/node/3',
      'alias' => '/path/third',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ], $repository->lookupByAlias('/path/third', LanguageInterface::LANGCODE_NOT_SPECIFIED));

    $this->assertEquals([
      'id' => 3,
      'path' => '/node/3',
      'alias' => '/path/third',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ], $repository->lookupByAlias('/path/third', 'de'));

    $this->assertNull($repository->lookupByAlias('/path/first', LanguageInterface::LANGCODE_NOT_SPECIFIED));

    $this->assertNull($repository->lookupByAlias('/path/first', 'de'));
  }

  /**
   * Test that pathHasMatchingAlias only checks language neutral aliases.
   */
  public function testPathHasMatchingAlias() {
    $repository = $this->container->get('path_alias.repository');

    $this->assertFalse($repository->pathHasMatchingAlias('/node/1'));
    $this->assertTrue($repository->pathHasMatchingAlias('/node/3'));
  }

}
