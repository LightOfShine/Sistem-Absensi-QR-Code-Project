# 📚 PENJELASAN ALUR SISTEM Laravel - E-ABSENSI SISWA
## Route → Controller → Model → View

---

## 🎯 RINGKASAN SISTEM

Project **E-Absensi Siswa** adalah sistem manajemen kehadiran siswa berbasis Laravel yang menggunakan **pola MVC (Model-View-Controller)**.

Alur kerja sistemnya:
```
USER MENGAKSES URL 
    ↓
ROUTE (web.php) mencocokkan URL
    ↓
CONTROLLER menjalankan logika bisnis
    ↓
MODEL berinteraksi dengan database
    ↓
VIEW menampilkan data ke user
```

---

## 1️⃣ LAYER 1: ROUTE (routes/web.php)

### Apa itu Route?
**Route** adalah "peta jalan" aplikasi yang menentukan:
- URL mana yang dapat diakses
- Controller mana yang menangani permintaan
- Middleware apa yang diperlukan

### Contoh dalam Sistem:

```php
// ROUTE: Tampilkan daftar Wali Kelas
Route::get('/admin/teachers', [TeacherController::class, 'index'])
    ->name('teachers.index');

// PENJELASAN:
// - GET /admin/teachers  : URL yang diakses user
// - [TeacherController::class, 'index'] : Controller & method yang dijalankan
// - ->name('teachers.index') : Nama route untuk digunakan di view/redirect
```

### Struktur Route dalam Project:

```php
// 1. ROUTE PUBLIK
Route::get('/', [LandingController::class, 'index'])->name('landing');

// 2. ROUTE PROTECTED (Harus login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

// 3. ROUTE ADMIN (Harus login + role super_admin)
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    
    // CRUD Wali Kelas
    Route::resource('teachers', TeacherController::class)->names('teachers');
    // Menghasilkan routes:
    // GET    /admin/teachers             → teachers.index (lihat daftar)
    // GET    /admin/teachers/create      → teachers.create (form tambah)
    // POST   /admin/teachers             → teachers.store (simpan ke DB)
    // GET    /admin/teachers/{id}/edit   → teachers.edit (form edit)
    // PUT    /admin/teachers/{id}        → teachers.update (update DB)
    // DELETE /admin/teachers/{id}        → teachers.destroy (hapus dari DB)
    
    // Siswa (Students)
    Route::resource('students', StudentController::class);
    
    // Kelas (Classes)
    Route::resource('classes', ClassController::class);
});

// 4. ROUTE WALI KELAS (guru)
Route::middleware(['auth', 'role:wali_kelas'])->prefix('wali-kelas')->group(function () {
    Route::get('dashboard', [WaliKelasDashboardController::class, 'index'])
        ->name('wali-kelas.dashboard');
});

// 5. ROUTE ORANG TUA (Parent)
Route::middleware(['auth', 'role:parent'])->group(function () {
    Route::get('parent/dashboard', [ParentController::class, 'dashboard'])
        ->name('parent.dashboard');
});
```

---

## 2️⃣ LAYER 2: CONTROLLER (app/Http/Controllers/)

### Apa itu Controller?
**Controller** adalah "pengatur lalu lintas" yang:
- Menerima request dari route
- Memproses logika bisnis
- Berkomunikasi dengan model
- Mengirim data ke view

### Contoh Controller: TeacherController

```php
namespace App\Http\Controllers\Admin;

class TeacherController extends Controller
{
    // 1. INDEX - Tampilkan daftar
    public function index()
    {
        // Query database melalui MODEL
        $teachers = User::where('role', 'wali_kelas')
            ->with('homeroomTeacher.class')
            ->get();
        
        // Return view dengan data
        return view('admin.teachers.index', compact('teachers'));
    }

    // 2. CREATE - Tampilkan form tambah
    public function create()
    {
        $availableClasses = ClassModel::whereNotIn('id', $assignedClassIds)
            ->get();
        
        return view('admin.teachers.create', compact('availableClasses'));
    }

    // 3. STORE - Simpan data baru ke database
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        // Transaksi: Simpan user dan homeroom teacher
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'wali_kelas',
            ]);

            if ($request->class_id) {
                HomeroomTeacher::create([
                    'user_id' => $user->id,
                    'class_id' => $request->class_id,
                ]);
            }
            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'Wali Kelas berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // 4. EDIT - Tampilkan form edit
    public function edit(User $teacher)
    {
        $availableClasses = ClassModel::whereNotIn('id', $assignedClassIds)
            ->orWhere('id', $teacher->homeroomTeacher->class_id ?? null)
            ->get();
        
        return view('admin.teachers.edit', compact('teacher', 'availableClasses'));
    }

    // 5. UPDATE - Update data
    public function update(Request $request, User $teacher)
    {
        $teacher->update($request->validated());
        return redirect()->route('teachers.index')
            ->with('success', 'Wali Kelas berhasil diperbarui!');
    }

    // 6. DESTROY - Hapus data
    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')
            ->with('success', 'Wali Kelas berhasil dihapus!');
    }
}
```

