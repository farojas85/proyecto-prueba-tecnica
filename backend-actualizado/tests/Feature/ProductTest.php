<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private $user;
    private Category $category;

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

        // Crear categoría base
        $this->category = Category::create([
            'name' => 'Categoría Test',
            'description' => 'Categoría de prueba para productos',
            'status' => 1
        ]);
    }

    public function test_can_create_product()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/products', [
            'name' => 'Laptop Gamer',
            'description' => 'Una laptop potente',
            'price' => 1500.00,
            'stock' => 5,
            'category_id' => $this->category->id
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('product.name', 'Laptop Gamer');
        $this->assertDatabaseHas('products', ['name' => 'Laptop Gamer']);
    }

    public function test_can_register_stock_movement_entry()
    {
        $product = Product::create([
            'name' => 'Celular Pro',
            'description' => 'Celular de gama alta',
            'price' => 999.99,
            'stock' => 10,
            'category_id' => $this->category->id,
            'status' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/products/' . $product->id . '/stock-movements', [
            'type' => 'entrada',
            'quantity' => 5,
            'reason' => 'Compra de proveedor'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('product.stock', 15);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'quantity' => 5
        ]);
    }

    public function test_cannot_register_exit_movement_with_insufficient_stock()
    {
        $product = Product::create([
            'name' => 'Teclado Mecánico',
            'description' => 'Teclado rgb',
            'price' => 49.99,
            'stock' => 2,
            'category_id' => $this->category->id,
            'status' => 1
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/products/' . $product->id . '/stock-movements', [
            'type' => 'salida',
            'quantity' => 5,
            'reason' => 'Venta minorista'
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Stock insuficiente'
        ]);

        // El stock debe permanecer intacto (en 2)
        $this->assertEquals(2, $product->fresh()->stock);
    }
}
