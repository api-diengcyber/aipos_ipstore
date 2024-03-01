<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Group_cs_model extends CI_Model
{

    public $table = 'group_cs';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // datatables
    function json() {
        $this->datatables->select('gc.id,gc.group,u.first_name as nama_advertiser');
        $this->datatables->from('group_cs gc');
        $this->datatables->join('users u', 'gc.id_advertiser = u.id_users AND u.level = 10','left');
        $this->datatables->add_column('action', anchor(site_url('admin/group_cs/update/$1'),'Update')." | ".anchor(site_url('admin/group_cs/delete/$1'),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id');
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


    function get_by_id_advertiser($id_advertiser)
    {
        return $this->db->where('id_advertiser',$id_advertiser)->get('group_cs')->result();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('id', $q);
        $this->db->or_like('group', $q);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
        $this->db->or_like('group', $q);
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

/* End of file Group_cs_model.php */
/* Location: ./application/models/Group_cs_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2019-09-16 09:45:01 */
/* http://harviacode.com */