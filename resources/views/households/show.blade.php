<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h1>Household Details</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Household Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Household ID:</strong> {{ $household->household_id }}</p>
                                <p><strong>Head of Household:</strong> {{ $household->headname }}</p>
                                <p><strong>Address:</strong> {{ $household->address }}</p>
                                <p><strong>Purok:</strong> {{ $household->purok }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Contact Number:</strong> {{ $household->contact_number }}</p>
                                <p><strong>Emergency Contact:</strong> {{ $household->emergency_contact }}</p>
                                <p><strong>Total Members:</strong> {{ $household->members->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Family Members</h5>
                    </div>
                    <div class="card-body">
                        @if($household->members->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Special Needs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($household->members as $member)
                                        <tr>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->age }}</td>
                                            <td>{{ ucfirst($member->gender) }}</td>
                                            <td>{{ $member->special_needs ? ucfirst($member->special_needs) : 'None' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No family members recorded for this household.</p>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('households.index') }}" class="btn btn-secondary">Back to Households</a>
                    <a href="{{ route('households.edit', $household) }}" class="btn btn-warning">Edit Household</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>