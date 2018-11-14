<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181114032146 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt_item (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, receipt_it INT DEFAULT NULL, amount INT NOT NULL, INDEX IDX_89601E924584665A (product_id), INDEX IDX_89601E9236EBB8F2 (receipt_it), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE receipt_item ADD CONSTRAINT FK_89601E924584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE receipt_item ADD CONSTRAINT FK_89601E9236EBB8F2 FOREIGN KEY (receipt_it) REFERENCES receipt (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipt_item DROP FOREIGN KEY FK_89601E9236EBB8F2');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE receipt_item');
    }
}
