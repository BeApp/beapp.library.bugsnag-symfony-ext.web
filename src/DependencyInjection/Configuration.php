<?php

namespace Beapp\Bugsnag\Ext\DependencyInjection;

use Bugsnag\Client;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NAME);
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $this->getRootNode($treeBuilder);

        $rootNode
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

    /**
     * Returns the root node of TreeBuilder with backwards compatibility
     * for pre-Symfony 4.1.
     *
     * @param TreeBuilder $treeBuilder a TreeBuilder to extract/create the root node
     *                                 from
     *
     * @return NodeDefinition the root node of the config
     */
    protected function getRootNode(TreeBuilder $treeBuilder)
    {
        if (\method_exists($treeBuilder, 'getRootNode')) {
            return $treeBuilder->getRootNode();
        } else {
            return $treeBuilder->root(self::ROOT_NAME);
        }
    }
}
