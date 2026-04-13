<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SafeTrack')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: #f5f5f5;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
        }
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: relative;
            z-index: 2;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            margin-bottom: 5px;
            text-decoration: none;
            display: block;
        }
        .sidebar .nav-link:hover {
            background: #f5f5f5;
            border-left-color: #667eea;
            color: #667eea;
        }
        .sidebar .nav-link.active {
            background: #f0f0f0;
            border-left-color: #667eea;
            color: #667eea;
            font-weight: bold;
        }
        .main-content {
            padding: 30px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px 8px 0 0;
        }
        .dashboard-card {
            text-align: center;
            padding: 30px;
        }
        .dashboard-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        .dashboard-card .label {
            color: #666;
            margin-top: 10px;
        }
        .badge-role {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
        }
    </style>
    @yield('additional_styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">SafeTrack</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="#profile">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="px-4 mt-3 mb-4">Menu</h5>
                    <nav class="nav flex-column">
                        <a href="{{ route('dashboard') }}" class="nav-link @if(request()->routeIs('dashboard')) active @endif" style="font-weight: 500; padding: 14px 20px; transition: all 0.3s ease;">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="{{ route('households.index') }}" class="nav-link @if(request()->routeIs('households.*')) active @endif" style="font-weight: 500; padding: 14px 20px; transition: all 0.3s ease; cursor: pointer;">
                            <i class="bi bi-house-fill"></i> Households
                        </a>
                       
                        <a href="{{ route('analytics.index') }}" class="nav-link @if(request()->routeIs('analytics.*')) active @endif" style="font-weight: 500; padding: 14px 20px; transition: all 0.3s ease;">
                            <i class="bi bi-graph-up"></i> Analytics
                        </a>

                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage_users') || auth()->user()->hasRole('Captain'))
                            <hr class="my-3">
                            <h5 class="px-4">Admin</h5>
                            <a href="{{ route('users.index') }}" class="nav-link @if(request()->routeIs('users.*')) active @endif">
                                <i class="bi bi-person-badge"></i> Users
                            </a>
                            <a href="{{ route('roles.index') }}" class="nav-link @if(request()->routeIs('roles.*')) active @endif">
                                <i class="bi bi-shield-lock"></i> Roles
                            </a>
                            <a href="{{ route('permissions.index') }}" class="nav-link @if(request()->routeIs('permissions.*')) active @endif">
                                <i class="bi bi-lock"></i> Permissions
                            </a>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('additional_scripts')
</body>
</html>
