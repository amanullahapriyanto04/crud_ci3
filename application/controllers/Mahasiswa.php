<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Mahasiswa extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //load helper
        $this->load->helper('url');
        $this->load->helper('form');
        //load library  
        $this->load->library('form_validation');
        //load model 
        $this->load->model('mahasiswa_m');
        
        include APPPATH . 'third_party/imageResize/ImageResize.php';
	}
	public function image_resize() {
		$image = new \Gumlet\ImageResize($this->config->item('image_main').$fname);
        $image
		    ->resizeToWidth(500,300)
		    ->save($this->config->item('image_large').$fname)
		    ->crop(100, 100)
		    ->save($this->config->item('image_small').$fname)
		;
    }

    public function index()
    {
        //ambil data dari database
        $getData = $this->mahasiswa_m->get();

        $data = [
            'mahasiswa' => $getData->result_array(),
            'jumlah_data' => $getData->num_rows()
        ];

        //menampilkan view
        $this->load->view('mahasiswa/index', $data);
    }

    public function create()
    {
        //rule validasi
        $validation_rules = [
            [
                'field' => 'nim',
                'label' => 'NIM',
                'rules' => 'required'
            ],
            [
                'field' => 'nama',
                'label' => 'Nama',
                'rules' => 'required'
            ],
            [
                'field' => 'jeniskelamin',
                'label' => 'Jenis Kelamin',
                'rules' => 'required'
            ],
            [
                'field' => 'jurusan',
                'label' => 'Jurusan',
                'rules' => 'required'
            ],
            [
                'field' => 'tanggal_lahir',
                'label' => 'Tanggal Lahir',
                'rules' => 'required'
            ],
            [
                'field' => 'alamat',
                'label' => 'Alamat',
                'rules' => 'required'
            ]
        ];

        //set rule validasi
        $this->form_validation->set_rules($validation_rules);

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('mahasiswa/add');
        } else {

            //data mahasiswa
            $mahasiswa = [
                'nim' => $this->input->post('nim'),
                'nama' => $this->input->post('nama'),
                'jurusan' => $this->input->post('jurusan'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'jenis_kelamin' => $this->input->post('jeniskelamin'),
                'alamat' => $this->input->post('alamat')
            ];

            $this->mahasiswa_m->insert($mahasiswa);

            $data['msg']  =  'Data berhasil disimpan';

            $this->load->view('mahasiswa/notifikasi', $data);
        }

    }
    public function edit($nim = '')
    {
        //Cek apakah ada parameter $nim
        if ('' == $nim) {
            //jika tidak ada, maka alihkan ke halaman daftar mahasiswa
            redirect('mahasiswa');
        }
        //ambil data mahasisa berdasarkan nim
        $data['mahasiswa'] =  $this->mahasiswa_m->get_by_nim($nim)->row_array();
        //load form edit
        $this->load->view('mahasiswa/edit', $data);
    }

    public function update()
    {
        //cek apakah tombol update ditekan
        if ($this->input->post('update')) {
            $nim = $this->input->post('nim');

            //rule validasi
            $validation_rules = [
                [
                    'field' => 'nim',
                    'label' => 'NIM',
                    'rules' => 'required'
                ],
                [
                    'field' => 'nama',
                    'label' => 'Nama',
                    'rules' => 'required'
                ],
                [
                    'field' => 'jeniskelamin',
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required'
                ],
                [
                    'field' => 'jurusan',
                    'label' => 'Jurusan',
                    'rules' => 'required'
                ],
                [
                    'field' => 'tanggal_lahir',
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required'
                ],
                [
                    'field' => 'alamat',
                    'label' => 'Alamat',
                    'rules' => 'required'
                ]
            ];

            //set rule validasi
            $this->form_validation->set_rules($validation_rules);

            if ($this->form_validation->run() === false) {
                redirect('mahasiswa/edit/'. $nim);
            }

            $where['nim'] = $nim;

            //data mahasiswa
            $mahasiswa = [
                'nim' => $this->input->post('nim'),
                'nama' => $this->input->post('nama'),
                'jurusan' => $this->input->post('jurusan'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'jenis_kelamin' => $this->input->post('jeniskelamin'),
                'alamat' => $this->input->post('alamat')
            ];

            //update data
            $this->mahasiswa_m->update($mahasiswa, $where);

            $data['msg'] = 'Data berhasil diperbaharui';
            $this->load->view('mahasiswa/notifikasi', $data);
        } else {
            echo "<h3 style='color:red;'>Forbidden access</h3>";
        }
    }

    public function hapus($nim = '')
    {
        //cek apakah parameter nim ada
        if ('' == $nim) {
            //jika tidak, tampilkan error
            return show_404();
        }
        //hapus data
        $this->mahasiswa_m->delete($nim);

        $data['msg']  =  'Data berhasil dihapus';
        $this->load->view('mahasiswa/notifikasi', $data);
    }

}