### Alur Controller:
```
1. TERIMA REQUEST
   ↓
2. VALIDASI INPUT (Request validation)
   ↓
3. PROSES LOGIKA BISNIS
   - Query Model
   - Transform data
   - Handle errors
   ↓
4. SIMPAN/UPDATE DATABASE
   ↓
5. RETURN RESPONSE
   - View (HTML)
   - Redirect
   - JSON (API)
```

---

## 3️⃣ LAYER 3: MODEL (app/Models/)

### Apa itu Model?
**Model** adalah representasi tabel database yang:
- Mendefinisikan struktur data
- Mengelola relasi antar tabel
- Menyediakan query builder
- Melakukan validasi data

### Contoh Model: User

```php
namespace App\Models;

class User extends Authenticatable
{
    use HasFactory;

    // Field yang dapat diisi massal
    protected $fillable = [
        'name',
        'email', 
        'password',
        'role', // super_admin, wali_kelas, parent, admin
    ];

    // Cast tipe data
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // RELASI: User -> HomeroomTeacher (1:1)
    public function homeroomTeacher()
    {
        return $this->hasOne(HomeroomTeacher::class, 'user_id');
    }

    // RELASI: User -> Class (through HomeroomTeacher)
    public function assignedClass()
    {
        return $this->hasOneThrough(
            ClassModel::class,
            HomeroomTeacher::class,
            'user_id',
            'id',
            'id',
            'class_id'
        );
    }
}
```

### Contoh Model: HomeroomTeacher

```php
class HomeroomTeacher extends Model
{
    protected $fillable = ['user_id', 'class_id'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Class
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
```

### Contoh Model: Student

```php
class Student extends Model
{
    protected $fillable = [
        'nisn', 'nis', 'name', 'email', 
        'gender', 'class_id', 'phone_number',
        'address', 'birth_place', 'birth_date',
        'photo', 'status', 'barcode_data',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Auto-generate barcode sebelum simpan
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($student) {
            if (empty($student->barcode_data)) {
                $student->barcode_data = Str::uuid()->toString();
            }
        });
    }

    // Relasi ke Class
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // Relasi ke Absence
    public function absences()
    {
        return $this->hasMany(Absence::class, 'student_id');
    }
}
```

### Hubungan Antar Model:

```
┌─────────────────────────────────────────────────┐
│                    DATABASE                      │
├─────────────────────────────────────────────────┤
│ users (ID, name, email, role, ...)              │
│ classes (ID, name, grade, ...)                  │
│ homeroom_teachers (ID, user_id, class_id)       │
│ students (ID, nisn, name, class_id, ...)        │
│ absences (ID, student_id, status, ...)          │
└─────────────────────────────────────────────────┘
        ↑               ↑               ↑
        │               │               │
        │ hasOne        │ belongsTo     │ hasMany
        │               │               │
┌───────┴─┐  ┌──────────┴────┐  ┌──────┴───────┐
│   User   │  │  HomeroomTeach│  │    Student   │
├─────────┼──┼───────────────┤  ├──────────────┤
│ id      │  │ id            │  │ id           │
│ name    │  │ user_id ─────→│  │ class_id ────┘
│ email   │  │ class_id ┐    │  │ name
│ role    │  │          └────┼──┘ nisn
└─────────┘  └────────────────┘  │ absences()
                                  └──────────────┘
```

---

## 4️⃣ LAYER 4: VIEW (resources/views/)

### Apa itu View?
**View** adalah template HTML yang:
- Menampilkan data dari controller
- Menggunakan Blade templating engine
- Menerima variabel dari controller

### Contoh View: index.blade.php (Daftar Wali Kelas)

