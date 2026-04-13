@extends('layouts.app')

@section('title', 'Add Household - SafeTrack')

@section('content')
    <h1 class="h3 mb-4">Add New Household</h1>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('households.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="household_id" class="form-label">Household ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('household_id') is-invalid @enderror" id="household_id" name="household_id" value="{{ old('household_id') }}" required placeholder="e.g., HH-001">
                        @error('household_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required placeholder="Street address">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('region') is-invalid @enderror" id="region" name="region" value="{{ old('region') }}" required placeholder="e.g., Region IV-A">
                        @error('region')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province') }}" required placeholder="e.g., Laguna">
                        @error('province')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="city_mun" class="form-label">City/Mun <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('city_mun') is-invalid @enderror" id="city_mun" name="city_mun" value="{{ old('city_mun') }}" required placeholder="e.g., Sta. Rosa City">
                        @error('city_mun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('barangay') is-invalid @enderror" id="barangay" name="barangay" value="{{ old('barangay') }}" required placeholder="e.g., Barangay Z">
                        @error('barangay')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="household_number" class="form-label">Household Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('household_number') is-invalid @enderror" id="household_number" name="household_number" value="{{ old('household_number') }}" required placeholder="e.g., HH-001">
                        @error('household_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="headname" class="form-label">Head of Household Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('headname') is-invalid @enderror" id="headname" name="headname" value="{{ old('headname') }}" required placeholder="e.g., Juan Dela Cruz">
                        @error('headname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required placeholder="e.g., 09123456789">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" required placeholder="Name and phone number">
                        @error('emergency_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Create Household</button>
                        <a href="{{ route('households.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

                <hr>
                <small class="text-muted">
                    <p><strong>Note:</strong> Add household members after creating the household record.</p>
                    <p>Members can be added individually or through member management.</p>
                </small>
            </div>
        </div>
    </div>
@endsection