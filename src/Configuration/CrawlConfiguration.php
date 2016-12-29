<?php

namespace Stingus\Crawler\Configuration;

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
                ->arrayNode('storage')
                    ->children()
                        ->arrayNode('mysql')
                            ->children()
                                ->scalarNode('host')->defaultValue('127.0.0.1')->end()
                                ->scalarNode('db')->defaultValue('crawler')->end()
                                ->scalarNode('user')->defaultValue('crawler')->end()
                                ->scalarNode('password')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('exchange')
                    ->children()
                        ->arrayNode('sources')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                            ->children()
                                ->scalarNode('class')->end()
                                ->scalarNode('url')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
