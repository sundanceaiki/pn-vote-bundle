<?php declare(strict_types=1);

namespace VoteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pn_vote');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->scalarNode('cookie_name')->defaultValue('pnvote')->isRequired()->end()
                ->scalarNode('cookie_lifetime')->defaultValue('+1 year')->isRequired()->end()
                ->booleanNode('csrf')->defaultValue(false)->isRequired()->end()
            ->end();

        return $treeBuilder;
    }
}