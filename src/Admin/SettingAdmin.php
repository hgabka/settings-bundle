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
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingAdmin extends AbstractAdmin
{
    const CATEGORY_SESSION_KEY = 'hg_settings.category';

    /** @var null|ArrayCollection|SettingCategory[] */
    protected $categories;
    
    /** @var Security */
    protected $security;
    
    /** @var string **/
    protected $editorRole;
    
    /** @var string **/
    protected $creatorRole;

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
    
    /**
     * @param Security $security
     *
     * @return LabelAdmin
     */
    public function setSecurity(Security $security): self
    {
        $this->security = $security;

        return $this;
    }
    
    /**
     * @param string $editorRole
     *
     * @return LabelAdmin
     */
    public function setEditorRole(?string $editorRole): self
    {
        $this->editorRole = $editorRole;

        return $this;
    }
    
    /**
     * @param string $creatorRole
     *
     * @return LabelAdmin
     */
    public function setCreatorRole(?string $creatorRole): self
    {
        $this->creatorRole = $creatorRole;

        return $this;
    }


    public function postUpdate(object $object): void
    {
        $this->manager->clearCache();
    }

    public function postPersist(object $object): void
    {
        $this->manager->clearCache();
    }

    public function postRemove(object $object): void
    {
        $this->manager->clearCache();
    }

    public function getModelName()
    {
        return 'hg_settings.admin.model_name';
    }

    
    /**
     * @param array<string, array<string, mixed>> $actions
     *
     * @return array<string, array<string, mixed>>
     */
    protected function configureDashboardActions(array $actions): array
    {
        $actions = [];
        $container = $this->getConfigurationPool()->getContainer();

        if ($this->security->isGranted($this->creatorRole)) {
            $actions['create'] = [
                'label' => 'link_add',
                'translation_domain' => 'SonataAdminBundle',
                'template' => $this->getTemplate('action_create'),
                'url' => $this->generateUrl('create'),
                'icon' => 'plus-circle',
            ];
        }

        if ($this->security->isGranted($this->editorRole)) {
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

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('saveCategory', '/saveCategory');
    }
    
    protected function configureBatchActions(array $actions): array
    {
        return [];
    }


    protected function configureFormFields(FormMapper $formMapper): void
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
