<?php

namespace Hgabka\SettingsBundle\Controller;

use Hgabka\SettingsBundle\Admin\SettingAdmin;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Form\SettingsType;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SettingAdminController extends Controller
{
    /** @var SettingsManager * */
    protected $settingsManager;

    /** @var HgabkaUtils */
    protected $hgabkaUtils;

    public function __construct(SettingsManager $settingsManager, HgabkaUtils $hgabkaUtils)
    {
        $this->settingsManager = $settingsManager;
        $this->hgabkaUtils = $hgabkaUtils;
    }

    public function listAction(Request $request): Response
    {
        if (!$this->isGranted($this->getParameter('hg_settings.editor_role'))) {
            throw new AccessDeniedException();
        }
        $manager = $this->settingsManager;

        $form = $this->createForm(SettingsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                foreach ($form->getData() as $settingId => $values) {
                    $setting = $this->getDoctrine()->getManager()->getRepository(Setting::class)->findOneBy(['id' => $settingId]);

                    if (!$setting->getId()) {
                        continue;
                    }
                    $type = $manager->getType($setting->getType());

                    if (!$setting->isCultureAware()) {
                        if (isset($values['general_value'])) {
                            $setting->setGeneralValue($type->transformValue($values['general_value']));
                        } else {
                            $setting->setGeneralValue(null);
                        }
                    } else {
                        $manager->setValuesByCultures($setting, $values);
                    }

                    $em->persist($setting);
                }
                $em->flush();
                $manager->clearCache();
                $this->addFlash('sonata_flash_success', $this->get('translator')->trans('hg_settings.message.settings_saved'));

                return $this->redirectToList();
            }
            $this->addFlash('sonata_flash_error', $this->get('translator')->trans('hg_settings.message.settings_save_failed'));
        }

        $repo = $this->getDoctrine()->getRepository(Setting::class);
        $creator = $this->isGranted($this->getParameter('hg_settings.creator_role'));
        $settings = $creator ? $repo->getSettingsOrdered($this->hgabkaUtils->getCurrentLocale()) : $repo->getVisibleSettings($this->hgabkaUtils->getCurrentLocale());

        return
            $this->renderWithExtraParams('@HgabkaSettings/SettingAdmin/list.html.twig', [
                'settings' => $settings,
                'form' => $form->createView(),
                'action' => 'list',
                'creator' => $creator,
            ]);
    }

    public function saveCategoryAction(Request $request): Response
    {
        if ($this->admin->hasAccess('list')) {
            $session = $request->getSession();

            $key = SettingAdmin::CATEGORY_SESSION_KEY;

            $queryCategory = $request->query->get('category');
            $this->admin->setCategoryId($queryCategory);
        }

        return new Response();
    }
}
