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
        .profile-img {
            max-width: 150px; /* Limit the width */
            max-height: 150px; /* Limit the height */
            object-fit: cover; /* Ensure the image is cropped to fit */
            margin-bottom: 10px; /* Add spacing */
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
        {{--        <div class="table-responsive text-nowrap">--}}
        {{--            <table id="sorter" class="table tablesorter-bootstrap">--}}
        {{--                <thead>--}}
        {{--                <tr>--}}
        {{--                    <th>ID</th>--}}
        {{--                    <th>名前</th>--}}
        {{--                    <th>メールアドレス</th>--}}
        {{--                    <th>権限</th>--}}
        {{--                </tr>--}}
        {{--                </thead>--}}
        {{--                <tbody>--}}
        {{--                @foreach ($userInfo as $user)--}}
        {{--                    <tr>--}}
        {{--                        <td>{{ $user->id}}</td>--}}
        {{--                        <td>{{ $user->name }}</td>--}}
        {{--                        <td>{{ $user->email }}</td>--}}
        {{--                        <td>{{ $user->role }}</td>--}}
        {{--                    </tr>--}}
        {{--                @endforeach--}}
        {{--                </tbody>--}}
        {{--            </table>--}}
        {{--        </div>--}}

        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center"> 編集画面 </h2>
                <form action="{{ route('admin.admin_info.update', $admin->id) }}" method="POST" autocomplete="off" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('put')
                    <div class="mb-3">
                        <div class="img text-center">
                            @if($admin->image)
                            <img src="{{$admin->image}}" alt="profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="/default-files/avatar.png" alt="profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @endif
                            <label for="profile_photo">
                                <img src="/default-files/dash_camera.png" alt="camera" class="img-fluid w-100">
                            </label>
                            <input type="file" id="profile_photo" name="avatar" hidden="">
                        </div>
                        <div class="text text-center">
                            <label class="form-label">Your Avatar</label>
                            <p>PNG or JPG no bigger than 400px wide and tall.</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{$admin->name}}" class="form-control" placeholder="名前" autocomplete="off" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" value="{{$admin->email}}" class="form-control" placeholder="your@email.com" autocomplete="off" required>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            Password

                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" name = "password" class="form-control password"  placeholder="Your password"  autocomplete="off" required>

                            <span class="input-group-text toggle-password">
                    <a href="javascript:;" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                    </a>
                  </span>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>


                    <div class="mb-2">
                        <label class="form-label">
                            Confirm Password

                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" name = "password_confirmation" class="form-control confirm-password"  placeholder="Your password"  autocomplete="off" required>

                            <span class="input-group-text toggle-confirm-password">
                    <a href="javascript:;" class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                    </a>
                  </span>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

{{--                    <div class="mb-3">--}}
{{--                        <label class="form-label">Role</label>--}}
{{--                        <select name="role" class="form-control" required>--}}
{{--                            <option value="" disabled>Select the role</option>--}}
{{--                            <option value="Company admin" @if($user->role == "Company admin") selected @endif>Company admin</option>--}}
{{--                            <option value="General user" @if($user->role == "General user") selected @endif>General user</option>--}}
{{--                        </select>--}}
{{--                        <x-input-error :messages="$errors->get('role')" class="mt-2" />--}}
{{--                    </div>--}}


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

