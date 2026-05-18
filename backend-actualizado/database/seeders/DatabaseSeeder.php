<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear usuario admin
        DB::table('users')->insert([
            'name' => 'Admin Legacy',
            'email' => 'admin@legacy.test',
            'password' => Hash::make('password'),
            'api_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Crear categorías en bloque
        $categories = [];
        for ($i = 1; $i <= 100; $i++) {
            $categories[] = [
                'name' => 'Categoria ' . $i,
                'description' => 'Descripción de categoría ' . $i,
                'status' => $i % 7 !== 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('categories')->insert($categories);

        // 3. Crear 10,000 productos optimizados en lotes (chunks) de 1000 para máximo rendimiento
        $productsChunk = [];
        for ($i = 1; $i <= 10000; $i++) {
            $productsChunk[] = [
                'name' => 'Producto Legacy ' . $i,
                'description' => 'Producto generado para prueba de rendimiento ' . $i,
                'price' => rand(1000, 30000) / 100,
                'stock' => rand(0, 200),
                'category_id' => rand(1, 100),
                'status' => $i % 9 !== 0,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now(),
            ];

            if (count($productsChunk) >= 1000) {
                DB::table('products')->insert($productsChunk);
                $productsChunk = [];
            }
        }
        if (!empty($productsChunk)) {
            DB::table('products')->insert($productsChunk);
        }

        // 4. Crear 30,000 movimientos de stock optimizados en lotes (chunks) de 1000
        $movementsChunk = [];
        for ($i = 1; $i <= 30000; $i++) {
            $movementsChunk[] = [
                'product_id' => rand(1, 10000),
                'type' => rand(0, 1) ? 'entrada' : 'salida',
                'quantity' => rand(1, 20),
                'reason' => 'Movimiento legacy ' . $i,
                'user_id' => 1,
                'created_at' => now()->subDays(rand(0, 180)),
                'updated_at' => now(),
            ];

            if (count($movementsChunk) >= 1000) {
                DB::table('stock_movements')->insert($movementsChunk);
                $movementsChunk = [];
            }
        }
        if (!empty($movementsChunk)) {
            DB::table('stock_movements')->insert($movementsChunk);
        }
    }
}
