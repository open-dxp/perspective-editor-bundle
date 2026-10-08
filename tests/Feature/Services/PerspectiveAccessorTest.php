<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\PerspectiveEditorBundle\Tests\Feature\Services;

use OpenDxp\Bundle\PerspectiveEditorBundle\Services\PerspectiveAccessor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

const BOOK_ICON = '/bundles/opendxpadmin/img/flat-white-icons/book.svg';

/**
 * @param array<string, mixed> $iconConfig
 *
 * @return array<string, mixed> the tree the editor sends for a perspective named "Catalog"
 */
function perspectiveTree(array $iconConfig): array
{
    return [
        'id' => 'root',
        'children' => [
            [
                'name' => 'Catalog',
                'type' => 'perspective',
                'children' => [
                    [
                        'type' => 'icon',
                        'config' => $iconConfig,
                    ],
                    [
                        'type' => 'elementTree',
                        'children' => [
                            [
                                'config' => [
                                    'type' => 'customview',
                                    'id' => 6,
                                    'expanded' => false,
                                    'hidden' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'elementTreeRight',
                        'children' => [
                            [
                                'config' => [
                                    'type' => 'asset',
                                    'expanded' => false,
                                    'hidden' => false,
                                    'treeContextMenu' => [
                                        'asset' => ['items' => ['add' => true]],
                                        'object' => ['items' => ['add' => true]],
                                    ],
                                ],
                            ],
                            [
                                'config' => [
                                    'type' => 'customview',
                                    'id' => 1,
                                    'expanded' => false,
                                    'hidden' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'dashboard',
                        'config' => [],
                    ],
                    [
                        'type' => 'toolbar',
                        'config' => [
                            'file' => [
                                'hidden' => false,
                                'items' => ['perspectives' => true],
                            ],
                            'ecommerce' => false,
                        ],
                    ],
                ],
            ],
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function bookIcon(): array
{
    return [
        'iconCls' => null,
        'icon' => BOOK_ICON,
    ];
}

it('saves a perspective with its trees, icon and toolbar', function () {
    $accessor = new PerspectiveAccessor('');

    $accessor->writeConfiguration(perspectiveTree(bookIcon()), null);

    $catalog = $accessor->getConfiguration()['Catalog'];
    $trees = array_map(
        static fn (array $tree): array => [
            $tree['type'],
            $tree['position'],
            $tree['sort'],
        ],
        $catalog['elementTree'],
    );
    expect($catalog['icon'])
        ->toBe(BOOK_ICON)
        ->and($catalog['toolbar'])
        ->toMatchArray([
            'file' => [
                'hidden' => false,
                'items' => ['perspectives' => true],
            ],
            'ecommerce' => false,
        ])
        ->and($trees)
        ->toBe([
            ['customview', 'left', 0],
            ['asset', 'right', 0],
            ['customview', 'right', 1],
        ]);
});

it('keeps only the context menu of the tree type', function () {
    $accessor = new PerspectiveAccessor('');

    $accessor->writeConfiguration(perspectiveTree(bookIcon()), null);

    $menu = $accessor->getConfiguration()['Catalog']['elementTree'][1]['treeContextMenu'];
    expect(array_keys($menu))->toBe(['asset']);
});

it('refuses a perspective with a setting OpenDXP does not know', function () {
    $tree = perspectiveTree([
        'icon' => '/book.svg',
        'unknownSetting' => '1',
    ]);

    expect(fn () => (new PerspectiveAccessor(''))->writeConfiguration($tree, null))
        ->toThrow(InvalidConfigurationException::class);
});
