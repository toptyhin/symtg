<?php

namespace App\Entity;

use App\Enum\SendStatus;
use App\Repository\TelegramSendLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: TelegramSendLogRepository::class)]
class TelegramSendLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $shop = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(enumType: SendStatus::class)]
    private ?SendStatus $status = null;

    #[ORM\Column(length: 255)]
    private ?string $error = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sent_at = null;

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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?SendStatus
    {
        return $this->status;
    }

    public function setStatus(SendStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): static
    {
        $this->error = $error;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTimeImmutable $sent_at): static
    {
        $this->sent_at = $sent_at;

        return $this;
    }
}
