<?php

namespace NuvoleWeb\Drupal\DrupalExtension\ServiceContainer;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Drupal\DrupalExtension\ServiceContainer\DrupalExtension as OriginalDrupalExtension;

/**
 * Class DrupalExtension.
 *
 * @package NuvoleWeb\Drupal\DrupalExtension\ServiceContainer
 */
class DrupalExtension extends OriginalDrupalExtension {

  /**
   * {@inheritdoc}
   */
  public function load(ContainerBuilder $container, array $config) {
    parent::load($container, $config);

    // Search for services.yml in following paths in order to perform overrides.
    $paths[] = __DIR__ . '/../../..';
    $paths[] = $container->getParameter('paths.base');
    $container_overrides = new ContainerBuilder();
    $loader = new YamlFileLoader($container_overrides, new FileLocator($paths));
    $loader->load('services.yml');
    $container->merge($container_overrides);

    $this->loadContextInitializer($container);
  }

  /**
   * Load context initializer.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
   *    Service container instance.
   */
  private function loadContextInitializer(ContainerBuilder $container) {
    // Set current service container instance for service container initializer.
    $definition = $container->getDefinition('drupal.behat.context_initializer.service_container');
    $definition->addArgument($container);
  }

  /**
   * {@inheritdoc}
   */
  public function configure(ArrayNodeDefinition $builder) {
    parent::configure($builder);

    // @codingStandardsIgnoreStart
    $builder->
      children()->
        arrayNode('text')->
          info(
            'Text strings, such as Log out or the Username field can be altered via behat.yml if they vary from the default values.' . PHP_EOL
            . '  log_out: "Sign out"' . PHP_EOL
            . '  log_in: "Sign in"' . PHP_EOL
            . '  password_field: "Enter your password"' . PHP_EOL
            . '  username_field: "Nickname"' . PHP_EOL
            . '  node_submit_label: "Save"'
          )->
          addDefaultsIfNotSet()->
            children()->
              scalarNode('log_in')->
                defaultValue('Log in')->
              end()->
              scalarNode('log_out')->
                defaultValue('Log out')->
              end()->
              scalarNode('password_field')->
                defaultValue('Password')->
              end()->
              scalarNode('username_field')->
                defaultValue('Username')->
              end()->
              scalarNode('node_submit_label')->
                defaultValue('Save')->
              end()->
          end()->
        end()->
      end();
    // @codingStandardsIgnoreEnd
  }

}
