<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SafeTrack</title>
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
        }
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            margin-bottom: 5px;
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
                        <a href="{{ route('dashboard') }}" class="nav-link active">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="{{ route('households.index') }}" class="nav-link">
                            <i class="bi bi-house"></i> Households
                        </a>
                       
                        <a href="{{ route('analytics.index') }}" class="nav-link">
                            <i class="bi bi-graph-up"></i> Analytics
                        </a>

                        @if(auth()->user()->isSuperAdmin())
                            <hr class="my-3">
                            <h5 class="px-4">Admin</h5>
                            <a href="{{ route('users.index') }}" class="nav-link">
                                <i class="bi bi-person-badge"></i> Users
                            </a>
                            <a href="{{ route('roles.index') }}" class="nav-link">
                                <i class="bi bi-shield-lock"></i> Roles
                            </a>
                            <a href="{{ route('permissions.index') }}" class="nav-link">
                                <i class="bi bi-lock"></i> Permissions
                            </a>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="main-content">
                    <h1 class="mb-4">Welcome, {{ auth()->user()->name }}!</h1>

                    <!-- User Info Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                    <p><strong>Role:</strong> <span class="badge-role">{{ auth()->user()->role->name }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Joined:</strong> {{ auth()->user()->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Cards -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card dashboard-card">
                                <div class="number">{{ \App\Models\Household::count() }}</div>
                                <div class="label">Total Households</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card">
                                <div class="number">{{ \App\Models\Member::count() }}</div>
                                <div class="label">Total Population</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card dashboard-card">
                                <div class="number">{{ \App\Models\Responder::count() }}</div>
                                <div class="label">Responders</div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Creation Section -->
                    @if(auth()->user()->isSuperAdmin())
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-person-plus"></i> Create New Accounts</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Manage user accounts for responders, evacuation officers, and household members.</p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <a href="{{ route('users.create') }}" class="btn btn-primary w-100">
                                            <i class="bi bi-person-badge"></i> Create Responder Account
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('users.create') }}" class="btn btn-primary w-100">
                                            <i class="bi bi-shield-check"></i> Create Evacuation Officer
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="{{ route('users.create') }}" class="btn btn-primary w-100">
                                            <i class="bi bi-house-door"></i> Create Household Account
                                        </a>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <p class="text-muted small mb-0"><strong>Note:</strong> Set a temporary password for the user. They can change it after logging in.</p>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card dashboard-card">
                                    <div class="number">{{ \App\Models\User::count() }}</div>
                                    <div class="label">Total Users</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card dashboard-card">
                                    <div class="number">{{ \App\Models\Role::count() }}</div>
                                    <div class="label">Roles</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>