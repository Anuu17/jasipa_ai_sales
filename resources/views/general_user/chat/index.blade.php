<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Dashboard - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <!-- CSS files -->
    <link href="{{ asset('admin/assets/dist/css/tabler.min.css?1692870487') }}" rel="stylesheet"/>
    <link href="{{ asset('admin/assets/dist/css/demo.min.css?1692870487') }}" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        .btn-custom {
            padding: 5px 10px;
            font-size: 1rem;
        }
    </style>
</head>
<body >
<script src="{{ asset('admin/assets/dist/js/demo-theme.min.js?1692870487') }}"></script>
<div class="page">
    <!-- Sidebar -->
    @if($user->role === "General user")
        @include('general_user.layouts.sidebar')
        @include('general_user.layouts.header')

    @elseif($user->role === "Company admin")
        @include('company_admin.layouts.sidebar')
        @include('company_admin.layouts.header')
    @endif
    <!-- Navbar -->

    <div class="page-wrapper">

        @yield('content')
        <div class="page-wrapper">
            <!-- Page header -->

            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                Chat
                            </h2>
                        </div>
                        @if($user->role === "General user")
                            <div class="col text-end">
                                <a type="button" class="btn btn-primary" href="{{route('general_user.chat')}}">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>新規チャト</span>
                                </a>
                            </div>
                        @elseif($user->role === "Company admin")
                            <div class="col text-end">
                                <a type="button" class="btn btn-primary" href="{{route('company_admin.chat')}}">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>新規チャト</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-12 col-lg-5 col-xl-3 border-end">
                                <div class="card-header d-none d-md-block">
                                    <div class="input-icon">
                                  <span class="input-icon-addon"> <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                                  </span>
                                        <input type="text" value="" class="form-control" placeholder="Search…" aria-label="Search">
                                    </div>
                                </div>
                                <div class="card-body p-0 scrollable" style="max-height: 38rem">
                                    <div class="nav flex-column nav-pills" role="tablist">
                                        @foreach($conversations as $conversation)
                                            <div class="d-flex align-items-center">
                                                <div class="col-auto">
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-muted p-0" type="button" id="dropdownMenuButton{{ $conversation->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                                <circle cx="12" cy="12" r="1"></circle>
                                                                <circle cx="12" cy="5" r="1"></circle>
                                                                <circle cx="12" cy="19" r="1"></circle>
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $conversation->id }}">
                                                            <li>
                                                                <form action="{{ route('conversations.destroy', $conversation->id) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item">Delete</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Avatar Second -->
                                                <div class="col-auto">
                                                    <span class="avatar rounded-circle" style="background-image: url('/default-files/avatar-girl.png')"></span>
                                                </div>

                                                <!-- Conversation Content Third -->
                                                <div class="col text-body">
                                                    <a href="{{ $conversation->id }}"
                                                       class="nav-link conversation-tab text-start mw-100 p-3 {{ $loop->first ? 'active' : '' }}"
                                                       data-id="{{ $conversation->id }}"
                                                       data-bs-toggle="pill"
                                                       role="tab"
                                                       aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                        <div class="d-flex flex-column">
                                                            <div class="fw-bold">美咲</div>
                                                            <div class="text-secondary text-truncate w-100">
                                                                {{ $conversation->prompt_message }}
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-7 col-xl-9 d-flex flex-column">
                                <div class="card-body scrollable" style="height: 44rem">
                                    <div class="chat">
                                        <div class="chat-bubbles" id="messages">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <h2 class="page-title">SES 営業アシスタント</h2>
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="img">
                                                        <img src="/default-files/avatar-girl.png" alt="profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                                    </div>

                                                    <div class="ms-3">
                                                        <h2 class="mb-0">美咲</h2>
                                                        <small class="text-muted">営業の達人<br>"最高のマッチングを、あなたと一緒に！"</small>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <h2 class="mb-0">経験値：38/100</h2>
                                                        <div class="progress">
                                                            <div class="progress-bar" style="width: 38%" role="progressbar" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100" aria-label="38% Complete">
                                                                <span class="visually-hidden">38% Complete</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                @if($user->role === "General user")
                                                    <form action="{{ route('general_user.prompt.save') }}" method="POST" style="display:inline;">
                                                @elseif($user->role === "Company admin")
                                                    <form action="{{ route('company_admin.prompt.save') }}" method="POST" style="display:inline;">
                                                @endif

                                                @csrf
                                                    <div class="text-end">
                                                    <button type="submit" name="prompt_save" class="btn btn-outline-info disable-on-load">プロンプト保存</button>

                                                    @if($user->role === "General user")
                                                        <a href="{{ route('general_user.prompt.reset') }}" id="prompt_reset" class="btn btn-outline-danger disable-on-load" data-disabled="false">初期化</a>
                                                    @elseif($user->role === "Company admin")
                                                        <a href="{{ route('company_admin.prompt.reset') }}" id="prompt_reset" class="btn btn-outline-danger disable-on-load" data-disabled="false">初期化</a>
                                                    @endif
                                                </div>

                                                <textarea name="message" id="message" class="form-control" rows="3" autocomplete="off" placeholder="メッセージを入力しましょう">{{ $prompt->message }}</textarea>

                                            </form>