```blade
<!-- File: resources/views/admin/teachers/index.blade.php -->

@extends('layouts.adminlte')

@section('title', 'Manajemen Data Wali Kelas')

@section('content')

<div class="space-y-6">
    
    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">Daftar Wali Kelas</h2>
            <p>Total <strong>{{ count($teachers) }}</strong> Wali Kelas</p>
        </div>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Wali Kelas
        </a>
    </div>

    {{-- TABEL --}}
    <table class="w-full border">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Nama</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Kelas Diampu</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- LOOP DATA DARI CONTROLLER --}}
            @forelse($teachers as $teacher)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-center">{{ $loop->iteration }}</td>
                    
                    <td class="px-6 py-4">
                        <strong>{{ $teacher->name }}</strong>
                    </td>
                    
                    <td class="px-6 py-4">{{ $teacher->email }}</td>
                    
                    {{-- CONDITIONAL: Cek apakah ada kelas --}}
                    <td class="px-6 py-4">
                        @if($teacher->homeroomTeacher && $teacher->homeroomTeacher->class)
                            <span class="badge badge-info">
                                {{ $teacher->homeroomTeacher->class->name }}
                            </span>
                        @else
                            <span class="badge badge-secondary">Belum Diampu</span>
                        @endif
                    </td>
                    
                    {{-- ACTION BUTTONS --}}
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('teachers.edit', $teacher->id) }}" 
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        
                        <button onclick="confirmDelete({{ $teacher->id }}, '{{ $teacher->name }}')"
                                class="btn btn-sm btn-danger">
                            Hapus
                        </button>

                        <form id="delete-form-{{ $teacher->id }}" 
                              action="{{ route('teachers.destroy', $teacher->id) }}"
                              method="POST" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
            
            {{-- JIKA DATA KOSONG --}}
            @empty
                <tr>
                    <td colspan="5" class="text-center py-8">
                        <p class="text-gray-500">Belum ada Wali Kelas</p>
                        <a href="{{ route('teachers.create') }}" class="btn btn-primary mt-4">
                            Tambah Data
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('js')
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Wali Kelas?',
        text: `Yakin ingin menghapus "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Hapus',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}
</script>
@endsection
```

### Blade Syntax yang Digunakan:

| Syntax | Fungsi |
|--------|--------|
| `@extends('layouts.adminlte')` | Inheritance layout |
| `@section('content')` | Bagian yang bisa di-override |
| `{{ $variable }}` | Output variable (escaped) |
| `{!! $html !!}` | Output HTML (tidak di-escape) |
| `@if()` `@else` `@endif` | Conditional |
| `@foreach()` `@endforeach` | Loop |
| `@forelse()` `@empty` `@endforelse` | Loop dengan fallback |
| `{{ route('name') }}` | Generate URL dari route name |
| `@csrf` `@method('DELETE')` | CSRF token & HTTP method |

---

## 🔄 ALUR LENGKAP: Contoh Kasus

### **KASUS: Admin menambah Wali Kelas baru**

#### STEP 1: User Klik "Tambah Wali Kelas"
```html
<!-- Button di index.blade.php -->
<a href="{{ route('teachers.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Tambah Wali Kelas
</a>
```

#### STEP 2: Route Mencocokkan URL
```php
// routes/web.php
Route::resource('teachers', TeacherController::class);
// Menghasilkan:
// GET /admin/teachers/create → TeacherController@create
```

#### STEP 3: Controller (Create Method)
```php
public function create()
{
    // QUERY MODEL: Ambil kelas yang belum punya wali
    $assignedClassIds = HomeroomTeacher::pluck('class_id')->toArray();
    $availableClasses = ClassModel::whereNotIn('id', $assignedClassIds)
                                  ->orderBy('grade')
                                  ->get();

    // RETURN VIEW: Kirim data ke template
    return view('admin.teachers.create', compact('availableClasses'));
}
```

#### STEP 4: View (Form)
```blade
<!-- resources/views/admin/teachers/create.blade.php -->
<form action="{{ route('teachers.store') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label>Nama Wali Kelas</label>
        <input type="text" name="name" required>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    
    <div class="form-group">
        <label>Pilih Kelas (Opsional)</label>
        <select name="class_id">
            <option value="">-- Belum Diampu Kelas --</option>
            @foreach($availableClasses as $class)
                <option value="{{ $class->id }}">
                    {{ $class->name }} (Kelas {{ $class->grade }})
                </option>
            @endforeach
        </select>
    </div>
    
    <button type="submit" class="btn btn-success">Simpan</button>
