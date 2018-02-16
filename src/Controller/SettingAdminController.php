<?php

namespace Hgabka\SettingsBundle\Controller;

use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\Form\SettingsType;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SettingAdminController extends Controller
{
  public function listAction(Request $request = null)
  {
    if (!$this->admin->isGranted($this->getParameter('hg_settings.editor_role')))
    {
      throw new AccessDeniedException();
    }

    $form = $this->createForm(SettingsType::class);

    $form->handleRequest($request);

    if ($form->isValid())
    {
        foreach ($form->getData() as $settingId => $values)
        {
          $setting = $this->getDoctrine()->getManager()->getRepository(Setting::class)->findOneBy(array('id' => $settingId));

          if (!$setting->getId())
          {
            continue;
          }

          if (!$setting->getCultureAware())
          {
            if (isset($values['general_value']))
            {
              $setting->setSettingValue($values['general_value']);
            }
          }
          else
          {
            $setting->setValuesByCultures($values);
          }

          $em = $this->getDoctrine()->getManager();
          $setting->mergeNewTranslations();

          $em->persist($setting);
          $em->flush();

        }

        $this->get('hg_settings.manager')->clearCache();
        $this->addFlash('sonata_flash_success', $this->get('translator')->trans('hg_settings_settings_saved'));

        return $this->redirect($this->generateUrl('admin_hg_settings_setting_list'));
    }


    return $this->render('HgabkaSettingsBundle:SettingAdmin:list.html.twig', array('settings' => $this->getDoctrine()->getRepository(Setting::class)->findAll(), 'form' => $form->createView(), 'action' => 'list', 'creator' => $this->admin->isGranted($this->getParameter('hg_settings.creator_role'))));
  }

}