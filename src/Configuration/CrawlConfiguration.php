<?php

namespace Stingus\Crawler\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class CrawlConfiguration
 *
 * @package Stingus\Crawler\Configuration
 */
class CrawlConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('crawl');

        $rootNode
            ->children()
                ->append($this->addStorageNode())
            ->end()
            ->children()
                ->append($this->addExchangeNode())
            ->end()
            ->children()
                ->append($this->addWeatherNode())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Config storage section
     *
     * @return ArrayNodeDefinition|NodeDefinition
     * @throws \RuntimeException
     */
    private function addStorageNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('storage');

        $node
            ->children()
                ->arrayNode('mysql')
                    ->children()
                        ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                        ->scalarNode('db')->defaultValue('crawler')->end()
                        ->scalarNode('user')->defaultValue('crawler')->end()
                        ->scalarNode('password')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Config exchange section
     *
     * @return ArrayNodeDefinition|NodeDefinition
     * @throws \RuntimeException
     */
    private function addExchangeNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('exchange');

        $node
            ->children()
                ->arrayNode('sources')->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->end()
                            ->scalarNode('url')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Config weather section
     *
     * @return ArrayNodeDefinition|NodeDefinition
     * @throws \RuntimeException
     */
    private function addWeatherNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('weather');

        $node
            ->children()
                ->enumNode('unit')
                    ->values(['C', 'F'])
                ->end()
                ->arrayNode('sources')->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->end()
                            ->scalarNode('url')->end()
                            ->arrayNode('stations')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
