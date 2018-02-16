<?php

namespace Hgabka\SettingsBundle\Twig;

use Hgabka\SettingsBundle\Helper\SettingsManager;

class HgabkaSettingsTwigExtension extends \Twig_Extension
{
    /**
     * @var SettingsManager
     */
    protected $settingManager;
    /**
     * PublicTwigExtension constructor.
     *
     * @param SettingsManager $settingsManager
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
            new \Twig_SimpleFunction('get_setting', [$this, 'getSetting']),
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
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'hgabka_settingsbundle_twig_extension';
    }
}