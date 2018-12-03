<?php

namespace Hgabka\SettingsBundle\Security;

use Hgabka\KunstmaanSettingsBundle\Entity\Setting;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SettingVoter extends Voter
{
    const EDIT = 'EDIT';
    const CREATE = 'CREATE';
    const DELETE = 'DELETE';

    /** @var string */
    protected $editorRole;

    /** @var string */
    protected $creatorRole;

    /** @var AccessDecisionManagerInterface */
    protected $decisionManager;

    /**
     * BannerVoter constructor.
     *
     * @param string $editorRole
     * @param mixed  $creatorRole
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager, $editorRole, $creatorRole)
    {
        $this->decisionManager = $decisionManager;
        $this->editorRole = $editorRole;
        $this->creatorRole = $creatorRole;
    }

    protected function supports($attribute, $subject)
    {
        if (!\in_array($attribute, [self::EDIT, self::CREATE, self::DELETE], true)) {
            return false;
        }
        if (!$subject instanceof Setting) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $token);
            case self::CREATE:
                return $this->canCreate($subject, $token);
            case self::DELETE:
                return $this->canDelete($subject, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Setting $setting, TokenInterface $token)
    {
        return $this->decisionManager->decide($token, [$this->editorRole]);
    }

    private function canCreate(Setting $setting, TokenInterface $token)
    {
        return $this->decisionManager->decide($token, $this->creatorRole);
    }

    private function canDelete(Setting $setting, TokenInterface $token)
    {
        return $this->decisionManager->decide($token, $this->creatorRole);
    }
}
