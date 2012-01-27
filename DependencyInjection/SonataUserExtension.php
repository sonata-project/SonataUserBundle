<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataUserExtension extends Extension
{

    /**
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin_orm.xml');
        $loader->load('form.xml');

        if ($config['security_acl']) {
            $loader->load('security_acl.xml');
        }

        $this->registerDoctrineMapping($config);
        $this->configureClass($config, $container);

        // add custom form widgets
        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array('SonataUserBundle:Form:form_admin_fields.html.twig')
        ));
    }

    /**
     * @param $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.user.admin.user.entity', $config['class']['user']);
        $container->setParameter('sonata.user.admin.group.entity', $config['class']['group']);
    }

    /**
     * @param array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['user'], 'mapManyToMany', array(
            'fieldName'       => 'groups',
            'targetEntity'    => $config['class']['group'],
            'cascade'         => array( ),
            'joinTable'       => array(
                'name' => $config['table']['user_group'],
                'joinColumns' => array(
                    array(
                        'name' => 'user_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
                'inverseJoinColumns' => array( array(
                    'name' => 'group_id',
                    'referencedColumnName' => 'id',
                )),
            )
        ));
    }
}