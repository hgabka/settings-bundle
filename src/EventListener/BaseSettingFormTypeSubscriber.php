<?php

namespace Hgabka\SettingsBundle\EventListener;

use Hgabka\SettingsBundle\Helper\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class BaseSettingFormTypeSubscriber implements EventSubscriberInterface
{
    /** @var SettingsManager */
    protected $settingsManager;

    /**
     * SpecialSettingFormTypeSubscriber constructor.
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    protected function addOptions(FormBuilderInterface $builder, $options)
    {
        foreach ($builder->all() as $name => $child) {
            $newOptions = array_merge($child->getOptions(), $options);
            $builder->add($name, get_class($child->getType()->getInnerType()), $newOptions);
        }
    }
}
