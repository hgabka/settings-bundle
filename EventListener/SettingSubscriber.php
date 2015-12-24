<?php

// src/Acme/SearchBundle/EventListener/SearchIndexer.php
namespace HG\SettingsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use HG\SettingsBundle\Entity\Setting;

class SettingSubscriber implements EventSubscriber
{

  private $container;
  
  public function __construct($container)
  {
    $this->container = $container;
  }

  public function getSubscribedEvents()
  {
    $events = array('prePersist', 'preUpdate');
    
    if (true === $this->container->getParameter('hg_settings.auto_delete_files'))
    {
      $events[] = 'preRemove';
    }
    
    return $events;
  }
  
  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    
    if (!$entity instanceof Setting)
    {
      return;
    } 
    
    $settingManager = $this->container->get('hg_settings.manager');
    $settingManager->clearCache();
  }
  
  public function preUpdate(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    
    if (!$entity instanceof Setting)
    {
      return;
    } 
    
    $settingManager = $this->container->get('hg_settings.manager');
    $settingManager->clearCache();
  }
  
  public function preRemove(LifecycleEventArgs $args)
  {
    $fileManager = $this->container->get('hg_file_repository.filemanager');
    $settingManager = $this->container->get('hg_settings.manager');
    
    $em = $args->getEntityManager();
    $entity = $args->getEntity();
    
    if (!$entity instanceof Setting)
    {
      return;
    } 
    
    $settingManager->clearCache();
    
    if (!$entity->isFileType())
    {
      return;
    }
    
    if (!$entity->getCultureAware())
    {
      $fileManager->delete($entity->getGeneralValue(), false);
    }
    else
    {
      foreach ($settingManager->getLocales() as $locale)
      {
        $fileManager->delete($entity->getValue($locale), false);
      }
    }
  }
}