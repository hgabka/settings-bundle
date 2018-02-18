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
    /** @var SettingsManager $settingsManager */
    private $settingsManager;

    /**
     * SettingListener constructor.
     *
     * @param SettingsManager $settingsManager
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

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->addSettingToCache($object);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->removeFromCache($object->getName());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if (!$object instanceof Setting) {
            return;
        }
        $this->settingsManager->addSettingToCache($object);
    }
}
