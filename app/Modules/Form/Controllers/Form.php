<?php

namespace Modules\Form\Controllers;

use App\Controllers\BaseController;
use App\Modules\Buku\Models\BukuModel;
use CodeIgniter\API\ResponseTrait;
use Modules\Form\Models\FormModel;

class Form extends BaseController
{
    use ResponseTrait;
    protected $folder_directory = "Modules\\Form\\Views\\";
    protected $model;

    public function __construct()
    {
        $this->model = new FormModel(); // Inisialisasi model
    }
    public function index()
    {
        return view($this->folder_directory . 'index');
    }
    public function getBukuOptions()
    {
        $bukuModel = new BukuModel();
        $data = $bukuModel->findAll(); // Fetch all buku data
        return $this->response->setJSON($data);
    }
    public function getBukuDetails($kode_buku)
    {
        $bukuModel = new BukuModel();
        $data = $bukuModel->where('kode_buku', $kode_buku)->first();
        return $this->response->setJSON($data);
    }
    public function createForm()
    {
        // $authHeader = $this->request->getHeader('Authorization');
        // if ($authHeader && $authHeader->getValue() === $this->value) {
        // }else {
        //     return $this->failUnauthorized('Anda Tidak Memiliki Kunci Akses');
        // }
        $rules = $this->model->validationRules();
        if (!$this->validate($rules)) {
            $response = [
                'pesan' => $this->validator->getErrors()
            ];
            return $this->response->setJSON($response);
        }
        $id_buku = esc($this->request->getVar('id_buku'));
        $bukuModel = new BukuModel();
        $buku = $bukuModel->where('judul_buku', $id_buku)->first();
        if (!$buku) {
            return $this->response->setJSON('Buku tidak ditemukan');
        }
        $id_buku = $buku['judul_buku'];
        $nama_kategori = esc($this->request->getVar('nama_kategori'));
        $kategoriModel = new \Modules\Kategori\Models\KategoriModel();
        $kategori = $kategoriModel->where('nama_kategori', $nama_kategori)->first();
        if (!$kategori) {
            return $this->response->setJSON('Kategori tidak ditemukan');
        }
        $id_kategori = $kategori['id_kategori'];
        $tgl_order = date('y-m-d', strtotime($this->request->getVar('tgl_order')));
        $this->model->insert([
            'id_kategori' => esc($id_kategori),
            'tgl_order' => esc($tgl_order),
            'id_user' => esc($this->request->getVar('id_user')),
            'nomor_job' => esc($this->request->getVar('nomor_job')),
            'id_buku' => esc($id_buku),
        ]);
        $id_tiket = $this->model->getInsertID();
        $kelengkapanModel = new \Modules\Kelengkapan\Models\KelengkapanModel();
        $kelengkapan = $this->request->getVar('kelengkapan');
        if (is_array($kelengkapan) && count($kelengkapan) > 0) {
            foreach ($kelengkapan as $nama_kelengkapan) {
                $kelengkapanModel->insert([
                    'id_tiket' => $id_tiket,
                    'nama_kelengkapan' => esc($nama_kelengkapan),
                ]);
            }
        }
        $statusKelengkapanModel = new \Modules\Status_Kelengkapan\Models\StatusKelengkapanModel();
        $tahap_kelengkapan = esc($this->request->getVar('tahap_kelengkapan'));
        $status_kelengkapan = esc($this->request->getVar('status_kelengkapan'));
        if (!empty($tahap_kelengkapan) && !empty($status_kelengkapan)) {
            $statusKelengkapanModel->insert([
                'id_tiket' => $id_tiket,
                'tahap_kelengkapan' => !empty($tahap_kelengkapan) ? $tahap_kelengkapan : 'N',
                'status_kelengkapan' => !empty($status_kelengkapan) ? $status_kelengkapan : 'N',
            ]);
        } else {
            $statusKelengkapanModel->insert([
                'id_tiket'           => $id_tiket,
                'tahap_kelengkapan'   => 'N',
                'status_kelengkapan'  => 'N'
            ]);
        }
        $response = [
            'Pesan' => 'Tiket Berhasil ditambahkan'
        ];
        return $this->response->setJSON($response);
    }
    public function form()
    {
        $data = [
            'judul' => 'Form Job Ticket',
        ];
        return view($this->folder_directory . 'form', $data);
    }
    public function data_form()
    {
        $data = [
            'judul' => 'List Form',
        ];
        return view($this->folder_directory . 'data_form', $data);
    }
}
