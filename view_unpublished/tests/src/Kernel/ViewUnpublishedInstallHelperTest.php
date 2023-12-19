<?php

declare(strict_types = 1);

namespace Drupal\Tests\view_unpublished\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;
use function array_flip;

/**
 * Tests the View Unpublished dependency issue.
 *
 * @group view_unpublished
 *
 * @todo Remove this in 2.0.x.
 *
 * @coversDefaultClass \Drupal\view_unpublished\ViewUnpublishedInstallHelper
 */
class ViewUnpublishedInstallHelperTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'node',
    'system',
    'text',
    'user',
    'view_unpublished',
    'views',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig('node');
  }

  /**
   * @testdox Tests view_unpublished not added as dependency of content view.
   *
   * @coversNothing
   */
  public function testDependencyNotAdded(): void {
    // Check dependency before saving.
    $module_deps = $this->config('views.view.content')->get('dependencies.module');
    $this->assertIsArray($module_deps);
    $this->assertArrayNotHasKey('view_unpublished', array_flip($module_deps));

    // Save and check again.
    $view = Views::getView('content');
    $this->assertInstanceOf(ViewExecutable::class, $view);
    $view->save();
    $module_deps = $this->config('views.view.content')->get('dependencies.module');
    $this->assertIsArray($module_deps);
    $this->assertArrayNotHasKey('view_unpublished', array_flip($module_deps));
  }

  /**
   * @testdox Tests the remove dependency install helper.
   *
   * @covers ::removeDependency
   */
  public function testDependencyRemoved(): void {
    $module_deps = $this->config('views.view.content')->get('dependencies.module');
    $this->assertIsArray($module_deps);
    $module_deps[] = 'view_unpublished';
    $this->config('views.view.content')->set('dependencies.module', $module_deps)->save(TRUE);
    $this->container->get('view_unpublished.install_helper')->removeDependency();
    $module_deps = $this->config('views.view.content')->get('dependencies.module');
    $this->assertIsArray($module_deps);
    $this->assertGreaterThan(0, count($module_deps));
    $this->assertArrayNotHasKey('view_unpublished', array_flip($module_deps));
  }

}
