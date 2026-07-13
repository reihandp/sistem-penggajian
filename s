warning: in the working copy of 'resources/views/penggajian/_form.blade.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/penggajian/index.blade.php', CRLF will be replaced by LF the next time Git touches it
[1mdiff --git a/app/Http/Controllers/EmployeeSalaryController.php b/app/Http/Controllers/EmployeeSalaryController.php[m
[1mindex 60f43ba..abb758e 100644[m
[1m--- a/app/Http/Controllers/EmployeeSalaryController.php[m
[1m+++ b/app/Http/Controllers/EmployeeSalaryController.php[m
[36m@@ -3,6 +3,7 @@[m
 namespace App\Http\Controllers;[m
 [m
 use App\Models\EmployeeSalary;[m
[32m+[m[32muse App\Models\Jabatan;[m
 use Illuminate\Http\Request;[m
 use Illuminate\Http\RedirectResponse;[m
 use Illuminate\View\View;[m
[36m@@ -19,6 +20,7 @@[m [mpublic function index(): View[m
         $salaries = EmployeeSalary::when($search, function ($query, $search) {[m
             $query->where('nama', 'like', $search . '%');[m
         })[m
[32m+[m[32m            ->with('jabatan')[m
             ->latest()[m
             ->paginate(10)[m
             ->withQueryString();[m
[36m@@ -31,7 +33,8 @@[m [mpublic function index(): View[m
      */[m
     public function create(): View[m
     {[m
[31m-        return view('penggajian.create');[m
[32m+[m[32m        $jabatan = Jabatan::orderBy('nama_jabatan')->get();[m
[32m+[m[32m        return view('penggajian.create', compact('jabatan'));[m
     }[m
 [m
     /**[m
[36m@@ -45,6 +48,7 @@[m [mpublic function store(Request $request): RedirectResponse[m
             'usia' => ['required', 'integer', 'min:0'],[m
             'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],[m
             'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],[m
[32m+[m[32m            'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],[m
         ]);[m
 [m
         EmployeeSalary::create($validated);[m
[36m@@ -67,7 +71,8 @@[m [mpublic function show(EmployeeSalary $employeeSalary)[m
      */[m
     public function edit(EmployeeSalary $employeeSalary): View[m
     {[m
[31m-        return view('penggajian.edit', compact('employeeSalary'));[m
[32m+[m[32m        $jabatan = Jabatan::orderBy('nama_jabatan')->get();[m
[32m+[m[32m        return view('penggajian.edit', compact('employeeSalary', 'jabatan'));[m
     }[m
 [m
     /**[m
[36m@@ -81,6 +86,7 @@[m [mpublic function update(Request $request, EmployeeSalary $employeeSalary): Redire[m
             'usia' => ['required', 'integer', 'min:0'],[m
             'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],[m
             'gaji_per_bulan_rp' => ['required', 'integer', 'min:0'],[m
[32m+[m[32m            'id_jabatan' => ['nullable', 'integer', 'exists:data_jabatan,id_jabatan'],[m
         ]);[m
 [m
         $employeeSalary->update($validated);[m
[1mdiff --git a/app/Models/EmployeeSalary.php b/app/Models/EmployeeSalary.php[m
[1mindex 28a86da..ec30da7 100644[m
[1m--- a/app/Models/EmployeeSalary.php[m
[1m+++ b/app/Models/EmployeeSalary.php[m
[36m@@ -6,8 +6,15 @@[m
 [m
 class EmployeeSalary extends Model[m
 {[m
[31m-    protected $table = 'gaji_karyawan_indonesia';[m
[31m-[m
[32m+[m[32m    protected $table = 'gaji_karyawan_indonesia_updated';[m
     // Mengizinkan kolom-kolom diisi secara massal[m
[31m-    protected $fillable = ['nama', 'pengalaman_kerja_tahun', 'usia', 'jenis_kelamin', 'gaji_per_bulan_rp'];[m
[32m+[m[32m    protected $fillable = ['id', 'nama', 'pengalaman_kerja_tahun', 'usia', 'jenis_kelamin', 'gaji_per_bulan_rp', 'id_jabatan'];[m
[32m+[m
[32m+[m[32m    public $incrementing = true;[m
[32m+[m[32m    protected $keyType = 'int';[m
[32m+[m
[32m+[m[32m    public function jabatan()[m
[32m+[m[32m    {[m
[32m+[m[32m        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');[m
[32m+[m[32m    }[m
 }[m
[1mdiff --git a/database/seeders/DatabaseSeeder.php b/database/seeders/DatabaseSeeder.php[m
[1mindex d101730..58f6de9 100644[m
[1m--- a/database/seeders/DatabaseSeeder.php[m
[1m+++ b/database/seeders/DatabaseSeeder.php[m
[36m@@ -22,6 +22,8 @@[m [mpublic function run(): void[m
             'email' => 'test@example.com',[m
         ]);[m
 [m
[32m+[m[32m        // Seed jabatan first so FK references exist[m
[32m+[m[32m        $this->call(JabatanSeeder::class);[m
         $this->call(EmployeeSalarySeeder::class);[m
     }[m
 }[m
[1mdiff --git a/database/seeders/EmployeeSalarySeeder.php b/database/seeders/EmployeeSalarySeeder.php[m
[1mindex ef43a41..bdecd2c 100644[m
[1m--- a/database/seeders/EmployeeSalarySeeder.php[m
[1m+++ b/database/seeders/EmployeeSalarySeeder.php[m
[36m@@ -9,21 +9,35 @@[m [mclass EmployeeSalarySeeder extends Seeder[m
 {[m
     public function run(): void[m
     {[m
[31m-        $csvFile = fopen(database_path('seeders/Gaji_Karyawan_Indonesia.csv'), 'r');[m
[32m+[m[32m        // New CSV format: (ID,Nama,Pengalaman_Kerja_Tahun,Usia,Jenis_Kelamin,Gaji_Per_Bulan_Rp,id_jabatan)[m
[32m+[m[32m        $csvPath = database_path('seeders/gaji_karyawan_indonesia_updated.csv');[m
[32m+[m[32m        if (!file_exists($csvPath)) {[m
[32m+[m[32m            // fallback to old filename if updated not present[m
[32m+[m[32m            $csvPath = database_path('seeders/Gaji_Karyawan_Indonesia.csv');[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $csvFile = fopen($csvPath, 'r');[m
         $firstRow = true;[m
[31m-        [m
[31m-        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {[m
[32m+[m
[32m+[m[32m        while (($data = fgetcsv($csvFile, 2000, ',')) !== FALSE) {[m
             if (!$firstRow) {[m
[31m-                EmployeeSalary::create([[m
[31m-                    'nama' => $data[1],[m
[31m-                    'pengalaman_kerja_tahun' => (int) $data[2],[m
[31m-                    'usia' => (int) $data[3],[m
[31m-                    'jenis_kelamin' => $data[4],[m
[31m-                    'gaji_per_bulan_rp' => (int) $data[5],[m
[32m+[m[32m                // Use DB insert to allow explicit ID if present in CSV[m
[32m+[m[32m                \Illuminate\Support\Facades\DB::table('gaji_karyawan_indonesia_updated')->insert([[m
[32m+[m[32m                    // CSV columns: 0:id,1:nama,2:pengalaman,3:usia,4:jenis_kelamin,5:gaji,6:id_jabatan[m
[32m+[m[32m                    'id' => isset($data[0]) && is_numeric($data[0]) ? (int)$data[0] : null,[m
[32m+[m[32m                    'nama' => $data[1] ?? null,[m
[32m+[m[32m                    'pengalaman_kerja_tahun' => isset($data[2]) ? (int)$data[2] : null,[m
[32m+[m[32m                    'usia' => isset($data[3]) ? (int)$data[3] : null,[m
[32m+[m[32m                    'jenis_kelamin' => $data[4] ?? null,[m
[32m+[m[32m                    'gaji_per_bulan_rp' => isset($data[5]) ? (int)$data[5] : null,[m
[32m+[m[32m                    'id_jabatan' => isset($data[6]) && $data[6] !== '' ? (int)$data[6] : null,[m
[32m+[m[32m                    'created_at' => now(),[m
[32m+[m[32m                    'updated_at' => now(),[m
                 ]);[m
             }[m
             $firstRow = false;[m
         }[m
[32m+[m
         fclose($csvFile);[m
     }[m
 }[m
[1mdiff --git a/resources/views/penggajian/_form.blade.php b/resources/views/penggajian/_form.blade.php[m
[1mindex 5c6b9d8..09a079b 100644[m
[1m--- a/resources/views/penggajian/_form.blade.php[m
[1m+++ b/resources/views/penggajian/_form.blade.php[m
[36m@@ -47,6 +47,20 @@[m
         @enderror[m
         <small class="text-muted">Masukkan angka dalam Rupiah tanpa tanda titik atau simbol Rp.</small>[m
     </div>[m
[32m+[m
[32m+[m[32m    <div class="col-md-6">[m
[32m+[m[32m        <label class="form-label">Jabatan</label>[m
[32m+[m[32m        @php $jabatanList = $jabatan ?? collect(); @endphp[m
[32m+[m[32m        <select name="id_jabatan" class="form-select @error('id_jabatan') is-invalid @enderror">[m
[32m+[m[32m            <option value="">-- Pilih Jabatan --</option>[m
[32m+[m[32m            @foreach($jabatanList as $j)[m
[32m+[m[32m                <option value="{{ $j->id_jabatan }}" @selected(old('id_jabatan', $employeeSalary->id_jabatan ?? '') == $j->id_jabatan)>{{ $j->nama_jabatan }}</option>[m
[32m+[m[32m            @endforeach[m
[32m+[m[32m        </select>[m
[32m+[m[32m        @error('id_jabatan')[m
[32m+[m[32m            <div class="invalid-feedback">{{ $message }}</div>[m
[32m+[m[32m        @enderror[m
[32m+[m[32m    </div>[m
 </div>[m
 [m
 <div class="d-flex justify-content-end gap-2 mt-4">[m
[1mdiff --git a/resources/views/penggajian/index.blade.php b/resources/views/penggajian/index.blade.php[m
[1mindex da52c7b..d099746 100644[m
[1m--- a/resources/views/penggajian/index.blade.php[m
[1m+++ b/resources/views/penggajian/index.blade.php[m
[36m@@ -52,6 +52,7 @@[m [mclass="form-control"[m
                             <th>Pengalaman</th>[m
                             <th>Usia</th>[m
                             <th>Jenis Kelamin</th>[m
[32m+[m[32m                            <th>Jabatan</th>[m
                             <th>Gaji Per Bulan</th>[m
                             <th class="text-end pe-4">Aksi</th>[m
                         </tr>[m
[36m@@ -69,6 +70,9 @@[m [mclass="form-control"[m
                                         {{ $item->jenis_kelamin }}[m
                                     </span>[m
                                 </td>[m
[32m+[m[32m                                <td>[m
[32m+[m[32m                                    {{ optional($item->jabatan)->nama_jabatan ?? '-' }}[m
[32m+[m[32m                                </td>[m
                                 <td class="fw-semibold">Rp {{ number_format($item->gaji_per_bulan_rp, 0, ',', '.') }}</td>[m
                                 <td class="text-end pe-4">[m
                                     <a href="{{ url('/penggajian/' . $item->id . '/edit') }}" class="btn btn-outline-secondary btn-sm py-1 px-2">Edit</a>[m
[36m@@ -81,7 +85,7 @@[m [mclass="form-control"[m
                             </tr>[m
                         @empty[m
                             <tr>[m
[31m-                                <td colspan="8" class="text-center text-muted py-5">Belum ada data gaji.</td>[m
[32m+[m[32m                                <td colspan="9" class="text-center text-muted py-5">Belum ada data gaji.</td>[m
                             </tr>[m
                         @endforelse[m
                     </tbody>[m
