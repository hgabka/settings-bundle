<?php

namespace Hgabka\SettingsBundle\Twig;

use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_setting', [$this, 'getSetting']),
            new TwigFunction('is_setting_visible', [$this, 'isSettingVisible']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('replace_settings', [$this, 'replaceSettings'], ['is_safe' => ['html']]),
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

    public function replaceSettings(string $target, string $prefix = '', string $postfix = ''): string
    {
        $pairs = [];
        foreach ($this->settingManager->getValues() as $name => $value) {
            if (is_scalar($value) && !is_bool($value)) {
                $pairs[$prefix.$name.$postfix] = (string)$value;
            }
        }

        return strtr($target, $pairs);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'hgabka_settingsbundle_twig_extension';
    }
}
