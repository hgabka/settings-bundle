<?php

namespace Hgabka\SettingsBundle\Tests\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Hgabka\SettingsBundle\Entity\Setting;
use Hgabka\SettingsBundle\EventListener\SettingListener;
use Hgabka\SettingsBundle\Helper\SettingsManager;
use PHPUnit\Framework\TestCase;

class SettingListenerTest extends TestCase
{
    private function em(): EntityManagerInterface
    {
        return $this->createStub(EntityManagerInterface::class);
    }

    public function testPersistAndUpdateRefreshTheCache(): void
    {
        $setting = (new Setting())->setName('a');
        $manager = $this->createMock(SettingsManager::class);
        $manager->expects($this->exactly(2))->method('addSettingToCache')->with($setting);

        $listener = new SettingListener($manager);
        $listener->postPersist(new PostPersistEventArgs($setting, $this->em()));
        $listener->postUpdate(new PostUpdateEventArgs($setting, $this->em()));
    }

    public function testRemoveDropsTheCacheEntryByName(): void
    {
        $manager = $this->createMock(SettingsManager::class);
        $manager->expects($this->once())->method('removeFromCache')->with('a');

        (new SettingListener($manager))->preRemove(new PreRemoveEventArgs((new Setting())->setName('a'), $this->em()));
    }

    public function testOtherEntitiesAreIgnored(): void
    {
        $manager = $this->createMock(SettingsManager::class);
        $manager->expects($this->never())->method('addSettingToCache');
        $manager->expects($this->never())->method('removeFromCache');

        $listener = new SettingListener($manager);
        $listener->postPersist(new PostPersistEventArgs(new \stdClass(), $this->em()));
        $listener->postUpdate(new PostUpdateEventArgs(new \stdClass(), $this->em()));
        $listener->preRemove(new PreRemoveEventArgs(new \stdClass(), $this->em()));
    }
}
