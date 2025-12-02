<?php

namespace Hgabka\SettingsBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;

/**
 * Class SettingSubscriber.
 */
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::preRemove)]
class SettingListener
{
    /**
     * SettingListener constructor.
     */
    public function __construct(private readonly SettingsManager $settingsManager)
    {
    }

    public function postPersist(PostPersistEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->addSettingToCache($object);
    }

    public function preRemove(PreRemoveEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->removeFromCache($object->getName());
    }

    public function postUpdate(PostUpdateEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->addSettingToCache($object);
    }
}
