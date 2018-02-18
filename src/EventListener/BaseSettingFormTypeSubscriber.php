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
     * BaseSettingFormTypeSubscriber constructor.
     *
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * Replaces all children forms of the given builder
     * with the given type and/or options
     *
     * @param FormBuilderInterface $builder
     * @param                      $options
     * @param null                 $newType
     */
    protected function replaceChildren(FormBuilderInterface $builder, $options, $newType = null)
    {
        foreach ($builder->all() as $name => $child) {
            $newOptions = array_merge($child->getOptions(), $options);
            $builder->add($name, $newType ?: get_class($child->getType()->getInnerType()), $newOptions);
        }
    }
}
