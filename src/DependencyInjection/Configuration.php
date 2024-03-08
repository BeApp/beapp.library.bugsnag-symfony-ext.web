<?php

namespace Beapp\Bugsnag\Ext\DependencyInjection;

use Bugsnag\Client;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * The name of the root of the configuration.
     *
     * @var string
     */
    const ROOT_NAME = 'bugsnag_ext';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NAME);

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('handled_exceptions')
            ->prototype('scalar')->end()
            ->treatNullLike([])
            ->defaultValue([])
            ->end()
            ->arrayNode('excluded_http_codes')
            ->prototype('scalar')->end()
            ->treatNullLike([])
            ->defaultValue([])
            ->end()
            ->scalarNode('client')
            ->defaultValue(Client::class)
            ->end()
            ->booleanNode('session_per_request')
            ->defaultValue(true)
            ->end();

        return $treeBuilder;
    }
}
