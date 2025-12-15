<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251214115951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(255) NOT NULL, total NUMERIC(10, 0) NOT NULL, customer_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, shop_id INT NOT NULL, INDEX IDX_F52993984D16C4DD (shop_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE telegram_integration (id INT AUTO_INCREMENT NOT NULL, bot_token VARCHAR(255) NOT NULL, chat_id VARCHAR(255) NOT NULL, enabled TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, shop_id INT NOT NULL, UNIQUE INDEX uniq_telegram_shop (shop_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE telegram_send_log (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, error VARCHAR(255) NOT NULL, sent_at DATETIME NOT NULL, shop_id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_77AA2A944D16C4DD (shop_id), INDEX IDX_77AA2A948D9F6D38 (order_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, code LONGTEXT NOT NULL, shop_id INT NOT NULL, conn_id_id INT NOT NULL, INDEX IDX_8D93D649D6CF29 (conn_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE telegram_integration ADD CONSTRAINT FK_4D6BE5084D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE telegram_send_log ADD CONSTRAINT FK_77AA2A944D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE telegram_send_log ADD CONSTRAINT FK_77AA2A948D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D6CF29 FOREIGN KEY (conn_id_id) REFERENCES shop (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984D16C4DD');
        $this->addSql('ALTER TABLE telegram_integration DROP FOREIGN KEY FK_4D6BE5084D16C4DD');
        $this->addSql('ALTER TABLE telegram_send_log DROP FOREIGN KEY FK_77AA2A944D16C4DD');
        $this->addSql('ALTER TABLE telegram_send_log DROP FOREIGN KEY FK_77AA2A948D9F6D38');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D6CF29');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE telegram_integration');
        $this->addSql('DROP TABLE telegram_send_log');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
