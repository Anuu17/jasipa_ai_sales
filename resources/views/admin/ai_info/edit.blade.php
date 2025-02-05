<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Dashboard - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <!-- CSS files -->
    <link href="{{ asset('admin/assets/dist/css/tabler.min.css?1692870487') }}" rel="stylesheet"/>
    <link href="{{ asset('admin/assets/dist/css/demo.min.css?1692870487') }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    @vite(['resources/js/admin/login.js'])
</head>
<body >
<script src="{{ asset('admin/assets/dist/js/demo-theme.min.js?1692870487') }}"></script>
<div class="page">
    <!-- Sidebar -->
    @include('admin.layouts.sidebar')
    <!-- Navbar -->
    @include('admin.layouts.header')
    <div class="page-wrapper">


        @yield('content')
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center"> 会社編集画面 </h2>
                <form action="{{ route('admin.ai_info.update', $ai->id) }}" method="POST" autocomplete="off" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{$ai->name}}" class="form-control" placeholder="名前" autocomplete="off" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Enabled</label>
                        <select name="enabled" class="form-control" required>
                            <option value="" disabled>Select the role</option>
                            <option value="1" @if($ai->enabled == "1") selected @endif>有効</option>
                            <option value="2" @if($ai->enabled == "2") selected @endif>無効</option>
                        </select>
                        <x-input-error :messages="$errors->get('enabled')" class="mt-2" />
                    </div>


                    <div class="form-footer text-center">
                        <button type="submit" class="btn btn-primary"> 更新 </button>
                    </div>
                </form>
            </div>
        </div>


        @include('admin.layouts.footer')
    </div>
</div>


<!-- Tabler Core -->
<script src="{{asset('admin/assets/dist/js/tabler.min.js?1692870487')}}" defer></script>
<script src="{{asset('admin/assets//dist/js/demo.min.js?1692870487')}}" defer></script>

</body>
</html>

