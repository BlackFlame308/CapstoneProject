@extends('layouts.app')

@section('title', 'Upload Households - SafeTrack')

@section('content')
    <div class="mb-4">
        <h1 class="h3">Upload Households CSV</h1>
    </div>
        
        <div class="alert alert-info">
            <h5>CSV Format Required</h5>
            <p>Your CSV file must have the following columns:</p>
            <ul>
                <li><strong>household_id</strong> - Unique household identifier (required)</li>
                <li><strong>address</strong> - Street address (required)</li>
                <li><strong>purok</strong> - Sitio/Purok name (required)</li>
                <li><strong>region</strong> - Region name (required)</li>
                <li><strong>province</strong> - Province name (required)</li>
                <li><strong>city_mun</strong> - City/Municipality (required)</li>
                <li><strong>barangay</strong> - Barangay name (required)</li>
                <li><strong>household_number</strong> - Household number (required)</li>
                <li><strong>headname</strong> - Head of household name (required)</li>
                <li><strong>contact_number</strong> - Primary contact number (required)</li>
                <li><strong>emergency_contact</strong> - Emergency contact person and number (required)</li>
                <li><strong>philsys_card_no</strong> - PhilSys card number (optional)</li>
                <li><strong>last_name, first_name, suffix, middle_name</strong> - Member personal name fields (required first and last)</li>
                <li><strong>birth_date</strong> - Birth date mm/dd/yyyy (required)</li>
                <li><strong>birth_place</strong> - Birth place (optional)</li>
                <li><strong>sex</strong> - male, female, or other (required)</li>
                <li><strong>civil_status</strong> - Civil status (optional)</li>
                <li><strong>religion</strong> - Religion (optional)</li>
                <li><strong>residence_address</strong> - Residence address (optional)</li>
                <li><strong>citizenship</strong> - Citizenship (optional)</li>
                <li><strong>profession</strong> - Profession/occupation (optional)</li>
                <li><strong>education_level</strong> - Highest educational attainment (optional)</li>
                <li><strong>is_graduate</strong> - true/false (optional)</li>
                <li><strong>is_pwd</strong> - true/false (optional)</li>
                <li><strong>date_accomplished</strong> - Date accomplished (optional)</li>
                <li><strong>name_signature</strong> - Name/Signature of person accomplishing form (optional)</li>
                <li><strong>attested_by</strong> - Attested by Barangay Secretary (optional)</li>
                <li><strong>left_thumbmark</strong>, <strong>right_thumbmark</strong> - true/false (optional)</li>
                <li><strong>age</strong> - Member age (required 0-150)</li>
            </ul>
            <p><strong>Important:</strong> Each row represents one family member. Household information should be repeated for each member of the same household.</p>
            <p><strong>Example CSV (RBI Form B enriched, one row per person):</strong></p>
            <pre>household_id,address,purok,region,province,city_mun,barangay,household_number,headname,contact_number,emergency_contact,philsys_card_no,last_name,suffix,first_name,middle_name,birth_date,birth_place,sex,civil_status,religion,residence_address,citizenship,profession,education_level,is_graduate,is_pwd,email,date_accomplished,name_signature,attested_by,left_thumbmark,right_thumbmark,age
HH-001,123 Main St,Sitio A,Region IV-A,Province X,City Y,Barangay Z,HH-001,Rex Martinez,09171234567,Juan Dela Cruz 09181234567,1234-5678-9012-3456,Martinez,,Rex,,01/01/1980,City Y,male,married,Catholic,123 Main St,Filipino,Farmer,College,true,false,rex@example.com,03/01/2025,Rex Martinez,Barangay Secretary,true,true,45
HH-001,123 Main St,Sitio A,Region IV-A,Province X,City Y,Barangay Z,HH-001,Rex Martinez,09171234567,Juan Dela Cruz 09181234567,1234-5678-9012-3456,Martinez,,Gwen,,01/02/1982,City Y,female,married,Catholic,123 Main St,Filipino,Teacher,College,true,false,gwen@example.com,03/01/2025,Gwen Martinez,Barangay Secretary,false,false,42
HH-001,123 Main St,Sitio A,Region IV-A,Province X,City Y,Barangay Z,HH-001,Rex Martinez,09171234567,Juan Dela Cruz 09181234567,1234-5678-9012-3456,Martinez,,Klint,,01/01/2006,City Y,male,single,Catholic,123 Main St,Filipino,Student,High School,false,false,klint@example.com,03/01/2025,Klint Martinez,Barangay Secretary,false,false,18
HH-001,123 Main St,Sitio A,Region IV-A,Province X,City Y,Barangay Z,HH-001,Rex Martinez,09171234567,Juan Dela Cruz 09181234567,1234-5678-9012-3456,Martinez,,Ronald,,01/01/2008,City Y,male,single,Catholic,123 Main St,Filipino,Student,High School,false,false,ronald@example.com,03/01/2025,Ronald Martinez,Barangay Secretary,false,false,16</pre>
            <p><strong>Note:</strong> The household data (columns 1-6) should be identical for all members of the same household.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('households.upload.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Select CSV file</label>
                <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt" required>
                <small class="form-text text-muted">Accepted formats: .csv, .txt</small>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="{{ route('households.index') }}" class="btn btn-secondary">Back</a>
        </form>

        <hr>
        <h5>Notes:</h5>
        <ul>
            <li>All fields are required</li>
            <li>household_id must be unique (will skip duplicates)</li>
            <li>Ensure CSV headers exactly match the format above</li>
            <li>Members can be added separately after household creation</li>
        </ul>
    </div>
@endsection