</form>
```

#### STEP 5: User Submit Form
```
POST /admin/teachers
Data: {
    name: "Budi Santoso",
    email: "budi@sekolah.com",
    password: "rahasia123",
    class_id: 5
}
```

#### STEP 6: Route → Controller (Store Method)
```php
// routes/web.php
// POST /admin/teachers → TeacherController@store
```

#### STEP 7: Controller (Store Method) - VALIDASI & SIMPAN
```php
public function store(Request $request)
{
    // 1. VALIDASI
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'class_id' => 'nullable|unique:homeroom_teachers,class_id|exists:classes,id',
    ]);

    try {
        // 2. TRANSAKSI DATABASE
        DB::beginTransaction();

        // 3. SIMPAN KE TABEL users (MODEL: User)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'wali_kelas',
        ]);

        // 4. SIMPAN KE TABEL homeroom_teachers (MODEL: HomeroomTeacher)
        if ($request->class_id) {
            HomeroomTeacher::create([
                'user_id' => $user->id,
                'class_id' => $request->class_id,
            ]);
        }

        DB::commit();

        // 5. REDIRECT + SESSION MESSAGE
        return redirect()->route('teachers.index')
                         ->with('success', 'Akun Wali Kelas berhasil ditambahkan!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
                         ->withInput()
                         ->with('error', 'Gagal: ' . $e->getMessage());
    }
}
```

#### STEP 8: Controller Redirect ke Index
```php
return redirect()->route('teachers.index')
                 ->with('success', 'Berhasil!');
```

#### STEP 9: Route → Controller (Index Method)
```php
public function index()
{
    // QUERY DARI MODELS
    $teachers = User::where('role', 'wali_kelas')
        ->with('homeroomTeacher.class')
        ->get();
    
    // RETURN VIEW
    return view('admin.teachers.index', compact('teachers'));
}
```

#### STEP 10: View Menampilkan Data
```blade
<table>
    @foreach($teachers as $teacher)
        <tr>
            <td>{{ $teacher->name }}</td>
            <td>{{ $teacher->homeroomTeacher->class->name }}</td>
            <td>
                <a href="{{ route('teachers.edit', $teacher->id) }}">Edit</a>
                <button onclick="confirmDelete({{ $teacher->id }})">Hapus</button>
            </td>
        </tr>
    @endforeach
