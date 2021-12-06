<?php

namespace Hgabka\SettingsBundle\Twig;

use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HgabkaSettingsTwigExtension extends AbstractExtension
{
    /**
     * @var SettingsManager
     */
    protected $settingManager;

    /**
     * PublicTwigExtension constructor.
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingManager = $settingsManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_setting', [$this, 'getSetting']),
            new TwigFunction('is_setting_visible', [$this, 'isSettingVisible']),
        ];
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getSetting($slug)
    {
        return $this->settingManager->get($slug);
    }

    public function isSettingVisible(Setting $setting)
    {
        $type = $this->settingManager->getType($setting->getType());

        return $type && $type->isVisible();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'hgabka_settingsbundle_twig_extension';
    }
}
