<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pil_menu_model extends CI_Model
{

    public $table = 'pil_menu';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // datatables
    function json() {
        $this->datatables->select('pb.id, pb.bank, CONCAT(a.kode," ",a.akun) AS kode_akun, CONCAT(a2.kode," ",a2.akun) AS kode_akun_2');
        $this->datatables->from('pil_menu pb');
        //add this line for join
        $this->datatables->join('(SELECT * FROM akun WHERE parent="bank") AS a', 'a.uid=pb.id', 'left');
        $this->datatables->join('(SELECT * FROM akun WHERE parent="bank2") AS a2', 'a2.uid=pb.id', 'left');
        $this->datatables->add_column('action', anchor(site_url('admin/pil_menu/read/$1'),'<button type="button" class="btn btn-xs btn-success"><i class="ace-icon fa fa-check bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('admin/pil_menu/update/$1'),'<button type="button" class="btn btn-xs btn-info"><i class="ace-icon fa fa-pencil bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('admin/pil_menu/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javascript: return confirm(\'Are You Sure ?\')"'), 'id');
        return $this->datatables->generate();
    }

    function ordered($res = array())
    {
        $arr = (array) array();
        foreach ($res as $key) {
            if (!empty($key->id_parent)) {
                $key->level = 2;
                if (!empty($arr[$key->id_parent])) {
                    $next = count($arr[$key->id_parent])+1;
                } else {
                    $next = 1;
                }
                $arr[$key->id_parent][$next] = $key;
            } else {
                $key->level = 1;
                $arr[$key->id][0] = $key;
            }
        }
        $arr2 = array();
        foreach ($arr as $key2) {
            ksort($key2);
            foreach ($key2 as $key3) {
                $arr2[] = $key3;
            }
        }
        return $arr2;
    }

    // get menu
    function get_menu($level)
    {
        $this->db->select('m.*, GROUP_CONCAT(DISTINCT CONCAT("isset($", m2.var_active, ")") SEPARATOR "||") AS var_active_menu');
        $this->db->from('user_menu um');
        $this->db->join('pil_menu m', 'um.id_menu=m.id');
        $this->db->join('(SELECT pm.* FROM pil_menu pm JOIN user_menu umm ON pm.id=umm.id_menu WHERE umm.level="'.$level.'" GROUP BY pm.id) AS m2', 'm.id=m2.id_parent', 'left');
        $this->db->where('um.level', $level);
        // $this->db->where('m.status', 0);
        $this->db->group_by('m.id');
        $this->db->order_by('m.id', 'asc');
        return $this->ordered($this->db->get()->result());
    }

    // get all
    function get_all($id_toko)
    {
        $this->db->select('pm.*, IFNULL(pm2.nama_menu, "#") AS nama_parent');
        $this->db->from('pil_menu pm');
        $this->db->join('pil_menu pm2', 'pm.id_parent=pm2.id', 'left');
        $this->db->where('pm.id_toko', $id_toko);
        $this->db->order_by('pm.id', 'asc');
        return $this->ordered($this->db->get()->result());
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

/* End of file Pil_menu_model.php */
/* Location: ./application/models/Pil_menu_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2020-02-18 02:28:42 */
/* http://harviacode.com */