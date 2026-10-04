<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\PerspectiveEditorBundle\Tests\Feature\Services;

use InvalidArgumentException;
use OpenDxp\Bundle\PerspectiveEditorBundle\Services\ViewAccessor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

function viewTree(array $config = []): array
{
    return [
        'id' => 'root',
        'children' => [[
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
                    'object' => ['items' => ['add' => true, 'addFolder' => true]],
                    'document' => ['items' => ['add' => true]],
                ],
                ...$config,
            ],
        ]],
    ];
}

it('saves a custom view with the context menu of its tree type only', function () {
    $accessor = new ViewAccessor('');
    $accessor->writeConfiguration(viewTree(), null);

    $view = $accessor->getConfiguration()['views']['cars'];

    expect($view)->toMatchArray(['treetype' => 'object', 'name' => 'Cars &amp; Bikes', 'rootfolder' => '/Product Data/Cars'])
        ->and($view['treeContextMenu'])->toBe(['object' => ['items' => ['add' => true, 'addFolder' => true]]]);
});

it('refuses a custom view with a setting OpenDXP does not know', function () {
    (new ViewAccessor(''))->writeConfiguration(viewTree(['unknownSetting' => 1]), null);
})->throws(InvalidConfigurationException::class);

it('refuses a condition that changes data', function (string $clause) {
    (new ViewAccessor(''))->writeConfiguration(viewTree([$clause => 'id > 0; DELETE FROM objects']), null);
})->with(['where', 'having'])->throws(InvalidArgumentException::class, 'Invalid SQL definition');
