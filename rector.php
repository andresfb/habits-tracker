<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use RectorLaravel\Rector\ClassMethod\ScopeNamedClassMethodToScopeAttributedClassMethodRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__.'/app',
            __DIR__.'/bootstrap/app.php',
            __DIR__.'/database',
            __DIR__.'/public',
        ])
        ->withSkip([
            __DIR__.'/bootstrap/cache',
            __DIR__.'/storage',
            __DIR__.'/node_modules',
            __DIR__.'/vendor',
            AddOverrideAttributeToOverriddenMethodsRector::class,
            DisallowedEmptyRuleFixerRector::class,
            ScopeNamedClassMethodToScopeAttributedClassMethodRector::class,
        ])
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            typeDeclarations: true,
            privatization: true,
            earlyReturn: true,
            strictBooleans: true,
        )
        ->withSets([
            LaravelLevelSetList::UP_TO_LARAVEL_120,
            LaravelSetList::LARAVEL_CODE_QUALITY,
            LaravelSetList::LARAVEL_COLLECTION,
        ])
        ->withPhpSets(
            php83: true,
        );
} catch (InvalidConfigurationException $e) {
    echo 'Invalid configuration: '.$e->getMessage();
}
