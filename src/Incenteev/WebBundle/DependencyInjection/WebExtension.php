<?php

namespace Incenteev\WebBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WebExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('avatar.xml');
        $loader->load('comment.xml');
        $loader->load('contest.xml');
        $loader->load('doctrine.xml');
        $loader->load('form.xml');
        $loader->load('listeners.xml');
        $loader->load('locale.xml');
        $loader->load('mailer.xml');
        $loader->load('markdown.xml');
        $loader->load('notification.xml');
        $loader->load('reminder.xml');
        $loader->load('session.xml');
        $loader->load('twig.xml');
        $loader->load('util.xml');
        $loader->load('validator.xml');

        $container->setAlias(
            'incenteev.util.base_url_resolver.uploads',
            new Alias(sprintf('incenteev.util.base_url_resolver.uploads.%s', $config['uploads_url_resolver']), false)
        );

        // Set the preset here as we need to cast the parameter to a string whereas it may be null.
        $assetsVersion = $container->getParameter('assets_version');

        if (null !== $assetsVersion) {
            $assetsVersion = '?' . $assetsVersion;
        }

        $container->setParameter('assetic.filter.lessphp.presets', array('assetVersion' => sprintf('"%s"', $assetsVersion)));

        // Adding this extra class avoids to have a class map of exactly 4096 bytes which is the case otherwise
        $this->addClassesToCompile(array('Symfony\Bridge\Twig\Extension\RoutingExtension'));
    }
}
