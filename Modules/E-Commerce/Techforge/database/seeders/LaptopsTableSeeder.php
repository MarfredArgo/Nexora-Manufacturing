<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LaptopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Modules\Ecommerce\Models\Laptop::truncate();

        $laptops = [
            [
                'name' => 'ROG Strix G16',
                'brand' => 'ASUS',
                'processor' => 'Intel Core i7-13650HX',
                'gpu' => 'NVIDIA GeForce RTX 4060',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '16" QHD+ 240Hz',
                'price' => 74999.00,
                'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Alienware m18',
                'brand' => 'Dell',
                'processor' => 'Intel Core i9-13980HX',
                'gpu' => 'NVIDIA GeForce RTX 4090',
                'ram' => '32GB DDR5',
                'storage' => '2TB NVMe SSD',
                'display' => '18" QHD+ 165Hz',
                'price' => 189999.00,
                'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Legion Pro 7i',
                'brand' => 'Lenovo',
                'processor' => 'Intel Core i9-13900HX',
                'gpu' => 'NVIDIA GeForce RTX 4080',
                'ram' => '32GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '16" WQXGA 240Hz',
                'price' => 144999.00,
                'image_url' => 'https://images.unsplash.com/photo-1593642702821-c823b13eb295?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Zephyrus G14',
                'brand' => 'ASUS',
                'processor' => 'AMD Ryzen 9 7940HS',
                'gpu' => 'NVIDIA GeForce RTX 4070',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '14" QHD+ 165Hz',
                'price' => 95999.00,
                'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Razer Blade 16',
                'brand' => 'Razer',
                'processor' => 'Intel Core i9-13950HX',
                'gpu' => 'NVIDIA GeForce RTX 4090',
                'ram' => '32GB DDR5',
                'storage' => '2TB NVMe SSD',
                'display' => '16" Dual UHD+/FHD+ Mini-LED',
                'price' => 249999.00,
                'image_url' => 'https://images.unsplash.com/photo-1629131726692-1accd0c53ce0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Omen 17',
                'brand' => 'HP',
                'processor' => 'Intel Core i7-13700HX',
                'gpu' => 'NVIDIA GeForce RTX 4070',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '17.3" QHD 240Hz',
                'price' => 91999.00,
                'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Stealth 16 Studio',
                'brand' => 'MSI',
                'processor' => 'Intel Core i7-13700H',
                'gpu' => 'NVIDIA GeForce RTX 4060',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '16" FHD+ 165Hz',
                'price' => 84999.00,
                'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Victus 15',
                'brand' => 'HP',
                'processor' => 'AMD Ryzen 5 7535HS',
                'gpu' => 'NVIDIA GeForce RTX 2050',
                'ram' => '8GB DDR5',
                'storage' => '512GB NVMe SSD',
                'display' => '15.6" FHD 144Hz',
                'price' => 41999.00,
                'image_url' => 'https://images.unsplash.com/photo-1593642702821-c823b13eb295?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Nitro 5',
                'brand' => 'Acer',
                'processor' => 'Intel Core i5-12500H',
                'gpu' => 'NVIDIA GeForce RTX 3050',
                'ram' => '16GB DDR4',
                'storage' => '512GB NVMe SSD',
                'display' => '15.6" FHD 144Hz',
                'price' => 49999.00,
                'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'TUF Gaming A15',
                'brand' => 'ASUS',
                'processor' => 'AMD Ryzen 7 7735HS',
                'gpu' => 'NVIDIA GeForce RTX 4050',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '15.6" FHD 144Hz',
                'price' => 58999.00,
                'image_url' => 'https://images.unsplash.com/photo-1629131726692-1accd0c53ce0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Katana 15',
                'brand' => 'MSI',
                'processor' => 'Intel Core i7-13620H',
                'gpu' => 'NVIDIA GeForce RTX 4070',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '15.6" FHD 144Hz',
                'price' => 74999.00,
                'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ],
            [
                'name' => 'Predator Helios 300',
                'brand' => 'Acer',
                'processor' => 'Intel Core i7-12700H',
                'gpu' => 'NVIDIA GeForce RTX 3070 Ti',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'display' => '15.6" QHD 165Hz',
                'price' => 88999.00,
                'image_url' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            ]
        ];

        foreach ($laptops as $laptop) {
            \Modules\Ecommerce\Models\Laptop::create($laptop);
        }
    }
}
