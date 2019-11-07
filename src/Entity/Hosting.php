<?php
/**
 * Hosting.php
 *
 * Hosting Entity
 *
 * @category   Entity
 */

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Hosting
 *
 * @ORM\Table(name="hosting")
 * @ORM\Entity(repositoryClass="App\Repository\HostingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Hosting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="hostings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Serializer\Exclude()
     */
    protected $user;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
 
    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cores;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $memory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $disc;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
 
    /**
     * @param mixed $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
 
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
 
    /**
     * @param mixed $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
 
        return $this;
    }
 
    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
 
    /**
     * @param mixed $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
 
        return $this;
    }
 
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $dateTimeNow = new DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    public function getCores(): ?string
    {
        return $this->cores;
    }

    public function setCores(?string $cores): self
    {
        $this->cores = $cores;

        return $this;
    }

    public function getMemory(): ?string
    {
        return $this->memory;
    }

    public function setMemory(?string $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getDisc(): ?string
    {
        return $this->disc;
    }

    public function setDisc(?string $disc): self
    {
        $this->disc = $disc;

        return $this;
    }
}
