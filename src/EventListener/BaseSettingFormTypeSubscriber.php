<?php

namespace Hgabka\SettingsBundle\EventListener;

use Hgabka\SettingsBundle\Event\SettingFormTypeEvent;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Class BaseSettingFormTypeSubscriber.
 */
abstract class BaseSettingFormTypeSubscriber implements EventSubscriberInterface
{
    /** @var SettingsManager */
    protected $settingsManager;

    /**
     * BaseSettingFormTypeSubscriber constructor.
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * @param SettingFormTypeEvent $event
     * @param string               $name
     * @param array                $options
     * @param null|string          $newType
     */
    protected function replaceChild(SettingFormTypeEvent $event, string $name, array $options, string $newType = null)
    {
        $builder = $event->getFormBuilder();
        if (!empty($options) && is_array($options) && $builder && $builder->has($name)) {
            $newOptions = array_merge($child->getOptions(), $options);
            $builder->add($name, $newType ?: get_class($child->getType()->getInnerType()), $newOptions);
        }
    }

    /**
     * @param SettingFormTypeEvent $event
     * @param array                $options
     * @param null|string          $newType
     */
    protected function replaceChildren(SettingFormTypeEvent $event, array $options, string $newType = null)
    {
        $builder = $event->getFormBuilder();
        if (!empty($options) && is_array($options) && $builder) {
            foreach ($builder->all() as $name => $child) {
                $this->replaceChild($event, $name, $options, $newType);
            }
        }
    }

    /**
     * @param SettingFormTypeEvent $event
     * @param string               $name
     * @param array|Constraint     $constraints
     */
    protected function addConstraintsToChild(SettingFormTypeEvent $event, string $name, $constraints)
    {
        $builder = $event->getFormBuilder();
        if (!empty($options) && is_array($options) && $builder && $builder->has($name)) {
            $child = $builder->get('name');
            $options = $child->getOptions();
            $this->settingsManager->addConstraints($options, $constraints);
            $builder->add($name, get_class($child->getType()->getInnerType()), $options);
        }
    }

    /**
     * @param SettingFormTypeEvent $event
     * @param array|Constraint     $constraints
     */
    protected function addConstraints(SettingFormTypeEvent $event, $constraints)
    {
        $builder = $event->getFormBuilder();
        if (!empty($options) && is_array($options) && $builder) {
            foreach ($builder->all() as $name => $child) {
                $this->addConstraintsToChild($event, $name, $constraints);
            }
        }
    }
}
