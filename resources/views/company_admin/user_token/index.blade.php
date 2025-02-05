<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>法人管理者</title>
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
    @include('company_admin.layouts.sidebar')
    <!-- Navbar -->
    @include('company_admin.layouts.header')
    <div class="page-wrapper">
        <div class="d-flex justify-content-between align-items-center ms-3 p-3">
            <div>
                <form action="./" method="get" autocomplete="off" novalidate>
                    <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                            <path d="M21 21l-6 -6"/>
                        </svg>
                    </span>
                        <input type="text" value="" class="form-control" placeholder="Search…" aria-label="Search in website">
                    </div>
                </form>
            </div>
        </div>

        @yield('content')
        <form id="tokenTable" class ="h-adr"  action="{{route('company_user_token.store')}}" method="POST">
            @csrf
            <input type="hidden" id="tableData" name="tableData">
            <input type="hidden" name="company_id" value="{{ $company->id }}">
            <div class="table-responsive text-nowrap ms-3 p-3 ">
                <table id="sorter" class="table table-bordered tablesorter-bootstrap">
                    <thead>
                    <tr>
                        <th class="bg-dark text-white" style="font-size: 0.9rem;"  >ユーザー名</th>
                        @foreach ($aiInfo as $ai)
                            @if($ai->enabled == 1)
                                <th class="bg-dark text-white" style="font-size: 0.9rem;" colspan="2" >{{$ai->name}}</th>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <th></th>
                        @foreach ($aiInfo as $ai)
                            @if($ai->enabled == 1)
                                <th class="bg-light text-dark" style="font-size: 0.8rem;">Input</th>
                                <th class="bg-light text-dark" style="font-size: 0.8rem;">Output</th>
                            @endif
                        @endforeach

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr data-user-id="{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            @foreach ($aiInfo as $ai)
                                @if($ai->enabled == 1)
                                    @php
                                        $token = isset($tokenInfo[$user->id])? $tokenInfo[$user->id]->firstWhere('ai_id', $ai->id) : null;
                                    @endphp
                                    <td data-ai-id="{{ $ai->id }}" data-token-type="input" contenteditable="true" class="editable-cell bg-light text-dark text-end">{{ $token ? $token->input_token : '' }}</td>
                                    <td data-ai-id="{{ $ai->id }}" data-token-type="output" contenteditable="true" class="editable-cell bg-light text-dark text-end">{{ $token ? $token->output_token : '' }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if ($errors->has('token_limit'))
                    <div class="alert alert-danger">
                        {{ $errors->first('token_limit') }}
                    </div>
                @endif
                <div class="form-footer ms-3 p-3 text-end">
                    <button id="submitTableData" class="btn btn-primary">保存</button>
                </div>
            </div>
        </form>
        @include('admin.layouts.footer')
    </div>
</div>


<!-- Tabler Core -->
<script src="{{asset('admin/assets/dist/js/tabler.min.js?1692870487')}}" defer></script>
<script src="{{asset('admin/assets//dist/js/demo.min.js?1692870487')}}" defer></script>
<script>
    $(function () {

        $('#submitTableData').click(function (event) {

            event.preventDefault();

            let tokenDataJson = getTokenTableData();

            if (!validateTokenData(tokenDataJson)) {
                return;
            }

            $('#tableData').val(tokenDataJson);

            $('#tokenTable').submit();
        });


        function getTokenTableData() {
            let tokenData = [];

            $('#sorter tbody tr').each(function () {
                let userId = $(this).data('user-id');


                $(this).find('.editable-cell').each(function () {
                    let aiId = $(this).data('ai-id');
                    let tokenType = $(this).data('token-type');
                    let tokenValue = $(this).text();

                    tokenData.push({
                        userId: userId,
                        aiId: aiId,
                        tokenType: tokenType,
                        tokenValue: tokenValue,
                    });
                });
            });

            return JSON.stringify(tokenData);
        }


        function validateTokenData(tokenDataJson) {
            let tokenData = JSON.parse(tokenDataJson);
            let isValid = true;
            let errorMessage = '';

            tokenData.forEach(data => {

                if (data.tokenType === 'input' || data.tokenType === 'output') {
                    if (data.tokenValue === '') {
                        isValid = false;
                        errorMessage = 'The cell cannot be blank.';
                    } else if (isNaN(data.tokenValue)) {
                        isValid = false;
                        errorMessage = 'The value should be a number.';
                    }
                }
            });
            if (!isValid) {
                alert(errorMessage);
            }
            return isValid;
        }
    });
</script>


</body>
</html>


