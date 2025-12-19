<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Dish;
use App\Entity\RestaurantOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RestaurantFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Клиенты
        $clients = [];
        $clientsData = [
            ['name' => 'Василий Кузьма', 'phone' => '+7-933-333-34-34'],
            ['name' => 'Джозеф Шульц', 'phone' => '+7-922-222-89-89'],
            ['name' => 'Исаак Карпов', 'phone' => '+7-911-111-42-42'],
        ];

        foreach ($clientsData as $data) {
            $client = new Client();
            $client->setName($data['name'])->setPhone($data['phone']);
            $manager->persist($client);
            $clients[] = $client;
        }

        // Блюда
        $dishes = [];
        $dishesData = [
            ['name' => 'Блины', 'price' => '42.00', 'category' => 'Выпечка'],
            ['name' => 'Кофе', 'price' => '100.00', 'category' => 'Напитки'],
            ['name' => 'Медовик', 'price' => '420.00', 'category' => 'Десерты'],
            ['name' => 'Овсянка', 'price' => '340.00', 'category' => 'Каши'],
            ['name' => 'Оливье', 'price' => '322.00', 'category' => 'Салаты'],
        ];

        foreach ($dishesData as $data) {
            $dish = new Dish();
            $dish->setName($data['name'])
                ->setPrice($data['price'])
                ->setCategory($data['category']);
            $manager->persist($dish);
            $dishes[] = $dish;
        }

        // Заказы
        $order1 = new RestaurantOrder();
        $order1->setClient($clients[0])
            ->addDish($dishes[0])
            ->addDish($dishes[3]);
        $order1->calculateTotal();
        $manager->persist($order1);

        $manager->flush();
    }
}