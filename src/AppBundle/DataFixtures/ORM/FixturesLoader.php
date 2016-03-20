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
            __DIR__.'/dev/product.yml',
            __DIR__.'/dev/user.yml',
            __DIR__.'/dev/private_message.yml',
            $this->categories(),
        ];
    }

    protected function categories()
    {
        return [
            'AppBundle\Entity\Category' => [
                'category1' => [
                    'title'         => 'Побутова техніка',
                    'hasChildren'   => '1',
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category2' => [
                    'title'         => 'Техніка для кухні',
                    'parent'        => '@category1',
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      => $this->dataMap('product', 1, 10),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category3' => [
                    'title'         => 'Кліматична техніка',
                    'parent'        => '@category1',
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      => $this->dataMap('product', 11, 20),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category4' => [
                    'title'         => 'Інструменти',
                    'hasChildren'   => '1',
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category5' => [
                    'title'         => 'Електроінструмент',
                    'parent'        => '@category4',
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      => $this->dataMap('product', 21, 30),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category6' => [
                    'title'         => 'Ручний інструмент',
                    'parent'        => '@category4',
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      => $this->dataMap('product', 31, 40),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category7' => [
                    'title'         => 'Вимірювальний інструмент',
                    'parent'        => '@category4',
                    'attributes'    => '3x @attribute*',
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category8' => [
                    'title'         => 'Книги',
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      => $this->dataMap('product', 41, 50),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category9' => [
                    'title'         => "Комп'ютери",
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      => $this->dataMap('product', 51, 60),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category10' => [
                    'title'         => 'Муз. інструмент',
                    'attributes'    => '3x @attribute*',
                    'hasProducts'   => '1',
                    'products'      =>  $this->dataMap('product', 61, 70),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
            ],
        ];
    }

    protected function dataMap($entity, $start, $stop) {
        return array_map(
            function($n, $entity) {
                return sprintf('@'.$entity.'%d', $n);
            },
            range($start, $stop),
            array_fill_keys(range($start, $stop), $entity)
        );
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