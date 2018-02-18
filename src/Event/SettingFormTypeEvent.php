<?php

namespace Hgabka\SettingsBundle\Event;

use Hgabka\SettingsBundle\Entity\Setting;
use Symfony\Component\EventDispatcher\Event;

class SettingFormTypeEvent extends Event
{
    const EVENT_FORM_ADD = 'hg_settings.form_add';

    /** @var Setting */
    protected $setting;

    /**
     * SettingFormTypeEvent constructor.
     *
     * @param Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @return Setting
     */
    public function getSetting(): Setting
    {
        return $this->setting;
    }
}