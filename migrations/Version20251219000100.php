<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251219000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблиц client, dish, restaurant_order, order_file и связей между ними';
    }

    public function up(Schema $schema): void
    {
        // CLIENT
        $this->addSql("
            CREATE TABLE client (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                password VARCHAR(255) NOT NULL,
                roles JSON NOT NULL,
                UNIQUE INDEX UNIQ_CLIENT_PHONE (phone),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // DISH
        $this->addSql("
            CREATE TABLE dish (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                price NUMERIC(10, 2) NOT NULL,
                category VARCHAR(100) DEFAULT NULL,
                image_path VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // RESTAURANT ORDER
        $this->addSql("
            CREATE TABLE restaurant_order (
                id INT AUTO_INCREMENT NOT NULL,
                client_id INT NOT NULL,
                total_amount NUMERIC(10, 2) NOT NULL,
                order_date DATETIME NOT NULL,
                status VARCHAR(20) NOT NULL,
                INDEX IDX_ORDER_CLIENT (client_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // ORDER ↔ DISH (ManyToMany)
        $this->addSql("
            CREATE TABLE restaurant_order_dish (
                restaurant_order_id INT NOT NULL,
                dish_id INT NOT NULL,
                INDEX IDX_ORDER_DISH_ORDER (restaurant_order_id),
                INDEX IDX_ORDER_DISH_DISH (dish_id),
                PRIMARY KEY(restaurant_order_id, dish_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // ORDER FILE
        $this->addSql("
            CREATE TABLE order_file (
                id INT AUTO_INCREMENT NOT NULL,
                restaurant_order_id INT NOT NULL,
                file_name VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                mime_type VARCHAR(50) NOT NULL,
                file_size INT NOT NULL,
                uploaded_at DATETIME NOT NULL,
                INDEX IDX_FILE_ORDER (restaurant_order_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // FOREIGN KEYS
        $this->addSql("
            ALTER TABLE restaurant_order
            ADD CONSTRAINT FK_ORDER_CLIENT
            FOREIGN KEY (client_id) REFERENCES client (id)
        ");

        $this->addSql("
            ALTER TABLE restaurant_order_dish
            ADD CONSTRAINT FK_ORDER_DISH_ORDER
            FOREIGN KEY (restaurant_order_id) REFERENCES restaurant_order (id) ON DELETE CASCADE
        ");

        $this->addSql("
            ALTER TABLE restaurant_order_dish
            ADD CONSTRAINT FK_ORDER_DISH_DISH
            FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE
        ");

        $this->addSql("
            ALTER TABLE order_file
            ADD CONSTRAINT FK_FILE_ORDER
            FOREIGN KEY (restaurant_order_id) REFERENCES restaurant_order (id) ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_file');
        $this->addSql('DROP TABLE restaurant_order_dish');
        $this->addSql('DROP TABLE restaurant_order');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE client');
    }
}
