<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AiSetting;
use App\Models\Companies;
use App\Models\CompanyToken;
use App\Models\UserToken;
use App\Traits\FileUpload;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;

class DashboardController extends Controller
{   use FileUpload;

    function index() : View{
        return view('admin.dashboard');
    }

    function user_info_show() {
        $userInfo = User::with('company')->orderBy('id')->get();

        return view('admin.user_info.index',compact('userInfo'));
    }

    public function user_info_create() {
        $companies = Companies::all();

        return view('admin.user_info.create', compact('companies'));
    }

    public function user_info_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' =>['required'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'max:255','confirmed',
                \Illuminate\Validation\Rules\Password::min(8) //最低８文字必要
                ->letters() //1文字以上のアルファベットを含むこと
                ->mixedCase() //大文字と小文字のアルファベットを含むこと
                ->numbers() //1文字以上の数字を含むこと
                ->symbols()
            ],
            'role' => ['required'],
        ]);

        $user = User::create([
            'company_id' => $request->company,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        if (!$user->save()){
            abort(500,'新規作成に失敗しました。');
        }
        $userInfo = User::orderBy('id')->get();

        return redirect()->route('admin.user_info');
    }

    public function user_info_edit(User $user)
    {
        $companyName = $user->company->name;
        $companies = Companies::all();

        return view('admin.user_info.edit', compact('user','companyName','companies'));
    }

    public function user_info_update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' =>['required'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'max:255','confirmed',
                \Illuminate\Validation\Rules\Password::min(8) //最低８文字必要
                ->letters() //1文字以上のアルファベットを含むこと
                ->mixedCase() //大文字と小文字のアルファベットを含むこと
                ->numbers() //1文字以上の数字を含むこと
                ->symbols()
            ],
            'role' => ['required'],
            'avatar'=>['nullable','image','max:200'],

        ]);

        $user->name = $validatedData['name'];
        $user->company_id = $validatedData['company'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        if(!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        if($request->hasFile('avatar')){
            $avatarPath = $this->uploadFile($request->file('avatar'));
            $this->deleteFile($user->image);
            $user->image = $avatarPath;
        }

        if (!$user->save()) {
            abort(500, 'ユーザーの更新に失敗しました。');
        }

        return redirect()->route('admin.user_info');
    }

    public function user_info_destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user_info');
    }

    function admin_info_show() {
        $adminInfo = Admin::orderBy('id')->get();

        return view('admin.admin_info.index',compact('adminInfo'));
    }
    public function admin_info_create() {
        return view('admin.admin_info.create');
    }

    public function admin_info_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Admin::class],
            'password' => ['required', 'max:255','confirmed',
                \Illuminate\Validation\Rules\Password::min(8) //最低８文字必要
                ->letters() //1文字以上のアルファベットを含むこと
                ->mixedCase() //大文字と小文字のアルファベットを含むこと
                ->numbers() //1文字以上の数字を含むこと
                ->symbols()
            ],
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$admin->save()){
            abort(500,'新規作成に失敗しました。');
        }
        $adminInfo = Admin::orderBy('id')->get();

        return redirect()->route('admin.admin_info');
    }

    public function admin_info_edit(Admin $admin)
    {
        return view('admin.admin_info.edit', compact('admin'));
    }

    public function admin_info_update(Request $request, Admin $admin)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'max:255','confirmed',
                \Illuminate\Validation\Rules\Password::min(8) //最低８文字必要
                ->letters() //1文字以上のアルファベットを含むこと
                ->mixedCase() //大文字と小文字のアルファベットを含むこと
                ->numbers() //1文字以上の数字を含むこと
                ->symbols()
            ],
            'avatar' => ['nullable','image','max:200'],

        ]);

        $admin->name = $validatedData['name'];
        $admin->email = $validatedData['email'];

        if(!empty($validatedData['password'])) {
            $admin->password = Hash::make($validatedData['password']);
        }
        if($request->hasFile('avatar')){
            $avatarPath = $this->uploadFile($request->file('avatar'));
            $this->deleteFile($admin->image);
            $admin->image = $avatarPath;
        }

        if (!$admin->save()) {
            abort(500, 'ユーザーの更新に失敗しました。');
        }

        return redirect()->route('admin.admin_info');
    }
    public function admin_info_destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.admin_info');
    }

    public function company_info_show()
    {
        $companyInfo = Companies::orderBy('id')->get();

        return view('admin.company_info.index',compact('companyInfo'));
    }

    public function company_info_create() {
        return view('admin.company_info.create');
    }
    public function company_info_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'enabled' => ['required'],
        ]);

        $company = Companies::create([
            'name' => $request->name,
            'enabled' => $request->enabled
        ]);

        if (!$company->save()){
            abort(500,'新規作成に失敗しました。');
        }

        return redirect()->route('admin.company_info');
    }
    public function company_info_edit(Companies $company)
    {
        return view('admin.company_info.edit', compact('company'));
    }

    public function company_info_update(Request $request, Companies $company)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'enabled' => ['required'],
        ]);

        $company->name = $validatedData['name'];
        $company->enabled = $validatedData['enabled'];

        if (!$company->save()) {
            abort(500, 'ユーザーの更新に失敗しました。');
        }

        return redirect()->route('admin.company_info');
    }
    public function company_info_destroy(Companies $company)
    {
        $company->delete();
        return redirect()->route('admin.company_info');
    }
    public function ai_info_show()
    {
        $aiInfo = AiSetting::orderBy('id')->get();

        return view('admin.ai_info.index',compact('aiInfo'));
    }

    public function ai_info_create() {
        return view('admin.ai_info.create');
    }
    public function ai_info_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'enabled' => ['required'],
        ]);

        $ai = AiSetting::create([
            'name' => $request->name,
            'enabled' => $request->enabled
        ]);

        if (!$ai->save()){
            abort(500,'新規作成に失敗しました。');
        }

        return redirect()->route('admin.ai_info');
    }

    public function ai_info_edit(AiSetting $ai)
    {
        return view('admin.ai_info.edit', compact('ai'));
    }

    public function ai_info_update(Request $request, AiSetting $ai)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'enabled' => ['required'],
        ]);

        $ai->name = $validatedData['name'];
        $ai->enabled = $validatedData['enabled'];

        if (!$ai->save()) {
            abort(500, 'ユーザーの更新に失敗しました。');
        }

        return redirect()->route('admin.ai_info');
    }
    public function ai_info_destroy(AiSetting $ai)
    {
        $ai->delete();
        return redirect()->route('admin.ai_info');
    }

    public function company_token_show()
    {
        $aiInfo = AiSetting::orderBy('id')->get();
        $companyInfo = Companies::orderBy('id')->get();

        $tokenInfo = CompanyToken::all()->groupBy('company_id');

        return view('admin.company_token.index',compact('aiInfo','companyInfo','tokenInfo'));
    }

    public function company_token_store(Request $request)
    {
//       $a = $request;

        $tokenData = json_decode($request->input('tableData'), true);

        foreach ($tokenData as $data) {
            if ($data['tokenType'] === 'input') {
                CompanyToken::updateOrCreate(
                    [
                        'company_id' => $data['companyId'],
                        'ai_id' => $data['aiId'],
                    ],
                    [
                        'input_token' => $data['tokenValue'],
                    ]
                );
            }
        }

        foreach ($tokenData as $data) {
            if ($data['tokenType'] === 'output') {
                CompanyToken::updateOrCreate(
                    [
                        'company_id' => $data['companyId'],
                        'ai_id' => $data['aiId'],
                    ],
                    [
                        'output_token' => $data['tokenValue'],
                    ]
                );
            }
        }

        return redirect()->route('admin.company_token');
    }

    public function users_token_show($companyId)
    {
        $company = Companies::findOrFail($companyId);
        $users = User::where('company_id', $companyId)->get();
        $aiInfo = AiSetting::orderBy('id')->get();

        $tokenInfo = UserToken::all()->groupBy('user_id');

        return view('admin.company_token.users', compact('company', 'users','aiInfo','tokenInfo'));
    }

    public function users_token_store(Request $request)
    {
        $tokenData = json_decode($request->input('tableData'), true);
        $companyId = $request->company_id;

        $companyToken = CompanyToken::where('company_id', $companyId)->get()->groupBy('ai_id');

        $inputTokenTotals = [];
        $outputTokenTotals = [];
        foreach ($tokenData as $data) {
            if ($data['tokenType'] === 'input') {
                $aiId = $data['aiId'];

                if (!isset($inputTokenTotals[$aiId])) {
                    $inputTokenTotals[$aiId] = 0;
                }
                $inputTokenTotals[$aiId] += $data['tokenValue'];
            }
        }

        foreach ($inputTokenTotals as $aiId => $totalTokenValue) {
            $companyTokenLimit = $companyToken->get($aiId)->sum("input_token");

            if ($companyTokenLimit < $totalTokenValue) {
                return back()->withErrors([
                    'token_limit' => "The InputToken exceeds the company's limit",
                ]);
            }
        }

        foreach ($tokenData as $data) {
            if ($data['tokenType'] === 'output') {
                $aiId = $data['aiId'];
                if (!isset($outputTokenTotals[$aiId])) {
                    $outputTokenTotals[$aiId] = 0;
                }
                $outputTokenTotals[$aiId] += $data['tokenValue'];
            }
        }

        foreach ($outputTokenTotals as $aiId => $totalTokenValue) {
            $companyTokenLimit = $companyToken->get($aiId)->sum("output_token");
            if ($companyTokenLimit < $totalTokenValue) {
                return back()->withErrors([
                    'token_limit' => "The OutputToken exceeds the company's limit",
                ]);
            }
        }

        foreach ($tokenData as $data) {
            if ($data['tokenType'] === 'input') {
                UserToken::updateOrCreate(
                    [
                        'user_id' => $data['userId'],
                        'ai_id' => $data['aiId'],
                    ],
                    [
                        'input_token' => $data['tokenValue'],
                    ]
                );
            }
        }

        foreach ($tokenData as $data) {
            if ($data['tokenType'] === 'output') {

                UserToken::updateOrCreate(
                    [
                        'user_id' => $data['userId'],
                        'ai_id' => $data['aiId'],
                    ],
                    [
                        'output_token' => $data['tokenValue'],
                    ]
                );
            }
        }


        return redirect()->route('admin.users_token', ['company' => $companyId]);
    }


}
