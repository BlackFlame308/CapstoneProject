<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Household</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h1>Edit Household</h1>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('households.update', $household) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="household_id" class="form-label">Household ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('household_id') is-invalid @enderror" id="household_id" name="household_id" value="{{ old('household_id', $household->household_id) }}" required placeholder="e.g., HH-001">
                        @error('household_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $household->address) }}" required placeholder="Street address">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('region') is-invalid @enderror" id="region" name="region" value="{{ old('region', $household->region) }}" required placeholder="e.g., Region IV-A">
                        @error('region')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province', $household->province) }}" required placeholder="e.g., Laguna">
                        @error('province')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="city_mun" class="form-label">City/Mun <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city_mun') is-invalid @enderror" id="city_mun" name="city_mun" value="{{ old('city_mun', $household->city_mun) }}" required placeholder="e.g., Sta. Rosa City">
                        @error('city_mun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('barangay') is-invalid @enderror" id="barangay" name="barangay" value="{{ old('barangay', $household->barangay) }}" required placeholder="e.g., Barangay Z">
                        @error('barangay')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="household_number" class="form-label">Household Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('household_number') is-invalid @enderror" id="household_number" name="household_number" value="{{ old('household_number', $household->household_number) }}" required placeholder="e.g., HH-001">
                        @error('household_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="headname" class="form-label">Head of Household Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('headname') is-invalid @enderror" id="headname" name="headname" value="{{ old('headname', $household->headname) }}" required placeholder="e.g., Juan Dela Cruz">
                        @error('headname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number', $household->contact_number) }}" required placeholder="e.g., 09123456789">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $household->emergency_contact) }}" required placeholder="Name and phone number">
                        @error('emergency_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h4>Family Members</h4>
                    <div id="members">
                        @foreach($household->members as $index => $member)
                        <div class="member mb-3 border p-3 rounded">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="members[{{ $index }}][name]" value="{{ $member->name }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Age</label>
                                    <input type="number" class="form-control" name="members[{{ $index }}][age]" value="{{ $member->age }}" required min="0" max="150">
                                </div>
                                <div class="col-md-2">
                                    <label>Gender</label>
                                    <select class="form-control" name="members[{{ $index }}][gender]" required>
                                        <option value="male" {{ $member->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $member->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ $member->gender == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Special Needs</label>
                                    <select class="form-control" name="members[{{ $index }}][special_needs]">
                                        <option value="">None</option>
                                        <option value="child" {{ $member->special_needs == 'child' ? 'selected' : '' }}>Child (0-17)</option>
                                        <option value="adult" {{ $member->special_needs == 'adult' ? 'selected' : '' }}>Adult (18-59)</option>
                                        <option value="senior" {{ $member->special_needs == 'senior' ? 'selected' : '' }}>Senior (60+)</option>
                                        <option value="pwd" {{ $member->special_needs == 'pwd' ? 'selected' : '' }}>PWD</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-member">Remove</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <button type="button" id="add-member" class="btn btn-secondary">Add Member</button>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update Household</button>
                        <a href="{{ route('households.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let memberIndex = {{ $household->members->count() }};

        $('#add-member').click(function() {
            const memberHtml = `
                <div class="member mb-3 border p-3 rounded">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="members[${memberIndex}][name]" required>
                        </div>
                        <div class="col-md-2">
                            <label>Age</label>
                            <input type="number" class="form-control" name="members[${memberIndex}][age]" required min="0" max="150">
                        </div>
                        <div class="col-md-2">
                            <label>Gender</label>
                            <select class="form-control" name="members[${memberIndex}][gender]" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Special Needs</label>
                            <select class="form-control" name="members[${memberIndex}][special_needs]">
                                <option value="">None</option>
                                <option value="child">Child (0-17)</option>
                                <option value="adult">Adult (18-59)</option>
                                <option value="senior">Senior (60+)</option>
                                <option value="pwd">PWD</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>