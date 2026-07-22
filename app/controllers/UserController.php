<?php
require_once dirname(__FILE__) . '/../models/ModelUser.php';
require_once dirname(__FILE__) . '/../models/ModelUserKaryawan.php';
require_once dirname(__FILE__) . '/../models/ModelLokasi.php';
require_once dirname(__FILE__) . '/../models/ModelRole.php';

class UserController extends Controller
{
    public function users()
    {
        if (!CekAkses(Auth()->role, 'read_user')) {
            return Show403();
        }

        $modelUser = new ModelUser();
        $modelRole = new ModelRole();
        $modelLokasi = new ModelLokasi();
        $modelUserKaryawan = new ModelUserKaryawan();

        $data = $modelUser->all();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->lokasi_kerja = explode(',', $data[$i]->lokasi_kerja);
            $data[$i]->lokasi = getLokasi($data[$i]->lokasi_kerja);
        }
        // dd($data);
        $dataLokasi = $modelLokasi->all();
        $dataRole = $modelRole->all();

        // Ambil data karyawan dengan id bukan 0 (menggunakan method whereNotEqual)
        // $dataKaryawan = $userKaryawan->whereNotEqual('id', 0)->get();
        $dataKaryawan = $modelUserKaryawan->where('id', 0, '!=')->get();
        $this->view('pengaturan/users', array('data' => $data, 'dataKaryawan' => $dataKaryawan, 'dataLokasi' => $dataLokasi, 'dataRole' => $dataRole));
    }

    public function users_get($id = null)
    {
        if (!CekAkses(Auth()->role, 'read_user')) {
            return Show403();
        }

        $user = new ModelUser();
        $data = $user->selectOne($id);
        if (!$data) {
            return jsonResponse(404, 'Data User tidak ditemukan');
        }
        return jsonResponse(200, 'Data User', $data);
    }

    public function users_add()
    {
        if (!CekAkses(Auth()->role, 'create_user')) {
            return Show403();
        }
        // CSRF Protection
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid. Silakan coba lagi.', 'error');
            return;
        }
        // dd($_POST);

        $method = isset($_POST['inputMethod']) ? $_POST['inputMethod'] : '';

        if ($method == 'manual') {
            // Ambil data dari POST
            $data = array(
                'username' => isset($_POST['username']) ? sanitize($_POST['username'], 'string') : '',
                'nama' => isset($_POST['nama']) ? sanitize($_POST['nama'], 'string') : '',
                'password' => isset($_POST['password']) ? $_POST['password'] : '',
                'role' => isset($_POST['role']) ? sanitize($_POST['role'], 'string') : 'petugas',
                // 'lokasi_kerja' => isset($_POST['lokasi']) ? sanitize($_POST['lokasi'], 'integer') : 0,
                'lokasi_kerja' => isset($_POST['lokasi']) && is_array($_POST['lokasi']) ? $_POST['lokasi'] : array(),
            );

            // Definisikan rules validasi
            $rules = array(
                'username' => 'required|alpha_numeric|min_length[3]|max_length[20]|unique[users.username]',
                'nama' => 'required|string|min_length[3]|max_length[155]',
                'password' => 'required|min_length[6]',
                'role' => 'required|in_list[perawat,admin,petugas]',
                'lokasi_kerja' => 'required'
            );

            // Definisikan custom messages
            $messages = array(
                'username' => array(
                    'required' => 'Username wajib diisi.',
                    'alpha_numeric' => 'Username hanya boleh berisi huruf dan angka.',
                    'min_length' => 'Username minimal 3 karakter.',
                    'max_length' => 'Username maksimal 20 karakter.',
                    'unique' => 'Username sudah digunakan.'
                ),
                'nama' => array(
                    'required' => 'Nama wajib diisi.',
                    'string' => 'Nama hanya boleh berisi huruf.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama maksimal 155 karakter.'
                ),
                'password' => array(
                    'required' => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 6 karakter.'
                ),
                'role' => array(
                    'required' => 'Role wajib diisi.',
                    'in_list' => 'Role tidak valid.'
                ),
                'lokasi_kerja' => array(
                    'required' => 'Lokasi wajib diisi.',
                )
            );

            // Validasi menggunakan helper function
            $validator = validator($data, $rules, $messages);

            if ($validator->fails()) {
                // Ambil error pertama saja
                $errorMessage = $validator->getFirstError();
                $this->redirectBack($errorMessage, 'error');
                return;
            }

            // Jika validasi sukses, lanjutkan proses simpan
            $user = new ModelUser();
            $data['lokasi_kerja'] = implode(',', $data['lokasi_kerja']);
            $data['password'] = hash_password($data['password']);
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $user->create($data);

            if (!$result) {
                $this->redirectBack('Gagal menambahkan user. Silakan coba lagi.', 'error');
                return;
            } else {
                $this->redirectBack('User berhasil ditambahkan!', 'success');
            }
        } else if ($method == 'karyawan') {
            // Handle database selection
            $userId = isset($_POST['userFromDb']) ? (int)$_POST['userFromDb'] : 0;

            $rules = array(
                'userFromDb' => 'required|numeric|exists[users.id]',
                'role' => 'required|in_list[perawat,admin,petugas]',
                'lokasi' => 'required'
            );

            $messages = array(
                'userFromDb' => array(
                    'required' => 'Silakan pilih user dari database.',
                    'numeric' => 'ID user tidak valid.',
                    'exists' => 'User tidak ditemukan di database.'
                ),
                'role' => array(
                    'required' => 'Role wajib diisi.',
                    'in_list' => 'Role tidak valid.'
                ),
                'lokasi' => array(
                    'required' => 'Lokasi wajib diisi.',
                    'integer' => 'Lokasi tidak valid.'
                )
            );

            $validator = validator(array('userFromDb' => $userId, 'role' => $_POST['role'], 'lokasi' => $_POST['lokasi']), $rules, $messages);

            if ($validator->fails()) {
                $this->redirectBack($validator->getFirstError(), 'error');
                return;
            }

            // cari data user karyawan dari database karyawan
            $userKaryawan = new ModelUserKaryawan();
            $karyawanData = $userKaryawan->selectOne($userId);

            if (!$karyawanData) {
                $this->redirectBack('User dari database tidak ditemukan.', 'error');
                return;
            }

            $data = array(
                'username' => $karyawanData->id,
                'nama' => $karyawanData->nama,
                'role' => isset($_POST['role']) ? sanitize($_POST['role'], 'string') : 'petugas',
                'lokasi_kerja' => isset($_POST['lokasi']) ? implode(',', $_POST['lokasi']) : '',
                'created_at' => date('Y-m-d H:i:s')
            );

            $user = new ModelUser();
            $result = $user->create($data);
            if (!$result) {
                $this->redirectBack('Gagal menambahkan user dari database. Silakan coba lagi.', 'error');
                return;
            } else {
                $this->redirectBack('User dari database berhasil ditambahkan!', 'success');
            }
        } else {
            $this->redirectBack('Metode input tidak valid.', 'error');
        }
    }

    public function users_update($id = null)
    {
        if (!CekAkses(Auth()->role, 'update_user')) {
            return Show403();
        }
        // CSRF Protection
        if (CSRF_ENABLED && !verify_csrf()) {
            $this->redirectBack('Token CSRF tidak valid. Silakan coba lagi.', 'error');
            return;
        }

        // Cek user exists
        $user = new ModelUser();
        $existingUser = $user->selectOne($id);
        if (!$existingUser) {
            $this->redirectBack('User tidak ditemukan.', 'error');
            return;
        }

        // Ambil metode input
        $method = isset($_POST['inputMethod']) ? $_POST['inputMethod'] : '';

        // dd($_POST);
        if ($method == 'manual') {
            // Handle manual input
            $data = array(
                'username' => isset($_POST['username']) ? sanitize($_POST['username'], 'string') : '',
                'nama' => isset($_POST['nama']) ? sanitize($_POST['nama'], 'string') : '',
                'password' => isset($_POST['password']) ? $_POST['password'] : '',
                'role' => isset($_POST['role']) ? sanitize($_POST['role'], 'string') : 'petugas',
                'lokasi_kerja' => isset($_POST['lokasi']) && is_array($_POST['lokasi']) ? $_POST['lokasi'] : array(),
            );

            // Definisikan rules validasi
            $rules = array(
                'username' => 'required|alpha_numeric|min_length[3]|max_length[20]',
                'nama' => 'required|string|min_length[3]|max_length[155]',
                'role' => 'required|in_list[perawat,admin,petugas]',
                'lokasi_kerja' => 'required'
            );

            // Cek username unique (kecuali username sendiri)
            $checkUsername = $user->where('username', $data['username'])->first();
            if ($checkUsername && $checkUsername->id != $id) {
                $this->redirectBack('Username sudah digunakan oleh user lain.', 'error');
                return;
            }

            // Jika password diisi, tambahkan validasi
            if (!empty($data['password'])) {
                $rules['password'] = 'min_length[6]';
            }

            // Custom messages
            $messages = array(
                'username' => array(
                    'required' => 'Username wajib diisi.',
                    'alpha_numeric' => 'Username hanya boleh berisi huruf dan angka.',
                    'min_length' => 'Username minimal 3 karakter.',
                    'max_length' => 'Username maksimal 20 karakter.'
                ),
                'nama' => array(
                    'required' => 'Nama wajib diisi.',
                    'string' => 'Nama hanya boleh berisi huruf.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama maksimal 155 karakter.'
                ),
                'password' => array(
                    'min_length' => 'Password minimal 6 karakter.'
                ),
                'role' => array(
                    'required' => 'Role wajib diisi.',
                    'in_list' => 'Role tidak valid.'
                ),
                'lokasi_kerja' => array(
                    'required' => 'Lokasi wajib diisi.'
                )
            );

            // Validasi
            $validator = validator($data, $rules, $messages);
            if ($validator->fails()) {
                $this->redirectBack($validator->getFirstError(), 'error');
                return;
            }

            // Prepare data untuk update
            $updateData = array(
                'username' => $data['username'],
                'nama' => $data['nama'],
                'role' => $data['role'],
                'lokasi_kerja' => implode(',', $data['lokasi_kerja']),
            );
            // dd($updateData, $data);

            // Jika password diisi, hash dan masukkan ke updateData
            if (!empty($data['password'])) {
                $updateData['password'] = hash_password($data['password']);
            }

            // change session user 
            // $_SESSION['user'] = array(
            //     'id' => $id,
            //     'username' => $updateData['username'],
            //     'nama' => $updateData['nama'],
            //     'role' => $updateData['role'],
            //     'role_nama' => getRole($updateData['role'])->nama,
            //     'lokasi_kerja' => explode(',', $updateData['lokasi_kerja'])
            // );

            // Update user
            $result = $user->update($updateData, $id);
            if ($result) {
                $this->redirectBack('User berhasil diperbarui!', 'success');
            } else {
                $this->redirectBack('Gagal memperbarui user. Silakan coba lagi.', 'error');
            }
        } else if ($method == 'karyawan') {
            // Handle database selection
            $userId = isset($_POST['userFromDb']) ? (int)$_POST['userFromDb'] : 0;

            $data = array(
                'userFromDb' => $userId,
                'role' => isset($_POST['role']) ? sanitize($_POST['role'], 'string') : 'petugas',
                'lokasi_kerja' => isset($_POST['lokasi']) && is_array($_POST['lokasi']) ? $_POST['lokasi'] : array()
            );

            $rules = array(
                'userFromDb' => 'required|numeric',
                'role' => 'required|in_list[perawat,admin,petugas]',
                'lokasi_kerja' => 'required'
            );

            $messages = array(
                'userFromDb' => array(
                    'required' => 'Silakan pilih user dari database.',
                    'numeric' => 'ID user tidak valid.'
                ),
                'role' => array(
                    'required' => 'Role wajib diisi.',
                    'in_list' => 'Role tidak valid.'
                ),
                'lokasi_kerja' => array(
                    'required' => 'Lokasi wajib diisi.'
                )
            );

            $validator = validator($data, $rules, $messages);
            if ($validator->fails()) {
                $this->redirectBack($validator->getFirstError(), 'error');
                return;
            }

            // Cari data user karyawan dari database karyawan
            $userKaryawan = new ModelUserKaryawan();
            $karyawanData = $userKaryawan->selectOne($userId);

            if (!$karyawanData) {
                $this->redirectBack('User dari database karyawan tidak ditemukan.', 'error');
                return;
            }

            // Cek apakah username sudah digunakan oleh user lain
            $checkUsername = $user->where('username', $karyawanData->id)->first();
            if ($checkUsername && $checkUsername->id != $id) {
                $this->redirectBack('Username dari database karyawan sudah digunakan oleh user lain.', 'error');
                return;
            }

            // Update data user
            $updateData = array(
                'username' => $karyawanData->id,
                'nama' => $karyawanData->nama,
                'role' => $data['role'],
                'lokasi_kerja' => implode(',', $data['lokasi_kerja'])
            );

            // Session user login update
            // $_SESSION['user'] = array(
            //     'id' => $id,
            //     'username' => $updateData['username'],
            //     'nama' => $updateData['nama'],
            //     'role' => $updateData['role'],
            //     'role_nama' => getRole($updateData['role'])->nama,
            //     'lokasi_kerja' => explode(',', $updateData['lokasi_kerja'])
            // );

            $result = $user->update($updateData, $id);
            if ($result) {
                $this->redirectBack('User berhasil diperbarui dari database karyawan!', 'success');
            } else {
                $this->redirectBack('Gagal memperbarui user. Silakan coba lagi.', 'error');
            }
        } else {
            $this->redirectBack('Metode input tidak valid.', 'error');
        }
    }

    public function users_delete($id = null)
    {
        if (!CekAkses(Auth()->role, 'delete_user')) {
            return Show403();
        }
        $user = new ModelUser();
        $data = $user->selectOne($id);
        if (!$data) {
            $this->redirectBack('User tidak ditemukan.', 'error');
            return;
        }
        $result = $user->delete($id);

        if ($result) {
            $this->redirectBack('User berhasil dihapus.', 'success');
        } else {
            $this->redirectBack('Gagal menghapus user. Silakan coba lagi.', 'error');
        }
    }
}
