<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Одежда, обувь, аксессуары',
                'slug' => 'odezda-obuv-aksessuary',
                'description' => null,
                'image' => 'uploads/categories/67f51f548c5e8_1744117588.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:11:10',
                'updated_at' => '2025-04-08 07:21:28',
            ],
            [
                'id' => 2,
                'name' => 'Товары для детей, игрушки',
                'slug' => 'tovary-dlia-detei-igruski',
                'description' => null,
                'image' => 'uploads/categories/67f523ec88238_1744118764.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:11:19',
                'updated_at' => '2025-04-08 07:41:04',
            ],
            [
                'id' => 3,
                'name' => 'Дом, дача, сад',
                'slug' => 'dom-daca-sad',
                'description' => null,
                'image' => 'uploads/categories/67f523f41fdcf_1744118772.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:11:29',
                'updated_at' => '2025-04-08 07:41:12',
            ],
            [
                'id' => 4,
                'name' => 'Строительство и ремонт',
                'slug' => 'stroitelstvo-i-remont',
                'description' => null,
                'image' => 'uploads/categories/67f52421b8d6f_1744118817.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:11:44',
                'updated_at' => '2025-04-08 07:41:57',
            ],
            [
                'id' => 5,
                'name' => 'Продукты питания, напитки',
                'slug' => 'produkty-pitaniia-napitki',
                'description' => null,
                'image' => 'uploads/categories/67f52434b75ca_1744118836.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:12:10',
                'updated_at' => '2025-04-08 07:42:16',
            ],
            [
                'id' => 6,
                'name' => 'Электроника, бытовая техника',
                'slug' => 'elektronika-bytovaia-texnika',
                'description' => null,
                'image' => 'uploads/categories/67f5244ded5bc_1744118861.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:42:41',
                'updated_at' => '2025-04-08 07:42:41',
            ],
            [
                'id' => 7,
                'name' => 'Мебель',
                'slug' => 'mebel',
                'description' => null,
                'image' => 'uploads/categories/67f5245ed0578_1744118878.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:42:58',
                'updated_at' => '2025-04-08 07:42:58',
            ],
            [
                'id' => 8,
                'name' => 'Сырьё, материалы',
                'slug' => 'syre-materialy',
                'description' => null,
                'image' => 'uploads/categories/67f5246d3bd6e_1744118893.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:43:13',
                'updated_at' => '2025-04-08 07:43:13',
            ],
            [
                'id' => 9,
                'name' => 'Услуги',
                'slug' => 'uslugi',
                'description' => null,
                'image' => 'uploads/categories/67f5247bd5b3e_1744118907.webp',
                'parent_id' => null,
                'status' => 'active',
                'created_at' => '2025-04-08 07:43:27',
                'updated_at' => '2025-04-08 07:43:27',
            ],
        ]);
    }
}
