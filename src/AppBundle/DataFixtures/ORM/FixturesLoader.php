<?php

/**
 * @link http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 * @link https://github.com/nelmio/alice
 * @link https://github.com/fzaninotto/Faker
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load($this->getFixtures(), $manager, ['providers' => [$this]]);
    }

    public function getFixtures()
    {
        $kernel = $GLOBALS['kernel'];
        $env = $kernel->getEnvironment();

        echo "\nEnvironment is: " . $env . "\n\n";

        if ($env == 'test') {
            return [
                __DIR__.'/test/product.yml',
            ];
        }
        return [
            __DIR__.'/dev/product.yml',
        ];
    }

    public function type()
    {
        $type = [
            'configurable',
            'simple',
        ];

        return $type[array_rand($type)];
    }

}