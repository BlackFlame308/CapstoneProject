<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Households</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Households</h1>
        <div class="mb-3">
            <a href="{{ route('households.create') }}" class="btn btn-primary">Add Household</a>
            <a href="{{ route('households.upload') }}" class="btn btn-secondary">Upload CSV</a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Head of Household</th>
                    <th>Address</th>
                    <th>Purok</th>
                    <th>Contact Number</th>
                    <th>Emergency Contact</th>
                    <th>Members</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($households as $household)
                <tr>
                    <td>{{ $household->household_id }}</td>
                    <td>{{ $household->headname }}</td>
                    <td>{{ $household->address }}</td>
                    <td>{{ $household->purok }}</td>
                    <td>{{ $household->contact_number }}</td>
                    <td>{{ $household->emergency_contact }}</td>
                    <td>{{ $household->members->count() }}</td>
                    <td>
                        <a href="{{ route('households.show', $household) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('households.edit', $household) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('households.destroy', $household) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $households->links() }}
    </div>
</body>
</html>