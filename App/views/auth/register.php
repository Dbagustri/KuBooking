<?php
$role = $role ?? 'mahasiswa';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Baru - Kubooking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-[#1e3a5f] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-xl shadow-2xl overflow-hidden grid md:grid-cols-2">
        <?php
        $flashPath = __DIR__ . '/../layout/flash.php';
        if (file_exists($flashPath)) {
            include $flashPath;
        }
        ?>
        <div class="p-8 md:p-12">
            <!-- Back -->
            <a href="index.php?controller=auth&action=login"
                class="text-gray-500 hover:text-gray-700 inline-flex items-center">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <h1 class="text-3xl font-bold text-gray-800 mt-4">Buat Akun Baru</h1>
            <p class="text-gray-600 mb-4">Daftar untuk mulai menggunakan Kubooking</p>

            <?php if (!empty($error)): ?>
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Tabs role -->
            <div class="flex space-x-2 mb-4">
                <a href="index.php?controller=auth&action=register&role=mahasiswa"
                    class="px-3 py-2 rounded-md text-sm font-semibold 
                          <?= $role === 'mahasiswa' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?>">
                    Mahasiswa
                </a>
                <a href="index.php?controller=auth&action=register&role=dosen"
                    class="px-3 py-2 rounded-md text-sm font-semibold 
                          <?= $role === 'dosen' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?>">
                    Dosen
                </a>
                <a href="index.php?controller=auth&action=register&role=tendik"
                    class="px-3 py-2 rounded-md text-sm font-semibold 
                          <?= $role === 'tendik' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' ?>">
                    Tendik
                </a>
            </div>

            <form action="index.php?controller=auth&action=registerProcess"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-3">

                <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap *
                    </label>
                    <input
                        id="nama"
                        type="text"
                        name="nama"
                        placeholder="Nama Lengkap"
                        class="w-full px-4 py-3 border border-gray-300 rounded-md 
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email *
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="Email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-md 
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- NIM / NIP -->
                <div>
                    <label for="npm" class="block text-sm font-medium text-gray-700 mb-1">
                        NIM / NIP *
                    </label>
                    <input
                        id="npm"
                        type="text"
                        name="npm"
                        placeholder="NIM/NIP"
                        class="w-full px-4 py-3 border border-gray-300 rounded-md 
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <!-- Jurusan (Mahasiswa & Dosen) -->
                <?php if ($role === 'mahasiswa' || $role === 'dosen'): ?>
                    <div>
                        <label for="jurusanSelect" class="block text-sm font-medium text-gray-700 mb-1">
                            Jurusan <?= $role === 'mahasiswa' ? '*' : '' ?>
                        </label>
                        <select
                            name="jurusan"
                            id="jurusanSelect"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500"
                            <?= $role === 'mahasiswa' ? 'required' : '' ?>>
                            <option value="">Pilih Jurusan</option>
                            <option value="TS">Teknik Sipil</option>
                            <option value="TM">Teknik Mesin</option>
                            <option value="TE">Teknik Elektro</option>
                            <option value="TN">Tata Niaga</option>
                            <option value="AN">Administrasi Niaga</option>
                            <option value="R">Rekayasa</option>
                            <option value="TIK">Teknik Informatika dan Komputer</option>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Prodi (Mahasiswa) -->
                <?php if ($role === 'mahasiswa'): ?>
                    <div>
                        <label for="prodiSelect" class="block text-sm font-medium text-gray-700 mb-1">
                            Program Studi *
                        </label>
                        <select
                            name="prodi"
                            id="prodiSelect"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="">Pilih Program Studi</option>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Unit / Jurusan (Tendik) -->
                <?php if ($role === 'tendik'): ?>
                    <div>
                        <label for="unit_jurusan" class="block text-sm font-medium text-gray-700 mb-1">
                            Unit / Jurusan *
                        </label>
                        <input
                            id="unit_jurusan"
                            type="text"
                            name="unit_jurusan"
                            placeholder="Unit / Jurusan"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                <?php endif; ?>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password *
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md pr-10 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500"
                            minlength="8"
                            pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$"
                            title="Minimal 8 karakter dan harus mengandung huruf dan angka."
                            required>
                        <button
                            type="button"
                            class="absolute inset-y-0 right-3 flex items-center text-sm text-gray-500"
                            data-toggle-password="password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Minimal 8 karakter, kombinasi huruf dan angka.
                    </p>
                </div>

                <!-- Ulangi Password -->
                <div>
                    <label for="password2" class="block text-sm font-medium text-gray-700 mb-1">
                        Ulangi Password *
                    </label>
                    <div class="relative">
                        <input
                            id="password2"
                            type="password"
                            name="password2"
                            placeholder="Ulangi Password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-md pr-10 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <button
                            type="button"
                            class="absolute inset-y-0 right-3 flex items-center text-sm text-gray-500"
                            data-toggle-password="password2">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Screenshot Kubaca (Mahasiswa) -->
                <?php if ($role === 'mahasiswa'): ?>
                    <div class="pt-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Upload Screenshot Profil Kubaca (JPG/PNG) *
                        </label>
                        <input
                            type="file"
                            name="screenshot"
                            accept="image/jpeg,image/png"
                            class="w-full text-sm text-gray-500
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100"
                            required>
                    </div>
                <?php endif; ?>

                <button
                    type="submit"
                    class="w-full bg-[#1e3a5f] text-white py-3 rounded-md font-semibold 
                           hover:bg-blue-700 transition duration-300 !mt-4">
                    Register
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-4">
                Sudah punya akun?
                <a href="index.php?controller=auth&action=login"
                    class="text-blue-600 font-medium hover:underline">
                    Login Sekarang
                </a>
            </p>
        </div>

        <div class="hidden md:block">
            <img src="img/rapat.png" alt="Ilustrasi Ruangan Rapat" class="w-full h-full object-cover">
        </div>

    </div>

    <script>
        // ====== DROPDOWN JURUSAN → PRODI (HANYA MAHASISWA) ======
        const jurusanSelect = document.getElementById('jurusanSelect');
        const prodiSelect = document.getElementById('prodiSelect');

        const prodiOptions = {
            // Teknik Sipil
            'TS': [{
                    value: 'D3_KS',
                    label: 'Konstruksi Sipil (D3)'
                },
                {
                    value: 'D3_KG',
                    label: 'Konstruksi Gedung (D3)'
                },
                {
                    value: 'D4_TRJJ',
                    label: 'Teknik Perancangan Jalan dan Jembatan (D4)'
                },
                {
                    value: 'D4_TKG',
                    label: 'Teknik Konstruksi Gedung (D4)'
                },
            ],
            // Teknik Mesin
            'TM': [{
                    value: 'D3_TM',
                    label: 'Teknik Mesin (D3)'
                },
                {
                    value: 'D4_TRM',
                    label: 'Teknologi Rekayasa Manufaktur (D4)'
                },
                {
                    value: 'D4_TRPE',
                    label: 'Teknologi Rekayasa Pembangkit Energi (D4)'
                },
                {
                    value: 'D4_TRKE',
                    label: 'Teknologi Rekayasa Konversi Energi (D4)'
                },
                {
                    value: 'D4_TRPAB',
                    label: 'Teknologi Rekayasa Pemeliharaan Alat Berat (D4)'
                },
            ],
            // Teknik Elektro
            'TE': [{
                    value: 'D3_EI',
                    label: 'Elektronika Industri (D3)'
                },
                {
                    value: 'D3_TL',
                    label: 'Teknik Listrik (D3)'
                },
                {
                    value: 'D3_TLK',
                    label: 'Telekomunikasi (D3)'
                },
                {
                    value: 'D4_IKI',
                    label: 'Instrumentasi Kontrol Industri (D4)'
                },
                {
                    value: 'D4_TOLI',
                    label: 'Teknik Otomasi Listrik Industri (D4)'
                },
                {
                    value: 'D4_BM',
                    label: 'Broadband Multimedia (D4)'
                },
            ],
            // Tata Niaga
            'TN': [{
                    value: 'D3_AK',
                    label: 'Akuntansi (D3)'
                },
                {
                    value: 'D3_KP',
                    label: 'Keuangan dan Perbankan (D3)'
                },
                {
                    value: 'D4_KP',
                    label: 'Keuangan dan Perbankan (D4)'
                },
                {
                    value: 'D4_AKK',
                    label: 'Akuntansi Keuangan (D4)'
                },
                {
                    value: 'D4_KPS',
                    label: 'Keuangan dan Perbankan Syariah (D4)'
                },
                {
                    value: 'D4_MK',
                    label: 'Manajemen Keuangan (D4)'
                },
                {
                    value: 'D3_MP',
                    label: 'Manajemen Pemasaran (D3)'
                },
            ],
            // Administrasi Niaga
            'AN': [{
                    value: 'D3_AB',
                    label: 'Administrasi Bisnis (D3)'
                },
                {
                    value: 'D4_ABT',
                    label: 'Administrasi Bisnis Terapan (D4)'
                },
                {
                    value: 'D4_MICE',
                    label: 'Usaha Jasa Konvensi / Perjalanan Insentif / Pameran (MICE) (D4)'
                },
                {
                    value: 'D4_BI',
                    label: 'Bahasa Inggris untuk Komunikasi Bisnis dan Profesional (D4)'
                },
            ],
            // Rekayasa
            'R': [{
                    value: 'D3_PB',
                    label: 'Penerbitan (D3)'
                },
                {
                    value: 'D3_TG',
                    label: 'Teknik Grafika (D3)'
                },
                {
                    value: 'D4_DG',
                    label: 'Desain Grafis (D4)'
                },
                {
                    value: 'D4_TICK',
                    label: 'Teknologi Industri Cetak Kemasan (D4)'
                },
                {
                    value: 'D4_TRCG3D',
                    label: 'Teknologi Rekayasa Cetak dan Grafis 3D (D4)'
                },
            ],
            // Teknik Informatika dan Komputer
            'TIK': [{
                    value: 'D4_TI',
                    label: 'Teknik Informatika (D4)'
                },
                {
                    value: 'D4_TMD',
                    label: 'Teknik Multimedia Digital (D4)'
                },
                {
                    value: 'D4_TMJ',
                    label: 'Teknik Multimedia dan Jaringan (D4)'
                },
            ]
        };

        function populateProdi() {
            if (!jurusanSelect || !prodiSelect) return;

            const jurusan = jurusanSelect.value;
            prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';

            if (!jurusan || !prodiOptions[jurusan]) return;

            prodiOptions[jurusan].forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.value;
                opt.textContent = p.label;
                prodiSelect.appendChild(opt);
            });
        }

        if (jurusanSelect && prodiSelect) {
            jurusanSelect.addEventListener('change', populateProdi);
            populateProdi();
        }

        // ====== TOGGLE SHOW / HIDE PASSWORD ======
        const toggleButtons = document.querySelectorAll('[data-toggle-password]');

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-toggle-password');
                const input = document.getElementById(targetId);
                const icon = btn.querySelector('i');
                if (!input || !icon) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // ====== VALIDASI PASSWORD KUAT DI FRONTEND ======
        const passwordInput = document.getElementById('password');
        const password2Input = document.getElementById('password2');

        function checkPasswordStrength() {
            if (!passwordInput) return;

            const val = passwordInput.value;
            const strong = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/.test(val);

            if (!strong && val.length > 0) {
                passwordInput.setCustomValidity(
                    'Password minimal 8 karakter dan harus mengandung huruf dan angka.'
                );
            } else {
                passwordInput.setCustomValidity('');
            }
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', checkPasswordStrength);
        }

        function checkPasswordMatch() {
            if (!passwordInput || !password2Input) return;

            if (password2Input.value && password2Input.value !== passwordInput.value) {
                password2Input.setCustomValidity('Konfirmasi password tidak sama.');
            } else {
                password2Input.setCustomValidity('');
            }
        }

        if (password2Input) {
            password2Input.addEventListener('input', checkPasswordMatch);
            if (passwordInput) {
                passwordInput.addEventListener('input', checkPasswordMatch);
            }
        }
    </script>

</body>

</html>