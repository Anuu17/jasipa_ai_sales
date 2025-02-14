<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use App\Models\Companies;
use App\Models\CompanyToken;
use App\Models\Prompt;
use App\Traits\FileUpload;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{   use FileUpload;
    public function index()
    {
        $user = Auth::user();

        if ($user->role === "Company admin") {
            return view('company_admin.dashboard');
        } elseif ($user->role === "General user") {
            return redirect()->route('chat_screen.chat');
        }

        abort(403, 'Unauthorized');
    }

    function company_user_show() {

        $companyAdmin = auth()->user();

        $userInfo = User::with('company')
            ->where('company_id', $companyAdmin->company_id)
            ->orderBy('id')
            ->get();

        return view('company_admin.user_info.index',compact('userInfo'));
    }

    public function company_user_create() {

        $companyAdmin = auth()->user();

        $company = Companies::find($companyAdmin->company_id);

        return view('company_admin.user_info.create', compact('company'));
    }

    public function company_user_store(Request $request)
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

        return redirect()->route('company_user');
    }

    public function company_user_edit(User $user)
    {

        $companyAdmin = auth()->user();

        $company = Companies::find($companyAdmin->company_id);

        return view('company_admin.user_info.edit', compact('user','company'));
    }

    public function company_user_update(Request $request, User $user)
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

        return redirect()->route('company_user');
    }

    public function company_user_destroy(User $user)
    {
        $companyAdmin = auth()->user();

        if($companyAdmin->company_id == $user->company_id) {
            $user->delete();
            return redirect()->route('company_user');
        }
        abort(403, 'Unauthorized');
    }

    function company_user_token_show() {
            $companyAdmin = auth()->user();

            $company = Companies::find($companyAdmin->company_id);
            $users = User::where('company_id', $companyAdmin->company_id)->get();
            $aiInfo = AiSetting::orderBy('id')->get();

            $tokenInfo = UserToken::whereIn('user_id', $users->pluck('id'))->get()->groupBy('user_id');

            return view('company_admin.user_token.index', compact('company', 'users','aiInfo','tokenInfo'));
        }

    public function company_user_token_store(Request $request)
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


        return redirect()->route('company_user_token');
    }
    public function prompt_show() {
        $companyAdmin = auth()->user();

        $companyPrompt = Prompt::where('company_id', $companyAdmin->company_id)->where('is_company_admin', 1)->first();;

        return view('company_admin.prompt_setting.index',compact('companyPrompt'));
    }

    public function prompt_store(Request $request) {

        $companyAdmin = auth()->user();
        $isCompanySetting = 1;
        $isCompanyAdmin = 1;

        Prompt::updateOrCreate(
            [
                'company_id' => $companyAdmin->company_id,
                'is_company_admin' => $isCompanyAdmin,
                'is_company_setting' => $isCompanySetting
            ],

            [
                'message' => $request->prompt,
                'user_id' => $companyAdmin->id,

            ]
        );

        return redirect()->route('prompt');
    }



}
