<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Penjualan_retail_model extends CI_Model
{

    public $table = 'orders';
    public $id = 'id_orders_2';
    public $id_toko = 'id_toko';
    public $id_users = 'id_users';
    public $pembeli = 'pembeli';
    public $no_faktur = 'no_faktur';
    public $tgl_order = 'tgl_order';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
        $this->load->model('Orders_detail_retail_model');
        $this->load->model('Produk_retail_model');
        
        
    }

    // datatables
    function json($id_toko) {
        $this->datatables->select('o.id_orders, o.id_orders_2, o.id_toko, o.id_users, o.no_faktur, o.pembeli, o.pembayaran, o.deadline, o.tgl_order, o.jam_order, o.nominal, o.laba, u.first_name AS nama_kasir, SUM(rj.total) AS total_retur');
        $this->datatables->from('orders o');
        // $this->datatables->join('(SELECT u.* FROM users u WHERE id_users = u.id_users AND u.id_toko = id_toko LIMIT 0, 1) AS u', 'u.id_toko > 0');
        $this->datatables->join('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->datatables->join('retur_jual rj', 'o.no_faktur=rj.no_faktur AND rj.id_users=u.id_users AND rj.id_users=u.id_users AND o.id_toko=rj.id_toko', 'left');
        $this->datatables->where('o.id_toko', $this->userdata->id_toko);
        // $this->datatables->where('u.id_cabang', $this->userdata->id_cabang);
        $this->datatables->group_by('o.id_orders');
        //$this->datatables->add_column('action', anchor(site_url('outlet/penjualan_retail/read/$1'),'<button type="button" class="btn btn-xs btn-success"><i class="ace-icon fa fa-check bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('outlet/penjualan_retail/update/$1'),'<button type="button" class="btn btn-xs btn-info"><i class="ace-icon fa fa-pencil bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('outlet/penjualan_retail/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id_orders_2');
        if($this->userdata->level ==1){
        $this->datatables->add_column('action', anchor(site_url('admin/penjualan_retail/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id_orders_2');
        }else{
        $this->datatables->add_column('action', anchor(site_url('outlet/penjualan_retail/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id_orders_2');
        }
        return $this->datatables->generate();
    }

    // datatables
    function json_produksi($id_toko) {
        $this->datatables->select('o.id_orders, o.id_orders_2, o.id_toko, o.id_users, o.no_faktur, o.nama_kustomer, o.pembeli, o.pembayaran, o.deadline, o.tgl_order, o.jam_order, o.nominal, o.laba, u.first_name AS nama_kasir, SUM(rj.total) AS total_retur');
        $this->datatables->from('orders o');
        // $this->datatables->join('(SELECT u.* FROM users u WHERE id_users = u.id_users AND u.id_toko = id_toko LIMIT 0, 1) AS u', 'u.id_toko > 0');
        $this->datatables->join('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->datatables->join('retur_jual rj', 'o.no_faktur=rj.no_faktur AND rj.id_users=u.id_users AND rj.id_users=u.id_users AND o.id_toko=rj.id_toko', 'left');
        $this->datatables->where('o.id_toko', $this->userdata->id_toko);
        // $this->datatables->where('u.id_cabang', $this->userdata->id_cabang);
        $this->datatables->group_by('o.id_orders');
        //$this->datatables->add_column('action', anchor(site_url('produksi/penjualan_retail/read/$1'),'<button type="button" class="btn btn-xs btn-success"><i class="ace-icon fa fa-check bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('produksi/penjualan_retail/update/$1'),'<button type="button" class="btn btn-xs btn-info"><i class="ace-icon fa fa-pencil bigger-120"></i></button>')."&nbsp;&nbsp;&nbsp;&nbsp;".anchor(site_url('produksi/penjualan_retail/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id_orders_2');
        $this->datatables->add_column('action', anchor(site_url('produksi/penjualan_retail/delete/$1'),'<button type="button" class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash bigger-120"></i></button>','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'), 'id_orders_2');
        return $this->datatables->generate();
    }

    // get all
    function get_all()
    {
        $this->db->select('o.*');
        $this->db->from('orders o');
        $this->db->from('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->order_by('o.id_orders_2', $this->order);
        $this->db->group_by('o.id_orders_2');
        return $this->db->get()->result();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->select('o.*');
        $this->db->from('orders o');
        $this->db->from('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->where('o.id_orders_2', $id);
        return $this->db->get()->row();
    }

    // get data by no faktur
    function get_by_no_faktur($no_faktur)
    {
        $this->db->select('o.*, rj.no_retur, rj.tgl AS tgl_retur, rj.id_retur');
        $this->db->from('orders o');
        $this->db->join('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->db->join('retur_jual rj', 'o.no_faktur=rj.no_faktur AND o.id_toko=rj.id_toko', 'left');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->where('o.no_faktur', $no_faktur);
        return $this->db->get()->row();
    }

    // get data by pembeli
    function get_by_pembeli($pembeli)
    {
        $this->db->select('o.*');
        $this->db->from('orders o');
        $this->db->from('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->where('o.pembeli', $pembeli);
        $this->db->order_by('o.id_orders_2', $this->order);
        $this->db->group_by('o.id_orders_2');
        return $this->db->get()->result();
    }

    // get data by id toko
    function get_by_id_toko($id_toko)
    {
        $this->db->select('o.*');
        $this->db->from('orders o');
        $this->db->from('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->order_by('o.id_orders_2', $this->order);
        $this->db->group_by('o.id_orders_2');
        return $this->db->get()->result();
    }

    // get data by tgl_order
    function get_by_tgl($id_toko, $tgl_order, $id_member = 0)
    {
        $this->db->select('SUM(o.nominal) AS nominal, SUBSTRING(o.jam_order,1,5) AS jam, SUM(o.laba) AS laba');
        $this->db->from('(SELECT o.*, u.id_cabang FROM orders o JOIN users u ON u.id_users=o.id_users AND u.id_toko=o.id_toko GROUP BY o.id_orders_2) AS o');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        $this->db->where('o.id_cabang', $this->userdata->id_cabang);
        $this->db->where('o.tgl_order', $tgl_order);
        if (!empty($id_member)) {
            $this->db->where('o.pembeli', $id_member);
        }
        $this->db->group_by('SUBSTRING(o.jam_order,1,2)');
        return $this->db->get()->result();
    }

    // get data by bulan tahun
    function get_by_bulan_tahun($id_toko, $bulan, $tahun)
    {
        $this->db->select('SUM(o.nominal) AS nominal, SUBSTRING(o.tgl_order,1,2) AS hari, SUM(o.laba) AS laba');
        $this->db->from('(SELECT o.*, u.id_cabang FROM orders o JOIN users u ON u.id_users=o.id_users AND u.id_toko=o.id_toko GROUP BY o.id_orders_2) AS o');
        $this->db->where("SUBSTRING(o.tgl_order,4,2)", $bulan);
        $this->db->where("SUBSTRING(o.tgl_order,7,4)", $tahun);
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        $this->db->where('o.id_cabang', $this->userdata->id_cabang);
        $this->db->group_by('o.tgl_order');
        return $this->db->get()->result();
    }
    
    // get nomor by id_toko
    function get_nomor($id_toko, $tgl_order)
    {
        $this->db->select('RIGHT(no_faktur,4) AS nomor');
        // $this->db->select('no_faktur AS nomor');
        $this->db->from($this->table);
        $this->db->where('id_toko', $this->userdata->id_toko);
        $this->db->where($this->tgl_order, $tgl_order);
        $this->db->order_by('nomor', $this->order);
        return $this->db->get()->row();
    }

    // gwet sdataby isd pwegaweai/ usdwer
    function get_group_by_id_users($id_toko)
    {
        $this->db->select('COUNT(o.id_orders_2) AS jml_order, o.id_users');
        $this->db->from('orders o');
        $this->db->join('users u', 'u.id_users=o.id_users AND u.id_toko=o.id_toko');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->group_by('o.id_users');
        return $this->db->get()->result();
    }

    // get data terakhir
    function get_last_data($id_toko, $limit)
    {
        $this->db->where('id_toko', $this->userdata->id_toko);
        $this->db->order_by($this->id, 'DESC');
        return $this->db->get($this->table, $limit)->result();
    }

    function cek_retur_jual($id_toko)
    {
        return $this->db->query('SELECT rj.* FROM retur_jual rj JOIN users u ON rj.id_users=u.id_users AND rj.id_toko=u.id_toko WHERE rj.id_toko="'.$id_toko.'" AND u.id_cabang="'.$this->userdata->id_cabang.'"')->row();
    }

    // get by between date 
    function get_by_between_date($id_toko, $tgl1='', $tgl2='', $pembayaran='',$bank='',$no_faktur='')
    {
        /*
        $this->db->select('orders.*, users.first_name, users.last_name');
        $this->db->from($this->table);
        $this->db->join('users', 'orders.id_users=users.id_users');
        $this->db->where("DATE(CONCAT(SUBSTRING(orders.tgl_order,7,4),'-',SUBSTRING(orders.tgl_order,4,2),'-',SUBSTRING(orders.tgl_order,1,2))) BETWEEN '".$tgl1."' AND '".$tgl2."'");
        $this->db->where('orders.id_toko', $this->userdata->id_toko);
        return $this->db->get()->result();
        */
        $this->db->select('o.*,o.id_orders_2 as id_o, od.*, u.first_name,u.last_name, (o.nominal) AS nominal, SUM(rjd.jumlah) AS jumlah_retur, m.nama AS nama_pembeli,pr.id_produk_2 as idp');
        $this->db->from('orders o');
        $this->db->join('users u', 'u.id_toko=o.id_toko AND u.id_users=o.id_users');
        $this->db->join('member m', 'o.id_toko=m.id_toko AND o.pembeli=m.id_member', 'left');
        $this->db->join('orders_detail od', 'o.id_orders_2=od.id_orders_2 AND od.id_karyawan=u.id_users AND od.id_toko=o.id_toko', 'left');
        
        $this->db->join('retur_jual_detail rjd', 'rjd.id_orders_detail=od.id_orders AND rjd.id_users=u.id_users AND rjd.id_toko=od.id_toko', 'left');
        
        $this->db->join('produk_retail pr', 'pr.id_produk_2=od.id_produk AND pr.id_toko=od.id_toko','left');
        
        
        
        
        if ($this->cek_retur_jual($id_toko)) {
            // $this->db->join('(SELECT * FROM retur_jual WHERE id_toko='.$this->userdata->id_toko.') r', 'r.no_faktur!=o.no_faktur');
            $this->db->join('retur_jual r', 'r.no_faktur=o.no_faktur AND r.id_users=u.id_users AND r.id_toko=o.id_toko', 'left');
        }
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        $this->db->where("DATE(CONCAT(SUBSTRING(o.tgl_order,7,4),'-',SUBSTRING(o.tgl_order,4,2),'-',SUBSTRING(o.tgl_order,1,2))) BETWEEN '".$tgl1."' AND '".$tgl2."'");
        // if (!empty($id_member)) {
        //     $this->db->where('o.pembeli', $id_member);
        // }
        
        if (!empty($pembayaran)) {
            if($pembayaran==1||$pembayaran==2||$pembayaran==4){
                $this->db->where('o.pembayaran', $pembayaran);
            }else{
                $this->db->where('o.bank', $bank);
            }
        }
        if (!empty($no_faktur)) {
            // $this->db->like('o.no_faktur', $no_faktur);
            $this->db->like('pr.barcode', $no_faktur);
        }


        $this->db->where_in('o.pembayaran', array('1','2','3','4'));
        $this->db->group_by('o.no_faktur');
        return $this->db->get()->result();
    }

    // get by between date and user
    function get_by_between_date_and_user($id_toko, $id_users, $tgl1='', $tgl2='' ,$pembayaran='')
    {
        $this->db->select('o.*, u.first_name, u.last_name, (o.nominal) AS nominal, m.nama AS nama_pembeli');
        $this->db->from('orders o');
        $this->db->join('users u', 'o.id_users=u.id_users and u.id_toko = '.$this->userdata->id_toko.'', 'left');
        $this->db->join('member m', 'o.id_toko=m.id_toko AND o.pembeli=m.id_member', 'left');
        if ($this->cek_retur_jual($id_toko)) {
            // $this->db->join('(SELECT * FROM retur_jual WHERE id_toko='.$this->userdata->id_toko.') r', 'r.no_faktur!=o.no_faktur');
            $this->db->join('retur_jual r', 'r.no_faktur=o.no_faktur AND r.id_users=u.id_users AND r.id_toko=o.id_toko', 'left');
        }
        $this->db->where("DATE(CONCAT(SUBSTRING(o.tgl_order,7,4),'-',SUBSTRING(o.tgl_order,4,2),'-',SUBSTRING(o.tgl_order,1,2))) BETWEEN '".$tgl1."' AND '".$tgl2."'");
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->where('o.id_users', $id_users);
        $this->db->where_in('o.pembayaran', array('1','2','3','4'));
        $this->db->group_by('o.no_faktur');
        return $this->db->get()->result();
    }

    // get by tgl
    function get_by_tgl2($id_toko, $tgl)
    {
        $this->db->select('o.*');
        $this->db->from('orders o');
        $this->db->join('users u', 'o.id_users=u.id_users AND o.id_toko=u.id_toko');
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        // $this->db->where('u.id_cabang', $this->userdata->id_cabang);
        $this->db->where('o.tgl_order', $tgl);
        $this->db->group_by('o.id_orders_2');
        return $this->db->get()->result();
    }

    // get by between date 2
    function get_by_between_date_2($id_toko, $tgl1='', $tgl2='')
    {
        $this->db->select('SUM(o.nominal) AS nominal, SUBSTRING(o.tgl_order,1,2) AS tgl, SUM(o.laba) AS laba');
        $this->db->from("(SELECT o.*, u.id_cabang FROM orders o JOIN users u ON o.id_users=u.id_users AND o.id_toko=u.id_toko GROUP BY o.id_orders_2) AS o");
        $this->db->where("DATE(CONCAT(SUBSTRING(o.tgl_order,7,4),'-',SUBSTRING(o.tgl_order,4,2),'-',SUBSTRING(o.tgl_order,1,2))) BETWEEN '".$tgl1."' AND '".$tgl2."'");
        $this->db->where('o.id_toko', $this->userdata->id_toko);
        $this->db->where('o.id_cabang', $this->userdata->id_cabang);
        $this->db->group_by('o.tgl_order');
        return $this->db->get()->result();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('id_orders', $q);
        $this->db->or_like('id_toko', $q);
        $this->db->or_like('id_users', $q);
        $this->db->or_like('no_faktur', $q);
        $this->db->or_like('nama_kustomer', $q);
        $this->db->or_like('pembeli', $q);
        $this->db->or_like('pembayaran', $q);
        $this->db->or_like('deadline', $q);
        $this->db->or_like('tgl_order', $q);
        $this->db->or_like('jam_order', $q);
        $this->db->or_like('nominal', $q);
        $this->db->or_like('laba', $q);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id_orders', $q);
        $this->db->or_like('id_toko', $q);
        $this->db->or_like('id_users', $q);
        $this->db->or_like('no_faktur', $q);
        $this->db->or_like('nama_kustomer', $q);
        $this->db->or_like('pembeli', $q);
        $this->db->or_like('pembayaran', $q);
        $this->db->or_like('deadline', $q);
        $this->db->or_like('tgl_order', $q);
        $this->db->or_like('jam_order', $q);
        $this->db->or_like('nominal', $q);
        $this->db->or_like('laba', $q);
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
        $this->db->where('id_toko', $this->userdata->id_toko);
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id, $id_toko)
    {
        /*
        $result = $this->Orders_detail_retail_model->get_by_id($id);
        $jumlah = 0;
        foreach ($result as $key) {
            $jumlah += $key->jumlah;
            $row = $this->Produk_retail_model->get_by_id_produk($key->id_produk, $this->userdata->id_toko);
            $data_produk = array(
                'dibeli' => $row->dibeli-$key->jumlah,
                'stok' => $row->stok+$key->jumlah,
            );
            $this->Produk_retail_model->update($key->id_produk, $data_produk);
        }
        */
        $this->db->where($this->id, $id);
        $this->db->where("id_toko", $id_toko);
        $this->db->delete($this->table);
        //$this->Orders_detail_retail_model->delete($id);
    }

}

/* End of file Penjualan_retail_model.php */
/* Location: ./application/models/Penjualan_retail_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2017-08-02 09:05:26 */
/* http://harviacode.com */