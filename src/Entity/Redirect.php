<?php

namespace Hgabka\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\SettingsBundle\Repository\RedirectRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: RedirectRepository::class)]
#[ORM\Table(name: 'hg_settings_redirect')]
class Redirect
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'domain', type: 'string', nullable: true)]
    protected ?string $domain = null;

    #[ORM\Column(name: 'origin', type: 'string', nullable: true)]
    #[Assert\NotBlank]
    protected ?string $origin = null;

    #[ORM\Column(name: 'target', type: 'string', nullable: true)]
    #[Assert\NotBlank]
    protected ?string $target = null;

    #[ORM\Column(name: 'permanent', type: 'boolean', nullable: true)]
    protected bool $permanent = true;

    public function __toString()
    {
        return 'Átirányítás';
    }

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param null|string $domain
     *
     * @return Redirect
     */
    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * @param null|string $origin
     *
     * @return Redirect
     */
    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param null|string $target
     *
     * @return Redirect
     */
    public function setTarget(?string $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermanent(): bool
    {
        return $this->permanent;
    }

    /**
     * @param bool $permanent
     *
     * @return Redirect
     */
    public function setPermanent(bool $permanent): self
    {
        $this->permanent = $permanent;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getOrigin() === $this->getTarget()) {
            $context->buildViolation('errors.redirect.origin_same_as_target')
                    ->atPath('target')
                    ->addViolation();
        }
    }
}
