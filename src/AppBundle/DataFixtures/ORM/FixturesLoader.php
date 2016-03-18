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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadFixtureData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
                __DIR__.'/test/category.yml',
                __DIR__.'/test/product.yml',
            ];
        }
        return [
            __DIR__.'/dev/attribute.yml',
            __DIR__.'/dev/category.yml',
            __DIR__.'/dev/product.yml',
            __DIR__.'/dev/user.yml',
            __DIR__.'/dev/private_message.yml',
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

    public function password($plainPassword)
    {
        $user = new User();
        $encoder = $this->container->get('security.password_encoder');
        return $encoder->encodePassword($user, $plainPassword);
    }

    /**
     * @param User $user
     * @param User $admin
     * @param User $super_admin
     * @return User
     */
    public function user_or_admin_or_super_admin(User $user, User $admin, User $super_admin)
    {
        if (mt_rand(1, 100) > 20) {
            return $user;
        } elseif (mt_rand(1, 100) > 50) {
            return $admin;
        } else {
            return $super_admin;
        }

    }
}