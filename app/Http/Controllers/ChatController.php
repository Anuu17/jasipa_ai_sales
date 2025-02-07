<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Prompt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ClaudeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ChatController extends Controller
{
    private ClaudeService $claudeService;


    public function __construct(ClaudeService $claudeService)
    {
        $this->claudeService = $claudeService;

    }
    public function chat_screen()
    {
        $user = auth()->user();
        $conversations = Conversation::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $prompt = Prompt::where('company_id', $user->company_id)->where('user_id', $user->id)->where('is_company_setting', 0)->first();

        if (!$prompt) {
            $prompt = Prompt::where('company_id', $user->company_id)->where('is_company_admin', 1)->where('is_company_setting', 1)->first();
        }

        return view('chat.index',compact('prompt','user','conversations'));
    }

    public function chat(Request $request)
    {

        $user = auth()->user();
        $fileResponse = $this->file_upload($request);
        $fileOriginalName = $request->file('file')->getClientOriginalName();
        $filenameWithoutExtension = pathinfo($fileOriginalName, PATHINFO_FILENAME);
        $filePath = $fileResponse->getData()->file_path;

        $conversation = Conversation::create([
            'user_id' => $user->id,
            'prompt_message' => $request->input('message'),
            'project_details' => $request->input('projectDetails'),
            'skills_experience' => $request->input('skillsAndExperience'),
            'label' => $filenameWithoutExtension,
            'upload_file' => $filePath,
        ]);

        $cacheKey = $conversation->id."_".$conversation->user_id."_".$conversation->created_at."_";

        try {


            $csvData = $fileResponse->getData()->csv_data;

            $message = $request->input('message');

            $editMessage = str_replace(
                ['【案件内容】', '【技術者のスキルと経歴】'],
                [$request->input('projectDetails'), $request->input('skillsAndExperience')],
                $message
            );


            $combinedMessage = $cacheKey."\n".$editMessage . "\n\nスキルシートデータは以下になります。:\n" . $csvData ;
            $length = mb_strlen($combinedMessage);
            $token_count = ceil($length/1.5);


            $userMessage = Message::create([
                            'conversation_id' => $conversation->id,
                            'role' => 'user',
                            'content' => $combinedMessage,
                        ]);
            $combinedMessage = [
                'role' => 'user',
                'content' => $cacheKey."\n".$editMessage . "\n\nスキルシートデータは以下になります。:\n" . $csvData
            ];

            $response = $this->claudeService->chat($combinedMessage['content']);
            $messageContent = $response['message'];
            unset($response['message']);

            $apiMessage = Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $messageContent,
                'input_token' => $response['input_tokens'],
                'output_token' => $response['output_tokens'],
                'cache_read' => $response['cache_read'],
                'cache_creation' => $response['cache_creation'],
                'message_json' => json_encode($response),
            ]);
            $response['message'] = $messageContent;

            return response()->json([
                'conversation_id' => $conversation->id,
                'conversation_label' => $conversation->label,
                'is_success' => true,
                'message' => $response,
                'created_at' => $conversation->created_at->format('m-d H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function file_upload(Request $request)
    {
        try {
            $fileOriginalName = $request->file('file')->getClientOriginalName();
            $file = $request->file('file');

            $ext = pathinfo($fileOriginalName)['extension'];
            $check_extension = ['xlsx'];
            if (!in_array($ext, $check_extension, true)) {
                return response()->json([
                    'is_success' => false,
                    'errors_message' => ["エクセルファイルのみアップロードお願いします。"]
                ]);
            }

            $time = date('YmdHis');
            $fileName = $time . $file->getClientOriginalName();
            $file->move(public_path('skillsheet'), $fileName);

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(public_path("skillsheet/{$fileName}"));
            $data = [$spreadsheet->getActiveSheet()->toArray()];

            $csvData = $this->convertArrayToCsv($data);

//            $data = Excel::toArray([], public_path("skillsheet/{$fileName}"),null, Excel::CALCULATE);

//            $csvData = $this->convertArrayToCsv($data);

//            Storage::delete('temp/' . $fileName);

            return response()->json([
                'is_success' => true,
                'csv_data' => $csvData,
                'file_path' => "skillsheet/{$fileName}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'errors_message' => [$e->getMessage()]
            ]);
        }
    }

    private function convertArrayToCsv(array $data): string
    {
        $csv = '';
        foreach ($data[0] as $row) {
            if (is_array($row)) {
                $csv .= implode(',', $row) . "\n";
            }
        }
        return $csv;
    }
    public function prompt_save(Request $request)
    {

        $user = auth()->user();
        $isCompanySetting = 0;
        $isCompanyAdmin = 1;

        if ($user->role === 'General user') {
            $isCompanyAdmin = 0;
        }

        Prompt::updateOrCreate(
            [
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'is_company_setting' => $isCompanySetting
            ],
            [
                'message' => $request->message,
                'is_company_admin' => $isCompanyAdmin,

            ]
        );

        return redirect()->route('chat_screen.chat')->with('success', 'プロンプトが保存されました。');
    }

    public function prompt_reset(Request $request)
    {
        $user = auth()->user();

        $userPrompt = Prompt::where('company_id', $user->company_id)->where('user_id', $user->id)->where('is_company_setting', 0)->first();

        if ($userPrompt) {
            $userPrompt->delete();
        }
            return redirect()->route('chat_screen.chat')->with('success', 'プロンプトが初期化されました。');

    }

    public function newMessage(Request $request)
    {
        try {
            $conversation = Conversation::findOrFail($request->conversation_id);

//            $cacheKey = $conversation->id."_".$conversation->user_id."_".$conversation->created_at."_";


            $previousMessages = Message::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'role' => $message->role,
                        'content' => $message->content
                    ];
                })
                ->toArray();

//            $conversationHistory .= "[User]: ".$request->message . "\n";
//            $newMessage = $request->input('message');
//            $contextMessage = $cachedPrompt . "\n\n" . $newMessage;
//            $response = $this->claudeService->chat($contextMessage);

            $response = $this->claudeService->chat($request->message, $previousMessages);
            $messageContent = $response['message'];
            unset($response['message']);

            $userMessage = Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $request->message,
            ]);

            $apiMessage = Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $messageContent,
                'input_token' => $response['input_tokens'],
                'output_token' => $response['output_tokens'],
                'cache_read' => $response['cache_read'],
                'cache_creation' => $response['cache_creation'],
                'message_json' => json_encode($response),
            ]);
            $response['message'] = $messageContent;

            return response()->json([
                'is_success' => true,
                'message' => $response,
                'user_message_created_at' => $userMessage->created_at->format('m-d H:i'),
                'api_message_created_at' => $apiMessage->created_at->format('m-d H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getDetails($id)
    {
        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at')
            ->get()
            ->slice(1);

        $conversationHistory = [];
        foreach ($messages as $msg) {
            if ($msg->role == 'user') {
                $role = 'User';
            } else {
                $role = '美咲';
            }
            $conversationHistory[] = [
                'role' => $role,
                'content' => $msg->content,
            ];
        }


        return response()->json([
            'message' => $conversation->prompt_message,
            'project_details' => $conversation->project_details,
            'skillsAndExperience' => $conversation->skills_experience,
            'conversationHistory' => $conversationHistory,
            'conversation_id' => $conversation->id,
            'created_at' => $conversation->created_at->format('m-d H:i')

        ]);
    }
    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        return redirect()->back()->with('success', 'Conversation deleted successfully.');
    }

    public function getTokenCount(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);

        $lastMessage = Message::where('conversation_id', $conversation->id)
            ->latest('created_at')
            ->first();

        $text = $request->input('text');
        $length = mb_strlen($text, 'UTF-8');
        $newMessageTokenCount = ceil($length * 1.5);

        if (!$lastMessage || $lastMessage->created_at < $fiveMinutesAgo) {

        $totalPreviousTokens = Message::where('conversation_id', $conversation->id)
            ->sum('cache_creation');

        $estimatedNewCacheToken = $totalPreviousTokens + $newMessageTokenCount;
        } else {

        $estimatedNewCacheToken = $lastMessage->cache_creation + $newMessageTokenCount;
        }


        return response()->json(['token_count' => $estimatedNewCacheToken]);
    }
    public function updateLabel(Request $request, $id)
    {
        $conversation = Conversation::find($id);
        $conversation->label = $request->label;
        $conversation->save();

        return response()->json(['message' => 'Label updated successfully']);
    }
}
