<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220128170706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', ended TINYINT(1) NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', category_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, last_bought_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_description (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', value VARCHAR(255) NOT NULL, language_value VARCHAR(255) NOT NULL, INDEX IDX_C1CBDE394584665A (product_id), UNIQUE INDEX locale_description_constraint (product_id, value, language_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_detail (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, language_value VARCHAR(255) NOT NULL, INDEX IDX_4C7A3E374584665A (product_id), UNIQUE INDEX product_detail_locale (product_id, language_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_name (id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', product_id BINARY(16) NOT NULL COMMENT \'(DC2Type:ulid)\', value VARCHAR(255) NOT NULL, language_value VARCHAR(255) NOT NULL, INDEX IDX_D3CB5CA74584665A (product_id), UNIQUE INDEX locale_name_constraint (product_id, value, language_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_description ADD CONSTRAINT FK_C1CBDE394584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_detail ADD CONSTRAINT FK_4C7A3E374584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_name ADD CONSTRAINT FK_D3CB5CA74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_description DROP FOREIGN KEY FK_C1CBDE394584665A');
        $this->addSql('ALTER TABLE product_detail DROP FOREIGN KEY FK_4C7A3E374584665A');
        $this->addSql('ALTER TABLE product_name DROP FOREIGN KEY FK_D3CB5CA74584665A');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_description');
        $this->addSql('DROP TABLE product_detail');
        $this->addSql('DROP TABLE product_name');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
