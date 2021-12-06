<?php

namespace Hgabka\SettingsBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Hgabka\SettingsBundle\Entity\SettingCategory;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingAdmin extends AbstractAdmin
{
    const CATEGORY_SESSION_KEY = 'hg_settings.category';

    /** @var null|ArrayCollection|SettingCategory[] */
    protected $categories;

    /** @var SettingsManager */
    private $manager;

    /** @var ManagerRegistry */
    private $doctrine;

    public function setManager(SettingsManager $manager)
    {
        $this->manager = $manager;
    }

    public function setDoctrine(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->end();
    }

    public function postUpdate($object)
    {
        $this->manager->clearCache();
    }

    public function postPersist($object)
    {
        $this->manager->clearCache();
    }

    public function postRemove($object)
    {
        $this->manager->clearCache();
    }

    public function getModelName()
    {
        return 'hg_settings.admin.model_name';
    }

    /**
     * Get the list of actions that can be accessed directly from the dashboard.
     *
     * @return array
     */
    public function getDashboardActions()
    {
        $actions = [];
        $container = $this->getConfigurationPool()->getContainer();
        $authChecker = $container->get('security.authorization_checker');

        if ($authChecker->isGranted($container->getParameter('hg_settings.creator_role'))) {
            $actions['create'] = [
                'label' => 'link_add',
                'translation_domain' => 'SonataAdminBundle',
                'template' => $this->getTemplate('action_create'),
                'url' => $this->generateUrl('create'),
                'icon' => 'plus-circle',
            ];
        }

        if ($authChecker->isGranted($container->getParameter('hg_settings.editor_role'))) {
            $actions['list'] = [
                'label' => 'link_list',
                'translation_domain' => 'SonataAdminBundle',
                'url' => $this->generateUrl('list'),
                'icon' => 'list',
            ];
        }

        return $actions;
    }

    public function getCategories()
    {
        if (null === $this->categories) {
            $this->categories =
                $this
                    ->doctrine
                    ->getRepository(SettingCategory::class)
                    ->createQueryBuilder('c')
                    ->orderBy('c.position')
                    ->getQuery()
                    ->getResult()
            ;
        }

        return $this->categories;
    }

    public function useCategories()
    {
        return \count($this->getCategories()) > 0;
    }

    public function getActiveCategoryId()
    {
        if (!$this->useCategories()) {
            return null;
        }

        $key = static::CATEGORY_SESSION_KEY;

        $session = $this->getRequest()->getSession();

        return $session->get($key, null);
    }

    public function setCategoryId($categoryId)
    {
        $key = static::CATEGORY_SESSION_KEY;

        $session = $this->getRequest()->getSession();

        if ('all' === $categoryId || null === $categoryId) {
            $session->remove($key);
        } else {
            $session->set($key, $categoryId);
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('saveCategory', '/saveCategory');
        $collection->remove('batch');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'hg_settings.label.name'])
        ;

        if ($this->useCategories()) {
            $formMapper
                ->add('category', EntityType::class, [
                    'label' => 'hg_settings.label.category',
                    'placeholder' => '',
                    'required' => true,
                    'constraints' => new NotBlank(),
                    'class' => SettingCategory::class,
                    'choice_label' => function ($category) {
                        return $category->getName($this->getRequest()->getLocale());
                    },
                ])
            ;
        }
        $formMapper
            ->add('type', ChoiceType::class, [
                'label' => 'hg_settings.label.type',
                'choices' => $this->manager->getTypeChoices(),
            ])
            ->add('culture_aware', CheckboxType::class, ['required' => false, 'label' => 'hg_settings.label.culture_aware'])
            ->add('required', CheckboxType::class, [
                'required' => false,
                'label' => 'hg_settings.label.required',
            ])
            ->add('editable', CheckboxType::class, [
                'required' => false,
                'label' => 'hg_settings.label.editable',
            ])
            ->add('visible', CheckboxType::class, [
                'required' => false,
                'label' => 'hg_settings.label.visible',
            ])
            ->add('translations', TranslationsType::class, [
                'label' => false,
                'locales' => $this->manager->getLocales(),
                'required' => false,
                'fields' => [
                    'description' => [
                        'label' => 'hg_settings.label.description',
                        'required' => false,
                        'field_type' => TextareaType::class,
                    ],
                    'value' => [
                        'field_type' => HiddenType::class,
                        'required' => false,
                    ],
                ],
            ])
        ;
    }
}
