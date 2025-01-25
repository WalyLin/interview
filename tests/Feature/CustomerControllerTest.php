<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * 测试获取客户列表
     *
     * @return void
     */
    public function test_can_get_customer_list()
    {

        $user = User::factory()->create();

        // 创建一些测试数据
        Customer::factory()->count(5)->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/customers');


        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'success',
                'code',
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'age',
                        'dob',
                        'creation_date',
                    ],
                ],
            ]);
    }

    /**
     * 测试新增客户
     *
     * @return void
     */
    public function test_can_create_customer()
    {
        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'age' => $this->faker->numberBetween(18, 99),
            'dob' => $this->faker->date('Y-m-d'),
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->postJson('/api/customer/create', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '操作成功',
                'success' => true,
                'code' => 200,
            ]);

        $this->assertDatabaseHas('customer', [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'age' => $data['age'],
            'dob' => $data['dob'],
        ]);
    }

    /**
     * 测试新增客户时输入错误数据
     *
     * @return void
     */
    public function test_cannot_create_customer_with_invalid_data()
    {
        $invalidData = [
            'first_name' => '', // 空字符串，假设这是无效的
            'last_name' => $this->faker->lastName,
            'email' => 'invalid-email', // 无效的电子邮件格式
            'age' => 'invalid-age', // 无效的年龄格式
            'dob' => 'invalid-date', // 无效的日期格式
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->postJson('/api/customer/create', $invalidData);

        $response->assertStatus(422) // 假设服务器返回422 Unprocessable Entity状态码
            ->assertJsonValidationErrors(['first_name', 'email', 'age', 'dob']); // 验证返回的错误字段
    }

    /**
     * 测试修改客户
     *
     * @return void
     */
    public function test_can_update_customer()
    {

        $user = User::factory()->create();

        $customer = Customer::factory()->create();

        $data = [
            'id' => $customer->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'age' => $this->faker->numberBetween(18, 99),
            'dob' => $this->faker->date('Y-m-d'),
        ];

        $response = $this->actingAs($user, 'api')->putJson('/api/customer/update', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '操作成功',
                'success' => true,
                'code' => 200,
            ]);

        $this->assertDatabaseHas('customer', [
            'id' => $customer->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'age' => $data['age'],
            'dob' => $data['dob'],
        ]);
    }

    /**
     * 测试更新客户时输入错误数据
     *
     * @return void
     */
    public function test_cannot_update_customer_with_invalid_data()
    {
        $invalidData = [
            'id' => 'a',
            'first_name' => '', // 空字符串，假设这是无效的
            'last_name' => $this->faker->lastName,
            'email' => 'invalid-email', // 无效的电子邮件格式
            'age' => 'invalid-age', // 无效的年龄格式
            'dob' => 'invalid-date', // 无效的日期格式
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->putJson('/api/customer/update', $invalidData);

        $response->assertStatus(422) // 假设服务器返回422 Unprocessable Entity状态码
            ->assertJsonValidationErrors(['first_name', 'email', 'age', 'dob', 'id']); // 验证返回的错误字段
    }

    /**
     * 测试删除客户
     *
     * @return void
     */
    public function test_can_delete_customer()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($user, 'api')->deleteJson('/api/customer/delete/' . $customer->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '操作成功',
                'success' => true,
                'code' => 200,
            ]);

        $this->assertDatabaseMissing('customer', [
            'id' => $customer->id,
        ]);
    }

    /**
     * 测试获取客户详情
     *
     * @return void
     */
    public function test_can_get_customer_details()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/customer/read/' . $customer->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '操作成功',
                'success' => true,
                'code' => 200,
                'data' => [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'age' => $customer->age,
                    'dob' => $customer->dob->format('Y-m-d'),
                    'creation_date' => $customer->creation_date,
                ],
            ]);
    }

    /**
     * 测试获取不存在的客户详情
     *
     * @return void
     */
    public function test_cannot_get_nonexistent_customer_details()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->getJson('/api/customer/read/9999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'code' => 404,
                'message' => 'Customer not found',
            ]);
    }
}