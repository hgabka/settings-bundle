<?php

namespace Hgabka\SettingsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;

/**
 * Class SettingSubscriber.
 */
class SettingSubscriber implements EventSubscriber
{
    /** @var SettingsManager */
    private $settingsManager;

    /**
     * SettingListener constructor.
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
            'preRemove',
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->addSettingToCache($object);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->removeFromCache($object->getName());
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->addSettingToCache($object);
    }
}
