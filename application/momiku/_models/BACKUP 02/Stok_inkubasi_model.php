<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stok_inkubasi_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
        $this->load->model('Login_model');
        $this->userdata = $this->Login_model->get_data();
	}

	function json()
	{
		$this->datatables->select('ib.id, ib.id_toko, ib.id_users, ib.id_inkubasi, ib.id_produk, ib.expire_date, ib.keranjang, ib.cup, ib.rusak, ib.total, ib.dipakai, pr.nama_produk, SUM(ib.keranjang) AS keranjang, SUM(ib.cup) AS cup, SUM(ib.rusak) AS rusak, SUM(ib.total) AS total, SUM(ib.dipakai) AS dipakai');
		$this->datatables->from('inkubasi_barang ib');
		$this->datatables->join('users u', 'ib.id_users=u.id_users AND ib.id_toko=u.id_toko');
		$this->datatables->join('produk_retail pr', 'ib.id_produk=pr.id_produk_2 AND pr.id_users=u.id_users AND ib.id_toko=pr.id_toko');
		$this->datatables->where('ib.id_toko', $this->userdata->id_toko);
		$this->datatables->where('u.id_cabang', $this->userdata->id_cabang);
		// $this->db->having('total', 'desc');
		$this->datatables->group_by('pr.id_produk_2');
        return $this->datatables->generate();
	}

}

/* End of file Stok_inkubasi_model.php */
/* Location: ./application/models/Stok_inkubasi_model.php */