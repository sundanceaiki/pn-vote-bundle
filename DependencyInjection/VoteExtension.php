<?php declare(strict_types=1);

namespace VoteBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VoteExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter("pn_vote.$key", $value);
        }

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        $this->addAnnotatedClassesToCompile(
            [
                'VoteBundle\\Controller\\VoteController'
            ]
        );
    }

    /**
     * The extension alias
     *
     * @return string
     */
    public function getAlias()
    {
        return 'pn_vote';
    }
}