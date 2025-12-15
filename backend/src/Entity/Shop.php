<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\OrderCreateProcessor;
use App\State\TelegramConnectProcessor;
use App\State\TelegramStatusProvider;
use App\Entity\Order;
use App\Entity\TelegramIntegration;
use Symfony\Component\Serializer\Attribute\Groups;



#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['shop:read']],
    denormalizationContext: ['groups' => ['shop:write']],
    operations: [
        new Get(),
        new Get(
            uriTemplate: '/shops/{shopId}/telegram/status',
            uriVariables: ['shopId' => 'id'],
            description: 'Получает статус подключения Telegram бота для указанного магазина.',
            provider: TelegramStatusProvider::class,
            input: TelegramIntegration::class,
            output: TelegramIntegration::class,
            normalizationContext: ['groups' => ['telegram_integration:read']],
            name: 'telegram_status'
        ),

        new Post(
            uriTemplate: '/shops/{shopId}/telegram/connect',
            description: 'Подключает Telegram бота к магазину. Требует botToken и chatId в теле запроса.',
            uriVariables: ['shopId' => 'id'],
            input: TelegramIntegration::class,
            output: TelegramIntegration::class,
            normalizationContext: ['groups' => ['telegram_integration:read']],
            denormalizationContext: ['groups' => ['telegram_integration:write']],
            provider: null,
            processor: TelegramConnectProcessor::class,
            name: 'telegram_connect'
        ),

        new Post(
            uriTemplate: '/shops/{shopId}/orders',
            description: 'Создает новый заказ для указанного магазина.',
            uriVariables: ['shopId' => 'id'],
            input: Order::class,
            output: Order::class,
            normalizationContext: ['groups' => ['order:read']],
            denormalizationContext: ['groups' => ['order:write']],
            provider: null,
            processor: OrderCreateProcessor::class,
            name: 'create_order',
        ),
     
    ],
)]

class Shop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['shop:read', 'order:read', 'telegram_integration:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['shop:read', 'shop:write', 'order:read', 'telegram_integration:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(
        mappedBy: 'shop',          
        targetEntity: Order::class 
    )]
    #[Groups(['shop:read'])]
    private Collection $orders;

    #[ORM\OneToOne(
        mappedBy: 'shop',          
        targetEntity: TelegramIntegration::class 
    )]
    #[Groups(['shop:read'])]
    private ?TelegramIntegration $telegramIntegration = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setShop($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getShop() === $this) {
                $order->setShop(null);
            }
        }

        return $this;
    }    

    public function getTelegramIntegration(): ?TelegramIntegration
    {
        return $this->telegramIntegration;
    }

    public function setTelegramIntegration(?TelegramIntegration $telegramIntegration): static
    {
        // unset the owning side of the relation if necessary
        if ($telegramIntegration === null && $this->telegramIntegration !== null) {
            $this->telegramIntegration->setShop(null);
        }

        // set the owning side of the relation if necessary
        if ($telegramIntegration !== null && $telegramIntegration->getShop() !== $this) {
            $telegramIntegration->setShop($this);
        }

        $this->telegramIntegration = $telegramIntegration;

        return $this;
    }


}
