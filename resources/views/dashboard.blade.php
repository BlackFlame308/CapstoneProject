@extends('layouts.app')

@section('title', 'Dashboard - SafeTrack')

@section('content')
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

                    <!-- Upload CSV Shortcut -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card p-3">
                                <h5>Quick Actions</h5>
                                <p class="text-muted">Upload household CSV data directly from dashboard.</p>
                                <a href="{{ route('households.upload') }}" class="btn btn-success">
                                    <i class="bi bi-upload"></i> Upload Household CSV
                                </a>
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
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage_users') || auth()->user()->hasRole('Captain'))
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

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('manage_users') || auth()->user()->hasRole('Captain'))
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
@endsection