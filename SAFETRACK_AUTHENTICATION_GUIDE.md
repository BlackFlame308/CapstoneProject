# SafeTrack - Role-Based Authentication System

## System Setup Complete ✅

The SafeTrack double-authentication role-based system has been successfully created with the following features:

---

## Test Account Credentials

### 1. **Super Admin (Barangay Captain)**
- **Email:** `captain@safetrack.local`
- **Password:** `password`
- **Permissions:** Full system access
  - Create, edit, delete households
  - Manage users and assign roles
  - Create and manage roles
  - Create and manage permissions
  - Manage responders and evacuation officers
  - Create disaster events
  - View analytics
  - **CANNOT**: Delete own account

### 2. **Encoder**
- **Email:** `encoder@safetrack.local`
- **Password:** `password`
- **Permissions:**
  - Add new households ✅
  - Update household information ✅
  - Manage users (add, edit) ✅
  - View analytics ✅
  - **CANNOT**: Delete households ❌

### 3. **Viewer**
- **Email:** `viewer@safetrack.local`
- **Password:** `password`
- **Permissions:**
  - View analytics only

---

## System Features

### 1. **Double Authentication**
- Email and password login
- Session-based authentication
- Remember me functionality
- Automatic logout on browser close

### 2. **Role Management**
The Super Admin can:
- ✅ Create new roles
- ✅ Edit existing roles (except Super Admin)
- ✅ Delete roles (only if no users assigned)
- ✅ Assign permissions to roles dynamically
- ✅ Set custom permissions for each role

### 3. **Permission System**
Pre-configured permissions:
- `add_households` - Can add new households
- `update_households` - Can update household information
- `delete_households` - Can delete household records
- `manage_users` - Can manage user accounts
- `manage_responders` - Can manage responders
- `manage_evacuation_officers` - Can manage evacuation officers
- `view_analytics` - Can view system analytics
- `create_disaster_events` - Can create disaster events

**Super Admin can create additional permissions dynamically!**

### 4. **Household Management**
Based on user roles:
- **Super Admin**: Full access (add, edit, delete)
- **Encoder**: Can add and edit households (cannot delete)
- **Viewer**: Cannot access

### 5. **User Management**
Super Admin can:
- ✅ Create new users with specific roles
- ✅ Edit user information
- ✅ Edit user passwords
- ✅ Change user roles
- ✅ Delete users (except own account)
- ❌ Users cannot change their own role to prevent privilege escalation

### 6. **Account Security**
- Cannot delete own account (prevents admin lockout)
- Cannot change own role (prevents privilege escalation)
- Only Super Admin can assign Super Admin roles
- Password hashing with bcrypt

---

## Navigation Routes

After login, you'll see a dashboard with sidebar navigation:

### For All Users
- Dashboard
- Households
- Responders
- Disaster Events
- Analytics

### For Super Admin Only (Admin Section)
- Users Management
- Roles Management
- Permissions Management

---

## How to Use the System

### 1. **Login**
```
URL: http://127.0.0.1:8000/login
Use one of the test credentials above
```

### 2. **Register New User (Super Admin Only)**
```
URL: http://127.0.0.1:8000/register
Note: Only Super Admin can create Super Admin accounts
Other roles can be selected during registration
```

### 3. **Create a New Role (Super Admin)**
Steps:
1. Login as Super Admin (Captain)
2. Go to Admin → Roles
3. Click "Create New Role"
4. Enter role name and description
5. Select permissions to assign
6. Click "Create Role"

Example new roles you can create:
- **Data Reviewer**: view_analytics
- **Evacuation Manager**: manage_evacuation_officers, view_analytics
- **Responder Coordinator**: manage_responders

### 4. **Manage Users (Super Admin)**
Steps:
1. Login as Super Admin
2. Go to Admin → Users
3. Click "Add New User"
4. Enter user details and select role
5. Users can then login with their credentials

### 5. **Add Household (Encoder)**
Steps:
1. Login as Encoder
2. Go to Households
3. Click "Add Household"
4. Fill in household details and member information
5. Analytics update automatically

---

## Key Security Features

✅ **Middleware Protection**
- All routes except login/register require authentication
- Admin routes require Super Admin role
- Household operations check specific permissions

✅ **Account Protection**
- Cannot delete own account
- Cannot change own role
- Super Admin lockout prevention

✅ **Permission Enforcement**
- Encoders blocked from household deletion
- Only authorized users can access admin features
- Role-based view filtering

✅ **Database Integrity**
- Foreign key constraints
- Unique constraints on roles/permissions
- Cascade deletes handled properly

---

## Permissions Matrix

| Feature | Super Admin | Encoder | Viewer |
|---------|-----------|---------|--------|
| Add Household | ✅ | ✅ | ❌ |
| Edit Household | ✅ | ✅ | ❌ |
| Delete Household | ✅ | ❌ | ❌ |
| Manage Users | ✅ | ❌ | ❌ |
| Manage Roles | ✅ | ❌ | ❌ |
| Manage Permissions | ✅ | ❌ | ❌ |
| View Analytics | ✅ | ✅ | ✅ |
| Create Disaster Events | ✅ | ❌ | ❌ |

---

## Extending the System

### To Create a New Role with Custom Permissions:

1. **Via Super Admin Panel:**
   - Admin → Roles → Create New Role
   - Select desired permissions
   - Save

2. **Or via Code (Seeder):**
```php
$role = Role::create([
    'name' => 'Custom Role',
    'description' => 'Does custom things',
    'guard_name' => 'web'
]);

$role->permissions()->attach(Permission::where('name', 'add_households')->first()->id);
```

### To Create a New Permission:

1. **Via Super Admin Panel:**
   - Admin → Permissions → Create New Permission
   - Enter permission name (use snake_case)
   - Add description
   - Save

2. **Then assign to roles via Roles → Edit**

---

## Troubleshooting

### Can't Login?
- Verify email and password match test credentials
- Clear browser cache and cookies
- Check database connection in `.env`

### Permission Denied Error?
- Check user role in Users → Edit
- Verify role has required permissions in Roles → Edit
- Only Super Admin can access Admin section

### Can't Delete User/Role?
- Cannot delete Super Admin accounts (except as Super Admin)
- Cannot delete roles with assigned users
- Cannot delete permissions assigned to roles

---

## Default Setup Summary

**Database:** CapstoneDb on localhost
**Tables Created:**
- users
- roles
- permissions
- role_permissions
- households
- members
- responders
- evacuation_officers
- disaster_events
- analytics
- ... (and other Laravel tables)

**Initial Roles:**
1. Super Admin (all permissions)
2. Encoder (add/edit households, manage users)
3. Viewer (analytics only)

**Test Users created:**
- captain@safetrack.local (Super Admin)
- encoder@safetrack.local (Encoder)
- viewer@safetrack.local (Viewer)

---

## Next Steps

1. ✅ Start Laravel development server: `php artisan serve`
2. ✅ Visit: http://127.0.0.1:8000/login
3. ✅ Login with test credentials
4. ✅ Test different user roles
5. ✅ Create custom roles as needed
6. ✅ Add real household data
7. ✅ Generate analytics reports

---

**SafeTrack is now ready for deployment! 🎉**
