<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    /**
     * 发送邮箱验证码
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function sendEmailVerificationCode(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
                'code' => 500
            ]);
        }

        $email = $request->input('email');

        // 生成验证码
        $verificationCode = mt_rand(100000, 999999);

        Redis::setex('email_verify_code:' . $email, 600, $verificationCode);

        // 发送邮件
        Mail::to($email)->send(new EmailVerification($verificationCode));

        return response()->json([
            'message' => '验证码发送成功',
            'success' => true,
            'code' => 200,
            'data' => $verificationCode
        ]);
    }

    /**
     * 执行登陆
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'verify_code' => 'required|numeric'
            ]);

            // 验证邮箱验证码
            // $this->verifyCode($request->input('email'), $request->input('verify_code'));



            $response = Http::asForm()->post(config('app.url').'/oauth/token', [
                'grant_type' => 'password',
		'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),		
		'username' => $request->input('email'),
                'password' => $request->input('password'),
                'scope' => '',
            ]);

            if ($response->status() != 200) {
                return response()->json([
                    'message' => $response->collect()->isEmpty() ? 'fail.' . $response->reason() : '账户或密码错误',
                    'success' => false,
                    'code' => 500
                ]);
            } else {
                return response()->json([
                    'message' => '操作成功',
                    'success' => true,
                    'code' => 200,
                    'data' => $response->collect()
                ]);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
                'code' => 500
            ]);
        }

    }

    /**
     * 验证邮箱验证码
     * @param mixed $email
     * @param mixed $code
     * @throws \Exception
     * @return void
     */
    public function verifyCode($email, $code)
    {
        $storedCode = Redis::get('email_verify_code:' . $email);
        if (!$storedCode || Redis::ttl('email_verify_code:' . $email) <= 0) {
            throw new \Exception('验证码已过期.');
        }

        if ($storedCode != $code) {
            throw new \Exception('验证码错误.');
        }
        Redis::del('email_verify_code:' . $email);
    }

    public function oauthtest(Request $request)
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => '未登录',
                'success' => false,
                'code' => 500,
                'data' => [

                ]

            ]);
        }
        return response()->json([
            'message' => '已登录',
            'success' => false,
            'code' => 200,
            'data' => [
                'user' => $user,
            ]

        ]);

    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => 'waly',
            'email' => 'waly@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        return response()->json([
            'data' => $user
        ]);

    }
}
