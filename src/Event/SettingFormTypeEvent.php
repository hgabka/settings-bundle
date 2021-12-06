<?php

namespace Hgabka\SettingsBundle\Event;

use Hgabka\SettingsBundle\Entity\Setting;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface;

class SettingFormTypeEvent extends Event
{
    const EVENT_FORM_ADD = 'hg_settings.form_add';

    /** @var Setting */
    protected $setting;

    /** @var FormBuilderInterface */
    protected $formBuilder;

    /**
     * SettingFormTypeEvent constructor.
     */
    public function __construct(Setting $setting, FormBuilderInterface $formBuilder)
    {
        $this->setting = $setting;
        $this->formBuilder = $formBuilder;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     *
     * @return SettingFormTypeEvent
     */
    public function setFormBuilder($formBuilder)
    {
        $this->formBuilder = $formBuilder;

        return $this;
    }
}
