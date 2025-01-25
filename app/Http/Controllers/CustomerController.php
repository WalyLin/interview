<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Documentation",
 *     description="API Documentation for My Laravel Application",
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */
class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customer.index');
    }

    /**
     * 显示客户列表
     * @OA\Get(
     *     path="/api/customers",
     *     summary="获取客户列表",
     *     tags={"Customer"},
     *     @OA\Response(
     *         response=200,
     *         description="成功获取客户列表",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="张三"),
     *                     @OA\Property(property="last_name", type="string", example="李四"),
     *                     @OA\Property(property="email", type="string", format="email", example="zhangsan@example.com"),
     *                     @OA\Property(property="age", type="integer", example=30),
     *                     @OA\Property(property="dob", type="string", format="date", example="1993-01-01"),
     *                     @OA\Property(property="creation_date", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function list()
    {
        $list = Customer::select()->get();

        return response()->json([
            'message' => '操作成功',
            'success' => true,
            'code' => 200,
            'data' => $list
        ]);
    }

    /**
     * 新增客户
     * @OA\Post(
     *     path="/api/customer/create",
     *     summary="新增客户",
     *     tags={"Customer"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="张三"),
     *             @OA\Property(property="last_name", type="string", example="李四"),
     *             @OA\Property(property="email", type="string", format="email", example="zhangsan@example.com"),
     *             @OA\Property(property="age", type="integer", example=30),
     *             @OA\Property(property="dob", type="string", format="date", example="1993-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="客户新增成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="操作失败",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作失败"),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {


        $validatedData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:customer,email',
            'age' => 'required|integer|min:0|max:150',
            'dob' => 'required|date',
        ]);


        if (Customer::create($validatedData)) {
            return $this->success();
        } else {
            return $this->error();
        }
    }
    /**
     * 修改客户
     * @OA\Put(
     *     path="/api/customer/update",
     *     summary="修改客户",
     *     tags={"Customer"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="张三"),
     *             @OA\Property(property="last_name", type="string", example="李四"),
     *             @OA\Property(property="email", type="string", format="email", example="zhangsan@example.com"),
     *             @OA\Property(property="age", type="integer", example=30),
     *             @OA\Property(property="dob", type="string", format="date", example="1993-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="客户修改成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="操作失败",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作失败"),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:customer,id',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:customer,email,' . $request->input('id'),
            'age' => 'required|integer|min:0|max:150',
            'dob' => 'required|date',
        ]);


        $customer = Customer::find($request->input('id'));
        if (!$customer) {
            return $this->error();
        }

        return $customer->fill($validatedData)->save() ? $this->success() : $this->error();
    }

    /**
     * 删除客户
     * @OA\Delete(
     *     path="/api/customer/delete/{id}",
     *     summary="删除客户",
     *     tags={"Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="客户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="客户删除成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="操作失败",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作失败"),
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function delete(Request $request, $id)
    {

        Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:customer,id',
        ]);


        $id = intval($id);
        Customer::destroy([$id]);
        return $this->success();
    }

    /**
     * 获取客户详情
     * @OA\Get(
     *     path="/api/customer/read/{id}",
     *     summary="获取客户详情",
     *     tags={"Customer"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="客户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功获取客户详情",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="操作成功"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="张三"),
     *                 @OA\Property(property="last_name", type="string", example="李四"),
     *                 @OA\Property(property="email", type="string", format="email", example="zhangsan@example.com"),
     *                 @OA\Property(property="age", type="integer", example=30),
     *                 @OA\Property(property="dob", type="string", format="date", example="1993-01-01"),
     *                 @OA\Property(property="creation_date", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="客户未找到",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Customer not found")
     *         )
     *     )
     * )
     */
    public function read(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['success' => false, 'code' => 404, 'message' => 'Customer not found'], 404);
        }
        return $this->success('操作成功', $customer);
    }

}