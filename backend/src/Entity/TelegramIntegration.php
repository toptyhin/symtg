<?php

namespace App\Entity;

use App\Repository\TelegramIntegrationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: TelegramIntegrationRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_telegram_shop', fields: ['shop'])]

class TelegramIntegration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['telegram_integration:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Shop::class, inversedBy: 'telegramIntegration')]
    #[ORM\JoinColumn(name: 'shop_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['telegram_integration:read'])]
    private ?Shop $shop = null;

    #[ORM\Column(length: 255)]
    #[Groups(['telegram_integration:read', 'telegram_integration:write'])]
    private ?string $bot_token = null;

    #[ORM\Column(length: 255)]
    #[Groups(['telegram_integration:read', 'telegram_integration:write'])]
    private ?string $chat_id = null;

    #[ORM\Column]
    #[Groups(['telegram_integration:read', 'telegram_integration:write'])]
    private ?bool $enabled = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['telegram_integration:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['telegram_integration:read'])]
    private ?\DateTimeImmutable $updated_at = null;

    #[Groups(['telegram_integration:read'])]
    private ?\DateTimeImmutable $lastSentAt = null;

    #[Groups(['telegram_integration:read'])]
    private ?int $sent7d = null;

    #[Groups(['telegram_integration:read'])]
    private ?int $failed7d = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getBotToken(): ?string
    {
        return $this->bot_token;
    }

    public function setBotToken(string $bot_token): static
    {
        $this->bot_token = $bot_token;

        return $this;
    }

    public function getChatId(): ?string
    {
        return $this->chat_id;
    }

    public function setChatId(string $chat_id): static
    {
        $this->chat_id = $chat_id;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLastSentAt(): ?\DateTimeImmutable { 
        return $this->lastSentAt; 
    }
    
    public function setLastSentAt(?\DateTimeImmutable $v): static { 
        $this->lastSentAt = $v; return $this; 
    }

    public function getSent7d(): ?int { 
        return $this->sent7d; 
    }

    public function setSent7d(?int $v): static { 
        $this->sent7d = $v; 
        return $this; 
    }

    public function getFailed7d(): ?int { 
        return $this->failed7d; 
    }

    public function setFailed7d(?int $v): static {
         $this->failed7d = $v; 
         return $this; 
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }


    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->created_at === null) {
            $this->created_at = new \DateTimeImmutable();
        }
        if ($this->updated_at === null) {
            $this->updated_at = new \DateTimeImmutable();
        }
    }   


    
}
