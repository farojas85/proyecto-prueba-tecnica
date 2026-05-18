<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario para bypass de token auth
        $this->token = 'test-token-12345';
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@legacy.test',
            'password' => bcrypt('password'),
            'api_token' => $this->token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->user = DB::table('users')->where('id', $userId)->first();
    }

    public function test_can_create_category()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/categories', [
            'name' => 'Categoría Nueva',
            'description' => 'Descripción de prueba'
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('name', 'Categoría Nueva');
        $this->assertDatabaseHas('categories', ['name' => 'Categoría Nueva']);
    }

    public function test_category_list_is_paginated()
    {
        for ($i = 1; $i <= 15; $i++) {
            Category::create([
                'name' => 'Categoria ' . $i,
                'description' => 'Descripcion ' . $i,
                'status' => 1
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/categories?per_page=10');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'last_page',
            'total'
        ]);
        $response->assertJsonCount(10, 'data');
    }

    public function test_cannot_delete_category_with_associated_products()
    {
        $category = Category::create([
            'name' => 'Categoria Principal',
            'description' => 'Con productos',
            'status' => 1
        ]);
        
        // Crear un producto asociado
        Product::create([
            'name' => 'Producto de Prueba',
            'description' => 'Un producto asociado',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'status' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'error' => 'No se puede eliminar la categoría porque tiene productos asociados'
        ]);
        
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
