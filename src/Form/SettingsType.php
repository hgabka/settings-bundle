<?php

namespace Hgabka\SettingsBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Event\SettingFormTypeEvent;
use Hgabka\SettingsBundle\Helper\SettingsManager;
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

    /** @var SettingsManager $manager */
    private $manager;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    /**
     * SettingsType constructor.
     *
     * @param SettingsManager          $settingsManager
     * @param ManagerRegistry          $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SettingsManager $settingsManager, ManagerRegistry $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->settings = $entityManager->getRepository(Setting::class)->findAll();
        $this->locales = $settingsManager->getLocales();
        $this->types = $settingsManager->getTypes();
        $this->manager = $settingsManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settings as $setting) {
            $type = $this->manager->getType($setting->getType());
            $oneForm = $builder->create($setting->getId(), FormType::class, ['label' => false]);

            $event = new SettingFormTypeEvent($setting, $oneForm);
            $this->dispatcher->dispatch(SettingFormTypeEvent::EVENT_FORM_ADD, $event);

            if (empty($oneForm)) {
                continue;
            }

            $builder->add($oneForm);
        }
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'settings';
    }
}
