<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Coba_aipos_faq_s_k_model extends CI_Model
{

    public $table = 'aipos_faq_s_k';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // datatables
    function json() {
        $this->datatables->select('id,id_k,sub_kategori');
        $this->datatables->from('aipos_faq_s_k');
        //add this line for join
        //$this->datatables->join('table2', 'aipos_faq_s_k.field = table2.field');
        $this->datatables->add_column('action', anchor(site_url('outlet/coba_aipos_faq_s_k/read/$1'),'Read')." | ".anchor(site_url('outlet/coba_aipos_faq_s_k/update/$1'),'Update')." | ".anchor(site_url('outlet/coba_aipos_faq_s_k/delete/$1'),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id');
        return $this->datatables->generate();
    }

    // datatables
    function json_produksi() {
        $this->datatables->select('id,id_k,sub_kategori');
        $this->datatables->from('aipos_faq_s_k');
        //add this line for join
        //$this->datatables->join('table2', 'aipos_faq_s_k.field = table2.field');
        $this->datatables->add_column('action', anchor(site_url('produksi/coba_aipos_faq_s_k/read/$1'),'Read')." | ".anchor(site_url('produksi/coba_aipos_faq_s_k/update/$1'),'Update')." | ".anchor(site_url('produksi/coba_aipos_faq_s_k/delete/$1'),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id');
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
	$this->db->or_like('id_k', $q);
	$this->db->or_like('sub_kategori', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
	$this->db->or_like('id_k', $q);
	$this->db->or_like('sub_kategori', $q);
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

/* End of file Coba_aipos_faq_s_k_model.php */
/* Location: ./application/models/Coba_aipos_faq_s_k_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2019-06-11 09:57:31 */
/* http://harviacode.com */