</table>
```

#### STEP 11: Browser Menampilkan Halaman
```
✅ Halaman berhasil dimuat dengan data terbaru
✅ Message "Akun Wali Kelas berhasil ditambahkan!" muncul
```

---

## 📊 DIAGRAM ALUR LENGKAP

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                             │
│        (Browser - index.blade.php atau create.blade.php)         │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ User klik button / submit form
┌─────────────────────────────────────────────────────────────────┐
│                  ROUTE (routes/web.php)                           │
│  GET  /admin/teachers/create   → TeacherController@create        │
│  POST /admin/teachers          → TeacherController@store         │
│  GET  /admin/teachers          → TeacherController@index         │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ Router menemukan controller & method
┌─────────────────────────────────────────────────────────────────┐
│            CONTROLLER (App\Http\Controllers\Admin)               │
│                  TeacherController.php                            │
│                                                                    │
│  ├─ create()    : Prepare data → Query Model → Return View      │
│  ├─ store()     : Validate → Transform → Save Model → Redirect  │
│  ├─ index()     : Query Model → Return View                     │
│  ├─ edit()      : Prepare data → Query Model → Return View      │
│  ├─ update()    : Validate → Update Model → Redirect            │
│  └─ destroy()   : Delete Model → Redirect                       │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ Controller memanggil Model
┌─────────────────────────────────────────────────────────────────┐
│              MODELS (App\Models)                                  │
│                                                                    │
│  ├─ User                                                          │
│  │   └─ Relasi: hasOne(HomeroomTeacher)                         │
│  │   └─ Relasi: hasOneThrough(ClassModel)                       │
│  │                                                                │
│  ├─ HomeroomTeacher                                              │
│  │   └─ Relasi: belongsTo(User)                                 │
│  │   └─ Relasi: belongsTo(ClassModel)                           │
│  │                                                                │
│  ├─ ClassModel                                                   │
│  │   └─ Relasi: hasMany(HomeroomTeacher)                        │
│  │   └─ Relasi: hasMany(Student)                                │
│  │                                                                │
│  └─ Student                                                      │
│      └─ Relasi: belongsTo(ClassModel)                           │
│      └─ Relasi: hasMany(Absence)                                │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ Model query ke Database
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE                                     │
│        (Laravel SQLite/MySQL/PostgreSQL)                         │
│                                                                    │
│  ├─ users (id, name, email, password, role)                     │
│  ├─ homeroom_teachers (id, user_id, class_id)                   │
│  ├─ classes (id, name, grade)                                   │
│  ├─ students (id, nisn, name, class_id)                         │
│  ├─ absences (id, student_id, status, date)                     │
│  └─ ... (tables lainnya)                                         │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ Return data ke Controller
┌─────────────────────────────────────────────────────────────────┐
│                      CONTROLLER                                   │
│          (Process data + Transform jika perlu)                   │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ Pass data ke View
┌─────────────────────────────────────────────────────────────────┐
│             VIEW (resources/views/admin)                          │
│                                                                    │
│  ├─ layouts/app.blade.php (Parent layout)                       │
│  ├─ admin/                                                       │
│  │   └─ teachers/                                               │
│  │       ├─ index.blade.php    (Display daftar)                │
│  │       ├─ create.blade.php   (Form tambah)                   │
│  │       ├─ edit.blade.php     (Form edit)                     │
│  │       └─ show.blade.php     (Detail single)                 │
│  │                                                               │
│  ├─ Blade Directives:                                           │
│  │  @extends(), @section(), {{ }}, @if, @foreach,              │
│  │  @forelse, {{ route() }}, {{ csrf_token() }}                │
│  └─                                                              │
└────────────────────────┬──────────────────────────────────────────┘
                         │
                         ↓ Render HTML
┌─────────────────────────────────────────────────────────────────┐
│                   RENDERED HTML                                   │
│         (Browser menampilkan halaman final)                      │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Manajemen Data Wali Kelas          [+ Tambah Wali Kelas]│  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │ No │ Nama            │ Email            │ Kelas │ Aksi  │  │
│  ├────┼─────────────────┼──────────────────┼───────┼────────┤  │
│  │ 1  │ Budi Santoso    │ budi@sekolah.com │ 7 A   │[Edit] │  │
│  │ 2  │ Siti Nurhaliza  │ siti@sekolah.com │ 8 B   │[Edit] │  │
│  │ 3  │ Ahmad Hidayat   │ ahmad@sekolah.com│ 9 C   │[Edit] │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔐 SECURITY FEATURES

1. **Authentication Middleware**
   ```php
   Route::middleware('auth')->group(...)
   // Hanya user yang login yang bisa akses
   ```

2. **Authorization (Role-based)**
   ```php
   Route::middleware(['auth', 'role:super_admin'])->group(...)
   // Hanya super_admin yang bisa akses admin routes
   ```

3. **CSRF Protection**
   ```blade
   <form method="POST">
       @csrf  <!-- Automatically added -->
       ...
   </form>
   ```

4. **Password Hashing**
   ```php
   'password' => Hash::make($request->password)
   // Password di-hash sebelum disimpan
   ```

5. **Database Transaction**
   ```php
   DB::beginTransaction();
   // Jika ada error, semua query di-rollback
   DB::rollBack();
   ```

---

## 📝 RINGKASAN ALUR MVC

| Layer | File | Fungsi |
|-------|------|--------|
| **Route** | `routes/web.php` | Define URL endpoints |
| **Controller** | `app/Http/Controllers/` | Business logic + Request handling |
| **Model** | `app/Models/` | Database interactions + Relationships |
| **View** | `resources/views/` | HTML templates + Data display |
| **Database** | `database/migrations/` | Schema definition |

---

## 🎓 Kesimpulan

**Alur Kerja Sistem E-Absensi:**

1. **User** mengakses URL → 
2. **Route** mencocokkan dan memanggil **Controller** → 
3. **Controller** memanggil **Model** untuk query database → 
4. **Model** mengambil data dari **Database** → 
5. **Controller** menerima data dan melewatkannya ke **View** → 
6. **View** merender HTML dan menampilkannya ke **Browser** →
7. **User** melihat halaman final

Pola ini memastikan **separation of concerns** dan **maintainability** kode yang baik! ✨
