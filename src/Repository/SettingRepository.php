<?php

namespace Hgabka\SettingsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * SettingRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SettingRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getSettings()
    {
        return $this
            ->createQueryBuilder('s')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY)
        ;
    }

    /**
     * @return array
     */
    public function getVisibleSettings()
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.visible = 1')
            ->getQuery()
            ->getResult()
        ;
    }
}