{{--                                            <form action="{{ route('prompt.reset') }}" method="POST" style="display:inline;">--}}
{{--                                                @csrf--}}
{{--                                                <div class = "text-end">--}}
{{--                                                <button type="submit" class="btn btn-outline-danger">初期化</button>--}}
{{--                                                </div>--}}
{{--                                            </form>--}}
                                                <div class="d-flex align-items-center">
                                                    <div class="w-100">
                                                        <h4 class="mb-1">案件内容</h4>
                                                        <div class="input-group input-group-flat">
                                                            <textarea id="project-details" class="form-control" rows="3" autocomplete="off" placeholder="案件の詳細を教えてください。一緒に最適な人材を見つけましょう!"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="d-flex align-items-center">
                                                <div class="w-100">
                                                    <h4 class="mb-1">技術者のスキルと経歴</h4>
                                                    <div class="input-group input-group-flat">
                                                        <textarea id="skills-experience" class="form-control" rows="3" autocomplete="off" placeholder="技術者のスキルや経歴を教えてください。素晴らしい才能を見逃さないようにしましょう!"></textarea>
                                                    </div>
                                                </div>
                                            </div>
{{--                                            <form id="messageForm" action="" method="POST" enctype="multipart/form-data">--}}
                                            <div class="d-flex align-items-center">
                                                <div class="w-100">
                                                    <h4 class="mb-1">スキルシート</h4>
                                                    <div class="input-group input-group-flat">
                                                        <input type="file" id="upload_file" name="upload_file" class="form-control" accept="xlsx" required>

                                                    </div>
                                                </div>
                                            </div>
{{--                                            <div class="d-flex align-items-center">--}}
{{--                                                <div class="w-100">--}}
{{--                                                    <h4 class="mt-3 mb-1">私の指示（オプション）</h4>--}}
{{--                                                    <div class="input-group input-group-flat">--}}
{{--                                                        <textarea id="optional-instructions" class="form-control" rows="1" autocomplete="off" placeholder="特別な指示があればどうぞ。あなたの要望に合わせて対応します!"></textarea>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            <div class="d-flex align-items-center">
                                                <div class="w-100">
                                                        <button id="sendMessage" type="submit" class="form-control btn btn-purple"> マッチングを始めましょう!　　<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-send"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11" /><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" /></svg></button>
                                                </div>
                                            </div>
{{--                                            </form>--}}
                                            <div class="mt-3">
                                                <div class="d-flex align-items-center">
                                                        <span class="badge badge-pill bg-red-lt"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-sparkles"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 18a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm0 -12a2 2 0 0 1 2 2a2 2 0 0 1 2 -2a2 2 0 0 1 -2 -2a2 2 0 0 1 -2 2zm-7 12a6 6 0 0 1 6 -6a6 6 0 0 1 -6 -6a6 6 0 0 1 -6 6a6 6 0 0 1 6 6z" /></svg>マッチング成功：50件</span>
                                                        <span class="badge badge-pill bg-yellow-lt ms-3"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trophy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 21l8 0" /><path d="M12 17l0 4" /><path d="M7 4l10 0" /><path d="M17 4v8a5 5 0 0 1 -10 0v-8" /><path d="M5 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M19 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /></svg>トップセールス</span>
                                                        <span class="badge badge-pill bg-green-lt ms-3"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-star"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>顧客満足度：4.9/5</span>

                                                        <div class="ms-auto">

                                                            <a href="#" class="btn btn-outline-info">
                                                                実績をみる
                                                            </a>
                                                        </div>

                                                </div>
                                            </div>
                                            <div id="output-container"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layouts.footer')
    </div>
</div>


<!-- Tabler Core -->
<script src="{{asset('admin/assets/dist/js/tabler.min.js?1692870487')}}" defer></script>
<script src="{{asset('admin/assets//dist/js/demo.min.js?1692870487')}}" defer></script>
<script>
    // Embed user's role into a JavaScript variable
    const userRole = @json($user->role);
</script>

<script>
    $(document).ready(function () {
        $('#sendMessage').click(function () {

            const projectDetails = $('#project-details').val();
            const skillsAndExperience = $('#skills-experience').val();
            const fileInput = document.getElementById("upload_file");
            const message = $('#message').val();


            if (!message.includes('【案件内容】') || !message.includes('【技術者のスキルと経歴】')) {
                alert('Error:（【案件内容】、【技術者のスキルと経歴】）がメッセージに含まれていません。');
                return;
            }

            if (!fileInput.files || !fileInput.files[0]) {
                alert('Error: ファイルを選択してください。');
                return;
            }
            $('#output-container').html(`
            <div class="d-flex justify-content-center align-items-center" style="height: 600px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            `);


            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('message', message);
            formData.append('projectDetails', projectDetails);
            formData.append('skillsAndExperience', skillsAndExperience);
            // formData.append('optionalInstructions', optionalInstructions);
            let apiEndpoint = '';
            if (userRole === "General user") {
                apiEndpoint = '/general_user/api/chat';
            } else if (userRole === "Company admin") {
                apiEndpoint = '/company_admin/api/chat';
            }

            $.ajax({
                type: "POST",
                url: apiEndpoint,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.is_success) {
                        const now = new Date();
                        $('#output-container').html(`
                        <div class="card">
                            <div class="card-body">
                                <div class="chat" style="height: 600px; display: flex; flex-direction: column;">
                                    <div class="chat-bubbles" id="messages-container" style="flex-grow: 1; overflow-y: auto;">

                                        <div class="d-flex justify-content-start mb-2">
                                            <div class="col-auto">
                                                <span class="avatar rounded-circle" style="background-image: url('/default-files/avatar-girl.png')"></span>
                                            </div>
                                            <div class="chat-bubble border rounded p-2" style="white-space: pre-wrap;"><div class="row">
                                            <div class="col chat-bubble-author">美咲</div><div class="col-auto chat-bubble-date">${response.created_at}</div>
                                            </div>${response.message.message}
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div id="conversationId" style="display: none;" data-conversation-id="${response.conversation_id}"></div>
                        </div>
                        <div class="chat-footer">
                            <div class="input-group">
                             <textarea id="new-instruction" class="form-control" rows="1" placeholder="メッセージ入力しましょう。"></textarea>
                             <button id="calculateToken" class="btn btn-green">計算</button><button id="send-button" class="btn btn-primary">送信</button>
                            </div>
                        </div>
                    `);
                    } else {
                        alert(response.errors_message.join('\n'));
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });

</script>
<script>
    $(document).on('click', '#send-button', function () {
        const newInstruction = $('#new-instruction').val();

        if (newInstruction.trim() === '') {
            alert('メッセージを入力してください。');
            return;
        }
        const conversationId = $('#conversationId').data('conversation-id');

        let apiEndpoint = '';
        if (userRole === "General user") {
            apiEndpoint = '/general_user/api/chat/new_message';
        } else if (userRole === "Company admin") {
            apiEndpoint = '/company_admin/api/chat/new_message';
        }

        $('#send-button').prop('disabled', true);
        $('#messages-container').append(`
        <div id="loading-indicator" class="d-flex justify-content-start mb-2">
        <div class="col-auto">
            <span class="avatar rounded-circle" style="background-image: url('/default-files/avatar-girl.png')"></span>
            </div>
            <div class="chat-bubble border rounded p-2">考えてます<span class="animated-dots">...</span>
            </div>
        </div>
        `);

        $.ajax({
            type: "POST",
            url: apiEndpoint,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                conversation_id: conversationId,
                message: newInstruction
            },
            success: function (response) {
                $('#loading-indicator').remove();
                $('#send-button').prop('disabled', false);
                if (response.is_success) {
                    const newInstruction = $('#new-instruction').val();
                    $('#messages-container').append(`

                                 <div class="d-flex justify-content-end mb-2">
                                  <div class="chat-bubble bg-light border rounded p-2"><div class = "row">
                                   <div class="col chat-bubble-author">Anand</div><div class="col-auto chat-bubble-date">${response.user_message_created_at}</div>
                                   </div>${newInstruction}
                                   </div>
                                 </div>

                        <div class="d-flex justify-content-start mb-2">
                        <div class="col-auto">
                          <span class="avatar rounded-circle" style="background-image: url('/default-files/avatar-girl.png')"></span>
                         </div>
                            <div class="chat-bubble border rounded p-2" style="white-space: pre-wrap;"><div class = "row">
                                <div class="col chat-bubble-author">美咲</div><div class="col-auto chat-bubble-date">${response.api_message_created_at}</div>
                                </div>${response.message.message}
                                </div>
                        </div>
                    `);


                    $('#new-instruction').val('');
                    const messagesContainer = $('#messages-container');
                    messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
                }else {
                    alert('エラー: ' + response.errors_message.join('\n'));
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.nav-link.conversation-tab').on('click', function (e) {
            e.preventDefault();

            var conversationId = $(this).data('id');
            const fileInput = document.getElementById("upload_file");
            let apiEndpoint = '';
            if (userRole === "General user") {
                apiEndpoint = '/general_user/conversation/';
            } else if (userRole === "Company admin") {
                apiEndpoint = '/company_admin/conversation/';
            }

            $.ajax({
                url: apiEndpoint + conversationId + '/details',
                method: 'GET',
                success: function (response) {
                    $('#message').val(response.message).prop('disabled', true);
                    $('#project-details').val(response.project_details).prop('disabled', true);
                    $('#skills-experience').val(response.skillsAndExperience).prop('disabled', true);
                    $('.disable-on-load').prop('disabled', true);
                    $('#prompt_reset').attr('data-disabled', 'true').addClass('disabled');
                    $(fileInput).prop('disabled', true);
                    $('#sendMessage').prop('disabled', true);

                    $('#output-container').html(`
                    <div class="card">
                        <div class="card-body">
                            <div class="chat" style="height: 600px; overflow-y: auto;">
                                <div class="chat-bubbles" id="messages-container"></div>
                                <div class="chat-footer">
                                    <div class="input-group">
                                        <textarea id="new-instruction" class="form-control" rows="1" placeholder="メッセージ入力しましょう。"></textarea>
                                        <button id="calculateToken" class="btn btn-green">計算</button><button id="send-button" class="btn btn-primary">送信</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="conversationId" style="display: none;" data-conversation-id="${response.conversation_id}"></div>
                    </div>
                `);
                    response.conversationHistory.forEach(msg => {
                        if (msg.role === 'User') {

                            $('#messages-container').append(`
                                <div class="d-flex justify-content-end mb-2">
                                    <div class="chat-bubble bg-light border rounded p-2"><div class = "row">
                                <div class="col chat-bubble-author">Anand</div><div class="col-auto chat-bubble-date">${response.created_at}</div>
                                </div>${msg.content}
                                </div>
                                </div>
                            `);
                        } else {

                            $('#messages-container').append(`
                                <div class="d-flex justify-content-start mb-2">
                                <div class="col-auto">
                                  <span class="avatar rounded-circle" style="background-image: url('/default-files/avatar-girl.png')"></span>
                                </div>
                                <div class="chat-bubble border rounded p-2" style="white-space: pre-wrap;"><div class = "row">
                                <div class="col chat-bubble-author">美咲</div><div class="col-auto chat-bubble-date">${response.created_at}</div>
                                </div>${msg.content}
                                </div>
                                </div>
                            `);
                        }
                    });
                },
                error: function (xhr) {
                    alert('Error fetching details: ' + xhr.responseJSON.error);
                }
            });
        });
    });
</script>
<script>
    $(document).on('click', '#calculateToken', function () {
        const newInstruction = $('#new-instruction').val();

        if (newInstruction.trim() === '') {
            alert('メッセージを入力してください。');
            return;
        }
        const conversationId = $('#conversationId').data('conversation-id');

        $.ajax({
            method: 'GET',
            url: '/getTokenCount',
            data: { text: newInstruction,
                conversation_id: conversationId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#token-count-container').remove();

                const tokenCountHtml = `
                <div id="token-count-container" class="mt-2 text-muted">
                    <strong>トークン数:</strong> ${response.token_count}
                </div>
            `;

                // Append it after the .chat-footer
                $('.chat-footer').after(tokenCountHtml);

            },

            error: function () {
                alert('エラーが発生しました。');
            }
        });


    });
</script>

</body>
</html>
