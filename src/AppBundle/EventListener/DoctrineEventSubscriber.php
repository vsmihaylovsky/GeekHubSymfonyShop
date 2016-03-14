<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Category;
use AppBundle\Entity\ProductPicture;
use AppBundle\Services\MediaHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineEventSubscriber implements EventSubscriber
{
    protected $doctrine;

    protected $mediaHandler;

    public function __construct(RegistryInterface $doctrine,
                                MediaHandler $mediaHandler)
    {
        $this->doctrine = $doctrine;
        $this->mediaHandler = $mediaHandler;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postPersist',
            'postUpdate',
            'postRemove'
        );
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof ProductPicture) {
            if($entity->getFile()){
                $entity->setPath(
                    $this->mediaHandler->fileUpload(
                        $entity->getFile(),
                        $entity->getUploadDir()
                    )
                );
            }
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
    }
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Category) {
            $parentCategory = $entity->getParent();
            isset($parentCategory) ? $this->updateHasChildren($parentCategory) : '';
            $parentCategoryTemp = $entity->getParentTemp();
            isset($parentCategoryTemp) ? $this->updateHasChildren($parentCategoryTemp) : '';
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Category) {
            $parentCategory = $entity->getParent();
            isset($parentCategory) ? $this->updateHasChildren($parentCategory) : '';
            $parentCategoryTemp = $entity->getParentTemp();
            isset($parentCategoryTemp) ? $this->updateHasChildren($parentCategoryTemp) : '';
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof ProductPicture) {

            $this->mediaHandler->clearCache(
                $entity->getPath(),
                $entity->getUploadDir()
            );
        }
    }

    protected function updateHasChildren($parentCategory)
    {
        $children = $parentCategory->getChildren()->isEmpty() ? null : 1;
        $parentCategory->setHasChildren($children);
        $em = $this->doctrine->getManager();
        $em->persist($parentCategory);
        $em->flush();
    }
}