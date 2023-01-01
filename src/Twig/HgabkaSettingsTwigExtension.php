<?php

namespace Hgabka\SettingsBundle\Twig;

use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HgabkaSettingsTwigExtension extends AbstractExtension
{
    public function __construct(protected readonly SettingsManager $settingManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_setting', $this->getSetting(...)),
            new TwigFunction('is_setting_visible', $this->isSettingVisible(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('replace_settings', $this->replaceSettings(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getSetting(string $slug, ?string $locale = null)
    {
        return $this->settingManager->get($slug, $locale);
    }

    public function isSettingVisible(Setting $setting): bool
    {
        $type = $this->settingManager->getType($setting->getType());

        return $type && $type->isVisible();
    }

    public function replaceSettings(?string $target, string $prefix = '', string $postfix = '', ?string $locale = null): ?string
    {
        if (empty($target)) {
            return $target;
        }

        return $this->settingManager->replaceSettings($target, $prefix, $postfix, null, $locale);
    }
}
