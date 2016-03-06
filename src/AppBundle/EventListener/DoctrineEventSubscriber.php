<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\ProductPicture;
use AppBundle\Services\MediaHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineEventSubscriber implements EventSubscriber
{

    protected $mediaHandler;

    public function __construct(MediaHandler $mediaHandler)
    {
        $this->mediaHandler = $mediaHandler;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
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

}