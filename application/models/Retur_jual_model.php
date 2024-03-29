<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retur_jual_model extends CI_Model {

	public $table = 'retur_jual';

	public function __construct()
	{
		parent::__construct();
	}

	function json($id_toko)
	{
        $this->datatables->select('r.id_retur, r.id_toko, r.tgl, r.no_retur, r.no_faktur, r.total');
        $this->datatables->from('retur_jual r');
        $this->datatables->where('r.id_toko', $id_toko);
        $this->datatables->add_column('action', anchor(site_url('admin/retur_jual/read/$1'),'<button class="btn btn-xs btn-success"><i class="ace-icon fa fa-check bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('admin/retur_jual/cetak/$2'),'<button class="btn btn-xs btn-inverse"><i class="ace-icon fa fa-print bigger-120"></i></button>', 'target="_blank"')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('admin/retur_jual/delete/$1'),'<button class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash-o bigger-120"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id_retur, no_retur');
        return $this->datatables->generate();
	}

}

/* End of file Retur_jual_model.php */
/* Location: ./application/models/Retur_jual_model.php */