<?php

declare(strict_types = 1);

namespace Drupal\Tests\view_unpublished\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\node\Traits\ContentTypeCreationTrait;

/**
 * Tests the View Unpublished module with views.
 *
 * @group view_unpublished
 */
class ViewUnpublishedViewsTest extends BrowserTestBase {

  use ContentTypeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'view_unpublished',
    'node',
    'views',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Node of type page.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $pageNode;

  /**
   * Node of type article.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $articleNode;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Rebuild node access which we have to do after installing the module.
    $this->drupalLogin($this->rootUser);
    node_access_rebuild();
    $this->drupalLogout();

    $this->createContentType(['type' => 'page']);
    $this->createContentType(['type' => 'article']);
    $this->pageNode = $this->createNode(['type' => 'page']);
    $this->pageNode->setUnpublished();
    $this->pageNode->save();
    $this->articleNode = $this->createNode(['type' => 'article']);
    $this->articleNode->setUnpublished();
    $this->articleNode->save();
  }

  /**
   * Test the node access based on any, content type specific or none.
   *
   * @dataProvider nodeAccessData
   */
  public function testNodeAccess(array $permissions, bool $page_access, bool $article_access): void {
    $user = $this->createUser($permissions);
    $this->assertInstanceOf(AccountInterface::class, $user);
    $this->drupalLogin($user);
    $this->drupalGet('admin/content');
    if ($page_access) {
      $this->assertSession()->pageTextContains((string) $this->pageNode->label());
    }
    else {
      $this->assertSession()->pageTextNotContains((string) $this->pageNode->label());
    }
    if ($article_access) {
      $this->assertSession()->pageTextContains((string) $this->articleNode->label());
    }
    else {
      $this->assertSession()->pageTextNotContains((string) $this->articleNode->label());
    }
  }

  /**
   * Data provider for ::testNodeAccess.
   */
  public function nodeAccessData(): \Generator {
    yield [
      [
        'view any unpublished content',
        'access content overview',
      ],
      TRUE,
      TRUE,
    ];
    yield [
      [
        'view any unpublished page content',
        'view any unpublished article content',
        'access content overview',
      ],
      TRUE,
      TRUE,
    ];
    yield [
      [
        'view any unpublished page content',
        'access content overview',
      ],
      TRUE,
      FALSE,
    ];
    yield [
      [
        'view any unpublished article content',
        'access content overview',
      ],
      FALSE,
      TRUE,
    ];
    yield [
      [
        'access content overview',
      ],
      FALSE,
      FALSE,
    ];
  }

}
