<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220528085031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_BE2DDF8C12469DE2 ON produits (category_id)');
        $this->addSql('ALTER TABLE users DROP createdAt, DROP updatedAt');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C12469DE2');
        $this->addSql('DROP INDEX IDX_BE2DDF8C12469DE2 ON produits');
        $this->addSql('ALTER TABLE produits DROP category_id');
        $this->addSql('ALTER TABLE users ADD createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
