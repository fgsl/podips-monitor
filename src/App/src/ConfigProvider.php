<?php

declare(strict_types=1);

namespace App;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class          => Handler\PingHandler::class,
                Handler\LogHandler::class           => Handler\LogHandler::class,
                Handler\KubernetesHandler::class    => Handler\KubernetesHandler::class,    
                Handler\QueueHandler::class         => Handler\QueueHandler::class,
                Handler\PodipsHandler::class        => Handler\PodipsHandler::class,
            ],
            'factories'  => [
                Handler\HomePageHandler::class      => Handler\HomePageHandlerFactory::class,
                Handler\ApiHandler::class           => Handler\ApiHandlerFactory::class
                
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'api'    => [__DIR__ . '/../templates/api'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }
}
