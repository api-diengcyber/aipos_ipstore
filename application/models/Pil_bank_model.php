<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pil_bank_model extends CI_Model
{

    public $table = 'pil_bank';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // datatables
    function json() {
        $this->datatables->select('pb.id, pb.bank, CONCAT(a.kode," ",a.akun) AS kode_akun, CONCAT(a2.kode," ",a2.akun) AS kode_akun_2');
        $this->datatables->from('pil_bank pb');
        //add this line for join
        $this->datatables->join('(SELECT * FROM akun WHERE parent="bank") AS a', 'a.uid=pb.id', 'left');
        $this->datatables->join('(SELECT * FROM akun WHERE parent="bank2") AS a2', 'a2.uid=pb.id', 'left');

        $this->datatables->add_column('action', anchor(site_url('admin/pil_bank/read/$1'),'<button type="button" class="btn btn-xs btn-success"><i class="ace-icon fa fa-check bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('admin/pil_bank/update/$1'),'<button type="button" class="btn btn-xs btn-info"><i class="ace-icon fa fa-pencil bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('admin/pil_bank/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javascript: return confirm(\'Are You Sure ?\')"'), 'id');
        return $this->datatables->generate();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->select('pb.*, CONCAT(a.kode," ",a.akun) AS kode_akun, CONCAT(a2.kode," ",a2.akun) AS kode_akun_2');
        $this->db->from($this->table.' pb');
        $this->db->join('(SELECT * FROM akun WHERE parent="bank") AS a', 'a.uid=pb.id', 'left');
        $this->db->join('(SELECT * FROM akun WHERE parent="bank2") AS a2', 'a2.uid=pb.id', 'left');
        $this->db->where('pb.'.$this->id, $id);
        return $this->db->get()->row();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('id', $q);
	$this->db->or_like('bank', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
	$this->db->or_like('bank', $q);
	$this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

}

/* End of file Pil_bank_model.php */
/* Location: ./application/models/Pil_bank_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2020-02-18 02:28:42 */
/* http://harviacode.com */