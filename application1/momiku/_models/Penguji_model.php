<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Penguji_model extends CI_Model
{

    public $table = 'penguji';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // datatables
    function json() {
        $this->datatables->select('id,nama,alamat,email,nama_usaha,deskripsi_usaha,pendidikan,lama_usaha,jabatan,hp_wa');
        $this->datatables->from('penguji');
        //add this line for join
        //$this->datatables->join('table2', 'penguji.field = table2.field');
        $this->datatables->add_column('action', anchor(site_url('penguji/read/$1'),'Read')." | ".anchor(site_url('penguji/update/$1'),'Update')." | ".anchor(site_url('penguji/delete/$1'),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id');
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
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('id', $q);
	$this->db->or_like('nama', $q);
	$this->db->or_like('alamat', $q);
	$this->db->or_like('email', $q);
	$this->db->or_like('nama_usaha', $q);
	$this->db->or_like('deskripsi_usaha', $q);
	$this->db->or_like('pendidikan', $q);
	$this->db->or_like('lama_usaha', $q);
	$this->db->or_like('jabatan', $q);
	$this->db->or_like('hp_wa', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
	$this->db->or_like('nama', $q);
	$this->db->or_like('alamat', $q);
	$this->db->or_like('email', $q);
	$this->db->or_like('nama_usaha', $q);
	$this->db->or_like('deskripsi_usaha', $q);
	$this->db->or_like('pendidikan', $q);
	$this->db->or_like('lama_usaha', $q);
	$this->db->or_like('jabatan', $q);
	$this->db->or_like('hp_wa', $q);
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

/* End of file Penguji_model.php */
/* Location: ./application/models/Penguji_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2018-02-02 10:26:14 */
/* http://harviacode.com */