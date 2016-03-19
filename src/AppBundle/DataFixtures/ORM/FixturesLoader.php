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
                __DIR__.'/test/category.yml',
                __DIR__.'/test/product.yml',
            ];
        }
        return [
            __DIR__.'/dev/attribute.yml',
            __DIR__.'/dev/product.yml',
            $this->categories(),
        ];
    }

    protected function categories()
    {
        return [
            'AppBundle\Entity\Category' => [
                'category1' => [
                    'title'         => 'Побутова техніка',
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category2' => [
                    'title'         => 'Техніка для кухні',
                    'parent'        => '@category1',
                    'attributes'    => '3x @attribute*',
                    'products'      => $this->dataMap('product', 1, 10),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category3' => [
                    'title'         => 'Кліматична техніка',
                    'parent'        => '@category1',
                    'attributes'    => '3x @attribute*',
                    'products'      => $this->dataMap('product', 11, 20),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category4' => [
                    'title'         => 'Інструменти',
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category5' => [
                    'title'         => 'Електроінструмент',
                    'parent'        => '@category4',
                    'attributes'    => '3x @attribute*',
                    'products'      => $this->dataMap('product', 21, 30),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category6' => [
                    'title'         => 'Ручний інструмент',
                    'parent'        => '@category4',
                    'attributes'    => '3x @attribute*',
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
                    'products'      => $this->dataMap('product', 41, 50),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category9' => [
                    'title'         => "Комп'ютери",
                    'attributes'    => '3x @attribute*',
                    'products'      => $this->dataMap('product', 51, 60),
                    'createdAt'     => '<dateTimeBetween(\'-1 years\', \'now\')>',
                ],
                'category10' => [
                    'title'         => 'Муз. інструмент',
                    'attributes'    => '3x @attribute*',
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
}