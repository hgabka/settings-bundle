<?php

namespace Hgabka\SettingsBundle\Form;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Event\SettingFormTypeEvent;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SettingsType.
 */
class SettingsType extends AbstractType
{
    /**
     * @var array|Setting[]
     */
    private $settings;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var array
     */
    private $types;

    /** @var SettingsManager */
    private $manager;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var HgabkaUtils */
    private $hgabkaUtils;

    /**
     * SettingsType constructor.
     */
    public function __construct(SettingsManager $settingsManager, Registry $entityManager, EventDispatcherInterface $dispatcher, HgabkaUtils $hgabkaUtils)
    {
        $this->hgabkaUtils = $hgabkaUtils;
        $this->settings = $entityManager->getRepository(Setting::class)->getSettingsOrdered($this->hgabkaUtils->getCurrentLocale());
        $this->locales = $settingsManager->getLocales();
        $this->types = $settingsManager->getTypes();
        $this->manager = $settingsManager;
        $this->dispatcher = $dispatcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($this->settings as $setting) {
            $type = $this->manager->getType($setting->getType());
            $oneForm = $builder->create($setting->getId(), FormType::class, ['label' => false]);

            $event = new SettingFormTypeEvent($setting, $oneForm);
            $this->dispatcher->dispatch($event, SettingFormTypeEvent::EVENT_FORM_ADD);

            if (empty($oneForm)) {
                continue;
            }

            $builder->add($oneForm);
        }
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix(): string
    {
        return 'settings';
    }
}
