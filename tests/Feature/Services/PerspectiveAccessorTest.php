<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\PerspectiveEditorBundle\Tests\Feature\Services;

use OpenDxp\Bundle\PerspectiveEditorBundle\Services\PerspectiveAccessor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

function perspectiveTree(array $iconConfig = ['iconCls' => null, 'icon' => '/bundles/opendxpadmin/img/flat-white-icons/book.svg']): array
{
    return [
        'id' => 'root',
        'children' => [[
            'name' => 'Catalog',
            'type' => 'perspective',
            'children' => [
                ['type' => 'icon', 'config' => $iconConfig],
                ['type' => 'elementTree', 'children' => [
                    ['config' => ['type' => 'customview', 'id' => 6, 'expanded' => false, 'hidden' => false]],
                ]],
                ['type' => 'elementTreeRight', 'children' => [
                    ['config' => ['type' => 'asset', 'expanded' => false, 'hidden' => false, 'treeContextMenu' => ['asset' => ['items' => ['add' => true]], 'object' => ['items' => ['add' => true]]]]],
                    ['config' => ['type' => 'customview', 'id' => 1, 'expanded' => false, 'hidden' => false]],
                ]],
                ['type' => 'dashboard', 'config' => []],
                ['type' => 'toolbar', 'config' => ['file' => ['hidden' => false, 'items' => ['perspectives' => true]], 'ecommerce' => false]],
            ],
        ]],
    ];
}

it('saves a perspective with its trees, icon and toolbar', function () {
    $accessor = new PerspectiveAccessor('');
    $accessor->writeConfiguration(perspectiveTree(), null);

    $catalog = $accessor->getConfiguration()['Catalog'];

    expect($catalog['icon'])->toBe('/bundles/opendxpadmin/img/flat-white-icons/book.svg')
        ->and($catalog['toolbar'])->toMatchArray(['file' => ['hidden' => false, 'items' => ['perspectives' => true]], 'ecommerce' => false])
        ->and(array_map(static fn (array $tree): array => [$tree['type'], $tree['position'], $tree['sort']], $catalog['elementTree']))
        ->toBe([['customview', 'left', 0], ['asset', 'right', 0], ['customview', 'right', 1]]);
});

it('keeps only the context menu of the tree type', function () {
    $accessor = new PerspectiveAccessor('');
    $accessor->writeConfiguration(perspectiveTree(), null);

    expect(array_keys($accessor->getConfiguration()['Catalog']['elementTree'][1]['treeContextMenu']))->toBe(['asset']);
});

it('refuses a perspective with a setting OpenDXP does not know', function () {
    (new PerspectiveAccessor(''))->writeConfiguration(perspectiveTree(['icon' => '/book.svg', 'unknownSetting' => '1']), null);
})->throws(InvalidConfigurationException::class);
