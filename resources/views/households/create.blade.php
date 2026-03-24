<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Household</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Add Household</h1>
        <form action="{{ route('households.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="household_id" class="form-label">Household ID</label>
                <input type="text" class="form-control" id="household_id" name="household_id" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="purok" class="form-label">Purok</label>
                <input type="text" class="form-control" id="purok" name="purok" required>
            </div>
            <div class="mb-3">
                <label for="emergency_contact" class="form-label">Emergency Contact</label>
                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" required>
            </div>
            <h3>Members</h3>
            <div id="members">
                <div class="member mb-3">
                    <div class="row">
                        <div class="col">
                            <label>Name</label>
                            <input type="text" class="form-control" name="members[0][name]" required>
                        </div>
                        <div class="col">
                            <label>Age</label>
                            <input type="number" class="form-control" name="members[0][age]" required>
                        </div>
                        <div class="col">
                            <label>Gender</label>
                            <select class="form-control" name="members[0][gender]" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col">
                            <label>Special Needs</label>
                            <select class="form-control" name="members[0][special_needs]">
                                <option value="">None</option>
                                <option value="child">Child (0-17)</option>
                                <option value="adult">Adult (18-59)</option>
                                <option value="senior">Senior (60+)</option>
                                <option value="pwd">PWD</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger remove-member">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-member" class="btn btn-secondary">Add Member</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    <script>
        let memberIndex = 1;
        $('#add-member').click(function() {
            const memberHtml = `
                <div class="member mb-3">
                    <div class="row">
                        <div class="col">
                            <label>Name</label>
                            <input type="text" class="form-control" name="members[${memberIndex}][name]" required>
                        </div>
                        <div class="col">
                            <label>Age</label>
                            <input type="number" class="form-control" name="members[${memberIndex}][age]" required>
                        </div>
                        <div class="col">
                            <label>Gender</label>
                            <select class="form-control" name="members[${memberIndex}][gender]" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col">
                            <label>Special Needs</label>
                            <select class="form-control" name="members[${memberIndex}][special_needs]">
                                <option value="">None</option>
                                <option value="child">Child (0-17)</option>
                                <option value="adult">Adult (18-59)</option>
                                <option value="senior">Senior (60+)</option>
                                <option value="pwd">PWD</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger remove-member">Remove</button>
                        </div>
                    </div>
                </div>
            `;
            $('#members').append(memberHtml);
            memberIndex++;
        });
        $(document).on('click', '.remove-member', function() {
            $(this).closest('.member').remove();
        });
    </script>
</body>
</html>