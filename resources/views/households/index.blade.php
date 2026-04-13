@extends('layouts.app')

@section('title', 'Household Registry - SafeTrack')

@section('additional_styles')
<style>
    .main-content {
        padding: 20px !important;
    }
    .table-responsive {
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
    @php
        $u = auth()->user();
        $canAddHouseholds = $u->hasPermission('add_households') || $u->isSuperAdmin();
        $canUpdateHouseholds = $u->hasPermission('update_households') || $u->isSuperAdmin();
        $canExportHouseholds = $canAddHouseholds || $canUpdateHouseholds || $u->hasPermission('view_households');
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">Household Registry</h1>
            <p class="text-muted mb-0">Search, filter, and register households with vulnerability scoring.</p>
        </div>
        @if($canAddHouseholds || $canExportHouseholds)
        <div class="btn-group">
            @if($canAddHouseholds)
            <a href="{{ route('households.create') }}" class="btn btn-primary">Register Household</a>
            <a href="{{ route('households.upload') }}" class="btn btn-secondary">Upload CSV</a>
            @endif
            @if($canExportHouseholds)
            <a href="{{ route('households.export') }}" class="btn btn-success">Export CSV</a>
            @endif
        </div>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <input id="householdSearch" type="search" class="form-control" placeholder="Search by head, sitio, address, or ID">
        </div>
        <div class="col-md-3">
            <select id="vulnerabilityFilter" class="form-select">
                <option value="all">Filter by vulnerability</option>
                <option value="Critical">Critical</option>
                <option value="High">High</option>
                <option value="Moderate">Moderate</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="householdTable">
            <thead class="table-light">
                <tr>
                    <th>Household ID</th>
                    <th>Head of Family</th>
                    <th>Sitio / Address</th>
                    <th>Members</th>
                    <th>Vulnerability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($households as $household)
                    <tr data-badge="{{ $household->vulnerability_badge }}">
                        <td>{{ $household->household_id }}</td>
                        <td>{{ $household->headname }}</td>
                        <td>
                            <strong>{{ $household->sitio ?? $household->purok }}</strong><br>
                            <small class="text-muted">{{ $household->address }}</small>
                        </td>
                        <td>{{ $household->members->count() }}</td>
                        <td>
                            @php
                                $badgeClass = 'secondary';
                                if ($household->vulnerability_badge === 'Critical') $badgeClass = 'danger';
                                elseif ($household->vulnerability_badge === 'High') $badgeClass = 'warning';
                                else $badgeClass = 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $household->vulnerability_badge }}</span>
                        </td>
                        <td>
                            <a href="{{ route('households.show', $household) }}" class="btn btn-sm btn-info">View</a>
                            @if($canUpdateHouseholds)
                            <a href="{{ route('households.edit', $household) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('households.destroy', $household) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $households->links() }}

@endsection

@section('additional_scripts')
<script>
    function applySearchAndFilter() {
        const search = document.getElementById('householdSearch').value.toLowerCase();
        const badge = document.getElementById('vulnerabilityFilter').value;
        document.querySelectorAll('#householdTable tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            const badgeValue = row.dataset.badge;
            const matchesSearch = !search || text.includes(search);
            const matchesBadge = badge === 'all' || badgeValue === badge;
            row.style.display = matchesSearch && matchesBadge ? '' : 'none';
        });
    }

    document.getElementById('householdSearch').addEventListener('input', applySearchAndFilter);
    document.getElementById('vulnerabilityFilter').addEventListener('change', applySearchAndFilter);
</script>
@endsection
