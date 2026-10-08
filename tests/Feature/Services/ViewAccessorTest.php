<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\PerspectiveEditorBundle\Tests\Feature\Services;

use InvalidArgumentException;
use OpenDxp\Bundle\PerspectiveEditorBundle\Services\ViewAccessor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @param array<string, mixed> $settings settings the custom view "cars" takes on top of its own
 *
 * @return array<string, mixed> the tree the editor sends for the custom view
 */
function viewTree(array $settings): array
{
    return [
        'id' => 'root',
        'children' => [
            [
                'id' => 'cars',
                'config' => [
                    'treetype' => 'object',
                    'name' => 'Cars & Bikes',
                    'id' => 'cars',
                    'rootfolder' => '/Product Data/Cars',
                    'showroot' => false,
                    'classes' => '',
                    'position' => 'left',
                    'sort' => 3,
                    'expanded' => true,
                    'treeContextMenu' => [
                        'object' => [
                            'items' => [
                                'add' => true,
                                'addFolder' => true,
                            ],
                        ],
                        'document' => ['items' => ['add' => true]],
                    ],
                    ...$settings,
                ],
            ],
        ],
    ];
}

it('saves a custom view with the context menu of its tree type only', function () {
    $accessor = new ViewAccessor('');

    $accessor->writeConfiguration(viewTree([]), null);

    $view = $accessor->getConfiguration()['views']['cars'];
    expect($view)
        ->toMatchArray([
            'treetype' => 'object',
            'name' => 'Cars &amp; Bikes',
            'rootfolder' => '/Product Data/Cars',
        ])
        ->and($view['treeContextMenu'])
        ->toBe([
            'object' => [
                'items' => [
                    'add' => true,
                    'addFolder' => true,
                ],
            ],
        ]);
});

it('refuses a custom view with a setting OpenDXP does not know', function () {
    $tree = viewTree(['unknownSetting' => 1]);

    expect(fn () => (new ViewAccessor(''))->writeConfiguration($tree, null))
        ->toThrow(InvalidConfigurationException::class);
});

it('refuses a condition that changes data', function (string $clause) {
    $tree = viewTree([$clause => 'id > 0; DELETE FROM objects']);

    expect(fn () => (new ViewAccessor(''))->writeConfiguration($tree, null))
        ->toThrow(InvalidArgumentException::class, 'Invalid SQL definition');
})->with([
    'in the where clause' => ['where'],
    'in the having clause' => ['having'],
]);
