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
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('crawl');

        $rootNode
            ->children()
                ->append($this->addNotificationNode())
            ->end()
            ->children()
                ->append($this->addStorageNode())
            ->end()
            ->children()
                ->append($this->addExchangeNode())
            ->end()
            ->children()
                ->append($this->addWeatherNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * Config notification section
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function addNotificationNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('notification');

        $node
            ->children()
                ->scalarNode('email')->isRequired()->defaultNull()->end()
                ->scalarNode('smtp_host')->isRequired()->defaultNull()->end()
                ->scalarNode('smtp_port')->isRequired()->defaultValue(25)->end()
                ->scalarNode('smtp_user')->defaultNull()->end()
                ->scalarNode('smtp_password')->defaultNull()->end()
                ->scalarNode('smtp_from')->defaultNull()->end()
            ->end();

        return $node;
    }

    /**
     * Config storage section
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function addStorageNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('storage');

        $node
            ->children()
                ->arrayNode('mysql')->isRequired()
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
     */
    private function addExchangeNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('exchange');

        $node
            ->children()
                ->booleanNode('notification')->defaultFalse()->end()
                ->arrayNode('sources')->isRequired()->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('url')->isRequired()->end()
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
     */
    private function addWeatherNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('weather');

        $node
            ->children()
                ->booleanNode('notification')->defaultFalse()->end()
                ->enumNode('unit')->isRequired()->values(['C', 'F'])->end()
                ->arrayNode('sources')->isRequired()->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('url')->isRequired()->end()
                            ->scalarNode('lang')->end()
                            ->scalarNode('apiKey')->end()
                            ->arrayNode('stations')->isRequired()->requiresAtLeastOneElement()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
