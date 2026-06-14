<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Orden importa: primero el catálogo de skills, luego los usuarios demo
     * (que referencian skills existentes).
     */
    public function run(): void
    {
        $this->call([
            SkillSeeder::class,
            CatalogSeeder::class,
            DemoUserSeeder::class,
        ]);
    }
}
