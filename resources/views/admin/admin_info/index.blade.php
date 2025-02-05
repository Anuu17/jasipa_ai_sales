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
    @include('admin.layouts.sidebar')
    <!-- Navbar -->
    @include('admin.layouts.header')


    <div class="page-wrapper">
        <div class="d-flex justify-content-between align-items-center ms-3 p-3">
            <!-- Save Button on the Left -->
            <div>
                <a type="button" class="btn btn-primary" href="{{ route('admin.admin_info.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    <span>新規追加</span>
                </a>
            </div>

            <!-- Search Bar on the Right -->
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
        <div class="table-responsive text-nowrap ms-3 p-3 ">
            <table id="sorter" class="table table-bordered tablesorter-bootstrap">
                <thead>
                <tr>
                    <th class="bg-dark text-white" style="font-size: 0.9rem;" >ID</th>
                    <th class="bg-dark text-white" style="font-size: 0.9rem;">名前</th>
                    <th class="bg-dark text-white" style="font-size: 0.9rem;">メールアドレス</th>
                    <th class="bg-dark text-white" style="font-size: 0.9rem;">アクション</th>

                </tr>
                </thead>
                <tbody>
                @foreach ($adminInfo as $admin)
                    <tr>
                        <td>{{ $admin->id}}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>

                        <td class="text-center" >
                            <a href="{{ route('admin.admin_info.edit', $admin->id) }}" class="btn btn-custom btn-dark me-2">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-settings" style="margin-right: -4px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                            </a>

                            <form action="{{ route('admin.admin_info.destroy', $admin->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-custom btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash" style="margin-right: -4px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @include('admin.layouts.footer')
    </div>
</div>


<!-- Tabler Core -->
<script src="{{asset('admin/assets/dist/js/tabler.min.js?1692870487')}}" defer></script>
<script src="{{asset('admin/assets//dist/js/demo.min.js?1692870487')}}" defer></script>

</body>
</html>
