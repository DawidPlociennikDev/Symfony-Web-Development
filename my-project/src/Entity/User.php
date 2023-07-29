<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class User
{
    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        // $this->createdAt = new \DateTime();
        // dump($this->createdAt);
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Video::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $videos;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    private Collection $followed;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followed')]
    private Collection $following;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->followed = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setUser($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getUser() === $this) {
                $video->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowed(): Collection
    {
        return $this->followed;
    }

    public function addFollowed(self $followed): static
    {
        if (!$this->followed->contains($followed)) {
            $this->followed->add($followed);
        }

        return $this;
    }

    public function removeFollowed(self $followed): static
    {
        $this->followed->removeElement($followed);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(self $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->addFollowed($this);
        }

        return $this;
    }

    public function removeFollowing(self $following): static
    {
        if ($this->following->removeElement($following)) {
            $following->removeFollowed($this);
        }

        return $this;
    }
}
