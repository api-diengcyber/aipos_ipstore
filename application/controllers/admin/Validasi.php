<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Validasi extends AI_Admin
{
    function __construct()
    {
      parent::__construct();
      $this->models('Validasi_model');
    }

    public function index()
    {
      set_time_limit(100);
      $data = array(
        'active_validasi' => 'active',
        'data_jurnal' => $this->Validasi_model->get_jurnal_couple($this->userdata->id_toko),
      );
      $this->view('validasi/validasi_list', $data);
    }
    
    public function json($h = "") {
      header('Content-Type: application/json');
      echo $this->Validasi_model->json($this->userdata->id_toko, $h);
    }
    
    public function json2($h = "") {
      header('Content-Type: application/json');
      echo $this->Validasi_model->json2($this->userdata->id_toko, $h);
    }
    
    public function json_invoice($h = "") {
      header('Content-Type: application/json');
      echo $this->Validasi_model->json_invoice($this->userdata->id_toko, $h);
    }

    public function read($id) 
    {
      $row = $this->Validasi_model->get_by_id($id);
      if ($row) {
        $data = array(
          'active_supplier' => 'active',
          'id_supplier' => $row->id_supplier,
          'id_toko' => $row->id_toko,
          'nama_supplier' => $row->nama_supplier,
          'alamat' => $row->alamat,
          'kota' => $row->kota,
          'telp' => $row->telp,
          'fax' => $row->fax,
          'cp' => $row->cp,
          'telp_cp' => $row->telp_cp,
          'ket' => $row->ket,
        );
        $this->view('supplier/supplier_read', $data);
      } else {
        $this->session->set_flashdata('message', 'Record Not Found');
        redirect(site_url('admin/supplier'));
      }
    }

    public function update($id_order)
    {
      $data_update = array(
        'valid' => 1
      );
      $this->db->where('id_orders_2', $id_order);
      $this->db->update('orders', $data_update);
      $this->session->set_flashdata('message', 'Validasi Record Success');
      redirect(site_url('admin/validasi'));
    }

    public function delete($id) 
    {
        $row = $this->Validasi_model->get_by_id($id);
        if ($row) {
          $this->Validasi_model->delete($id);
          $this->session->set_flashdata('message', 'Delete Record Success');
          redirect(site_url('admin/validasi'));
        } else {
          $this->session->set_flashdata('message', 'Record Not Found');
          redirect(site_url('admin/validasi'));
        }
    }

    public function konfirmasi_delete()
    {
        $id_order = $this->input->post('id_order', true);
        $password = $this->input->post('password', true);
        $r = $this->Ion_auth_model->hash_password_db($this->userdata->id_users, $password);
        if ($r) {
          $this->session->set_flashdata('message', 'Hapus data berhasil');
          $this->delete_orders($id_order);
        } else {
          $this->session->set_flashdata('message', 'Hapus data gagal');
          redirect(base_url('admin/validasi'),'refresh');
        }
    }

    public function delete_orders($id_order)
    {
      $this->db->where('id_orders_2', $id_order);
      $this->db->delete('orders_detail');
      $this->db->where('id_orders_2', $id_order);
      $this->db->delete('orders');
	    redirect(base_url('admin/validasi'),'refresh');
    }

    public function delete_jurnal($kode)
    {
      $this->db->where('kode', $kode);
      $this->db->delete('jurnal');
	    redirect(base_url('admin/validasi'),'refresh');
    }
}

/* End of file Supplier.php */
/* Location: ./application/controllers/Supplier.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2017-07-29 08:59:55 */
/* http://harviacode.com */