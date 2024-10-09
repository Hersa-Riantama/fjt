<?php

namespace Modules\Auth\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Modules\Auth\Models\AuthModel;

class Auth extends BaseController
{
    use ResponseTrait;
    protected $folder_directory = "Modules\\Auth\\Views\\";
    protected $model;

    public function __construct()
    {
        $this->model = new AuthModel();
    }
    public function Flogin()
    {
        // Ambil data inputan dari request
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->run(compact('email', 'password'))) {
            // Jika validasi gagal, kembalikan error
            $errors = $validation->getErrors();
            $response = [
                'Pesan' => 'Validasi gagal',
                'Status' => 'error',
                'Errors' => $errors  // Menyertakan pesan error spesifik
            ];
            return $this->response->setJSON($response)->setStatusCode(400);
        }

        // Cari user berdasarkan email di database
        $user = $this->model->where('email', $email)->first();
        if (!$user) {
            // Jika user tidak ditemukan di database
            $response = [
                'Pesan' => 'User tidak ditemukan',
                'Status' => 'error'
            ];
            return $this->response->setJSON($response)->setStatusCode(404);
        }
        $passmd5 = md5($password);
        // Verifikasi password dengan password yang ada di database
        if ($passmd5 !== ($user['password'])) {
            // Jika password tidak cocok
            $response = [
                'Pesan' => 'Password salah',
                'Status' => 'error'
            ];
            return $this->response->setJSON($response)->setStatusCode(401);
        }

        // Cek status verifikasi user di database
        if ($user['verifikasi'] == 'N') {
            // Jika user belum diverifikasi
            return $this->fail('User belum diverifikasi');
        }
        // Response berhasil
        $response = [
            'Pesan' => 'Berhasil Login',
            'Status' => 'success'
        ];
        return $this->response->setJSON($response)->setStatusCode(200);
        // // Ambil nilai Authorization header
        // $authHeader = $this->request->getHeader('Authorization');
        // // Cek apakah header Authorization ada dan nilai key cocok
        // if ($authHeader && $authHeader->getValue() === $this->value) {
        // } else {
        //     // Jika header Authorization tidak valid
        //     return $this->failUnauthorized('Anda tidak memiliki kunci akses');
        // }
    }

    public function regis()
    {
        // Validasi input data
        $rules = $this->model->validationRules();

        // Jika validasi gagal
        if (!$this->validate($rules)) {
            $response = [
                'Pesan' => $this->validator->getErrors()
            ];
            return $this->response->setJSON($response, 400);
        }
        $this->model->insert([
            'nama' => esc($this->request->getVar('nama')),
            'nomor_induk' => esc($this->request->getVar('nomor_induk')),
            'email' => esc($this->request->getVar('email')),
            'no_tlp' => esc($this->request->getVar('no_tlp')),
            'jk' => esc($this->request->getVar('jk')),
            'password' => md5(esc($this->request->getVar('password'))),
            'level_user' => esc($this->request->getVar('level_user')),
        ]);

        // Response berhasil
        $response = [
            'Pesan' => 'Data Pegawai Berhasil ditambahkan'
        ];
        return $this->response->setJSON($response)->setStatusCode(200);
        // // Ambil nilai Authorization header
        // $authHeader = $this->request->getHeader('Authorization');

        // // Cek apakah header Authorization ada dan nilai key cocok
        // if ($authHeader && $authHeader->getValue() === $this->value) {
        // } else {
        //     // Jika header Authorization tidak valid
        //     return $this->failUnauthorized('Anda Tidak Memiliki Kunci Akses');
        // }
    }

    public function index()
    {
        return view($this->folder_directory . 'index');
    }

    public function login()
    {
        return view($this->folder_directory . 'login');
    }

    public function daftar()
    {
        return view($this->folder_directory . 'daftar');
    }
}
