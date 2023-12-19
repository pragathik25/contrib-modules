<?php

declare(strict_types = 1);

namespace Drupal\Tests\view_unpublished\Functional\Update;

use Drupal\FunctionalTests\Update\UpdatePathTestBase;
use function node_access_needs_rebuild;
use function node_access_rebuild;

/**
 * Test update hooks.
 *
 * @group legacy
 * @group view_unpublished
 *
 * @covers view_unpublished_update_8001()
 * @covers view_unpublished_update_8002()
 *
 * @todo Remove this in 2.0.x.
 */
class ViewUnpublishedUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles(): void {
    // Dump needs to be created in php 7.3.
    // See https://www.drupal.org/project/drupal/issues/3275093.
    $this->databaseDumpFiles = [
      __DIR__ . '/../../../fixtures/drupal-9.5.8.view-unpublished-8.x-1.0-alpha1.php.gz',
    ];
  }

  public function testUpdateHooks(): void {
    /** @var \Drupal\Core\Update\UpdateHookRegistry $update_hook_registry */
    $update_hook_registry = $this->container->get('update.update_hook_registry');
    $version = $update_hook_registry->getInstalledVersion('view_unpublished');
    $this->assertSame(8000, $version);
    $this->assertFalse(node_access_needs_rebuild());

    $this->drupalGet('/node/1');
    // Unpublished EN-Translation.
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalGet('/de/node/1');
    // Published DE-Translation.
    $this->assertSession()->statusCodeEquals(403);
    $this->assertSession()->pageTextNotContains('DE - Test');

    // Module was added as dependency due wrong implementation.
    $this->assertEquals(['node', 'user', 'view_unpublished'],
      $this->config('views.view.content')->get('dependencies.module')
    );

    $this->runUpdates();

    // view_unpublished_update_8001 set the needs rebuild flag.
    $this->assertTrue(node_access_needs_rebuild());
    $this->assertSession()->pageTextContains('A rebuild of node access permissions is necessary. Rebuilding may take some time if there is a lot of content or complex permission settings.');
    node_access_rebuild();
    $this->assertSame(8002, $update_hook_registry->getInstalledVersion('view_unpublished'));

    // Dependency was removed via view_unpublished_update_8002().
    $this->assertEquals(['node', 'user'],
      $this->config('views.view.content')->get('dependencies.module')
    );

    $this->drupalGet('/node/1');
    // Unpublished EN-Translation.
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalGet('/de/node/1');
    // Published DE-Translation.
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('DE - Test');

  }

}
