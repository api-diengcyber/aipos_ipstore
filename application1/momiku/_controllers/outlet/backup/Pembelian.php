<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pembelian extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->library('form_validation');        
	    $this->load->library('datatables');
        $this->load->model('Pengaturan_transaksi_model');
        $this->load->model('Faktur_retail_model');
        $this->load->model('Pembelian_retail_model');
        $this->load->model('Pengaturan_transaksi_model');
        $this->load->model('Login_model');
        $this->load->library('view');
        $this->Login_model->is_outlet();
        $this->userdata = $this->Login_model->get_data();
	}

	public function index()
	{
        $data = array(
            'id_toko' => $this->userdata->id_toko,
            'nama_toko' => $this->userdata->nama_toko,
            'nama_user' => $this->userdata->email,
            'active_pembelian_create' => 'active',
            'id_modul' => $this->userdata->id_modul,
            'nama_modul' => $this->userdata->nama_modul,
            'data_faktur' => $this->Faktur_retail_model->get_all_by_id_toko($this->userdata->id_toko),
        );
        $this->view->render_outlet('pembelian2/pembelian_list', $data);
	}
    
    public function json($id_faktur) {
        header('Content-Type: application/json');
        echo $this->Pembelian_retail_model->json_faktur($this->userdata->id_toko, $id_faktur);
    }

    public function read($id_faktur = NULL)
    {
        $row = $this->db->select('fr.*, s.nama_supplier')
        				->from('faktur_retail fr')
        				->join('users u', 'fr.id_users=u.id_users AND fr.id_toko=u.id_toko')
        				->join('supplier s', 'fr.id_supplier=s.id_supplier AND s.id_toko=u.id_toko AND fr.id_toko=s.id_toko')
        				->where('fr.id_toko', $this->userdata->id_toko)
        				->where('fr.id_cabang', $this->userdata->id_cabang)
        				->where('fr.id_faktur', $id_faktur)
        				->get()->row();
        // $row = $this->Faktur_retail_model->get_by_id_faktur($id_faktur, $this->userdata->id_toko);
        $data = array(
            'id_toko' => $this->userdata->id_toko,
            'nama_toko' => $this->userdata->nama_toko,
            'nama_user' => $this->userdata->email,
            'active_pembelian_create' => 'active',
            'id_modul' => $this->userdata->id_modul,
            'nama_modul' => $this->userdata->nama_modul,
        	'data' => $row,
            'id_faktur' => $row->id_faktur,
            'no_faktur' => $row->no_faktur,
        );
        $this->view->render_outlet('pembelian2/pembelian_read', $data);
    }

	public function create()
	{
		$data_supplier = $this->db->select('s.*')
								  ->from('supplier s')
								  ->join('users u', 'u.id_users=s.id_users AND u.id_toko=s.id_toko')
								  ->where('s.id_toko', $this->userdata->id_toko)
								  ->where('u.id_cabang', $this->userdata->id_cabang)
								  ->group_by('s.id_supplier')
								  ->get()->result();
        $data = array(
            'id_toko' => $this->userdata->id_toko,
            'id_users' => $this->userdata->id_users,
            'nama_toko' => $this->userdata->nama_toko,
            'nama_user' => $this->userdata->email,
            'active_pembelian_create' => 'active',
            'id_modul' => $this->userdata->id_modul,
            'nama_modul' => $this->userdata->nama_modul,
        	'tgl' => set_value('tgl', date('d-m-Y')),
        	'supplier' => set_value('supplier'),
        	'pembayaran' => set_value('pembayaran'),
            'data_supplier' => $data_supplier,
        );
        $this->view->render_outlet('pembelian2/pembelian_form', $data);
	}

	public function tambah_temp()
	{
		header('Content-Type: application/json');
		$barcode = $this->input->post('barcode', true);
		$row_produk = $this->db->select('pr.*')
							   ->from('produk_retail pr')
							   ->join('users u', 'pr.id_users=u.id_users AND pr.id_toko=u.id_toko')
							   ->where('pr.barcode', $barcode)
							   ->where('pr.id_toko', $this->userdata->id_toko)
							   ->where('u.id_cabang', $this->userdata->id_cabang)
							   ->group_by('pr.id_produk_2')
							   ->get()->row();
		if ($row_produk) {
			$row_temp = $this->db->select('pt.*')
								 ->from('pembelian_temp pt')
								 ->join('users u', 'pt.id_users=u.id_users AND pt.id_toko=u.id_toko')
								 ->where('pt.id_produk', $row_produk->id_produk_2)
								 ->where('pt.id_toko', $this->userdata->id_toko)
							     ->where('u.id_cabang', $this->userdata->id_cabang)
							     ->group_by('pt.id')
								 ->get()->row();
			if ($row_temp) {
				$data_update = array(
					'jumlah' => $row_temp->jumlah+1
				);
				$this->db->where('id', $row_temp->id);
				$this->db->update('pembelian_temp', $data_update);
			} else {
				$data_insert = array(
					'id_toko' => $this->userdata->id_toko,
					'id_produk' => $row_produk->id_produk_2,
					'id_users' => $this->userdata->id_users,
					'harga_satuan' => 0,
					'jumlah' => 1,
					'diskon' => 0,
					'subtotal' => 0,
				);
				$this->db->insert('pembelian_temp', $data_insert);
			}
		}
		$data = array(
			'status' => "ok"
		);
		echo json_encode($data);
	}

	public function list_temp()
	{
		header('Content-Type: application/json');
		$res_temp = $this->db->select('pt.*, pr.nama_produk, sp.satuan AS nama_satuan')
							 ->from('pembelian_temp pt')
							 ->join('produk_retail pr', 'pt.id_produk=pr.id_produk_2 AND pt.id_toko=pr.id_toko')
							 ->join('satuan_produk sp', 'pr.satuan=sp.id_satuan AND pr.id_toko=sp.id_toko')
							 ->join('users u', 'pt.id_users=u.id_users AND pt.id_toko=u.id_toko')
							 ->where('pt.id_toko', $this->userdata->id_toko)
							 ->where('u.id_users', $this->userdata->id_users)
							 ->order_by('pr.nama_produk')
							 ->group_by('pt.id')
							 ->get()->result();
		$no = 1;
		$html = '';
		$total = 0;
		foreach ($res_temp as $key) :
			$html .= '<tr>
						<td>'.$no.'</td>
						<td>'.$key->nama_produk.' ('.$key->nama_satuan.')</td>
						<td style="padding:0px;"><input type="text" class="form-control expire_date editable" data-id="'.$key->id.'" value="'.(!empty($key->expire_date)?$key->expire_date:date('d-m-2099')).'" style="border:none;" placeholder="dd-mm-yyyy" /></td>
						<td style="padding:0px;"><input type="text" class="form-control text-right numbering editable harga_satuan" data-id="'.$key->id.'" value="'.number_format($key->harga_satuan,0,',','.').'" style="border:none;" /></td>
						<td style="padding:0px;"><input type="text" class="form-control text-right numbering editable jumlah" data-id="'.$key->id.'" value="'.number_format($key->jumlah,0,',','.').'" style="border:none;" /></td>
						<td style="padding:0px;"><input type="text" class="form-control text-right numbering editable diskon" data-id="'.$key->id.'" value="'.number_format($key->diskon,0,',','.').'" maxlength="3" style="border:none;" /></td>
						<td class="text-right"><span class="subtotal" data-id="'.$key->id.'">'.number_format($key->subtotal,0,',','.').'</span></td>
						<td class="text-center" style="padding:0px;"><button type="button" class="btn btn-danger btn-xs btn-hapus-temp" data-id="'.$key->id.'"><i class="ace-icon fa fa-trash"></i></button></td>
					</tr>';
			$no++;
			$total+=$key->subtotal;
		endforeach;
		$data = array(
			'status' => "ok",
			'data' => $html,
		);
		echo json_encode($data);
	}

	public function update_temp()
	{
		header('Content-Type: application/json');
		$id = $this->input->post('id', true);
		$data_update = array(
			'expire_date' => $this->input->post('expire_date', true),
			'harga_satuan' => $this->input->post('harga_satuan', true),
			'jumlah' => $this->input->post('jumlah', true),
			'diskon' => $this->input->post('diskon', true),
			'subtotal' => $this->input->post('subtotal', true),
		);
		$this->db->where('id', $id);
		$this->db->where('id_toko', $this->userdata->id_toko);
		$this->db->where('id_users', $this->userdata->id_users);
		$this->db->update('pembelian_temp', $data_update);
		$data = array(
			'status' => "ok"
		);
		echo json_encode($data);
	}

	public function hapus_temp()
	{
		header('Content-Type: application/json');
		$id = $this->input->post('id', true);
		$this->db->where('id', $id);
		$this->db->delete('pembelian_temp');
		$data = array(
			'status' => "ok"
		);
		echo json_encode($data);
	}

	public function create_action()
	{
		$this->_rules();
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
			$res_temp = $this->db->select('pt.*, pr.nama_produk, sp.satuan AS nama_satuan')
								 ->from('pembelian_temp pt')
								 ->join('produk_retail pr', 'pt.id_produk=pr.id_produk_2 AND pt.id_toko=pr.id_toko')
								 ->join('satuan_produk sp', 'pr.satuan=sp.id_satuan AND pr.id_toko=sp.id_toko')
								 ->join('users u', 'pt.id_users=u.id_users AND pt.id_toko=u.id_toko')
								 ->where('pt.id_toko', $this->userdata->id_toko)
								 ->where('u.id_users', $this->userdata->id_users)
								 ->order_by('pr.nama_produk')
								 ->group_by('pt.id')
								 ->get()->result();
            if (count($res_temp) > 0) {
				$tgl = $this->input->post('tgl', true);
				$id_supplier = $this->input->post('supplier', true);
				$pembayaran = $this->input->post('pembayaran', true);
				$jatuh_tempo = $this->input->post('jatuh_tempo', true);
				$total_bruto = $this->to_number($this->input->post('total_bruto', true))*1;
				$total = $this->to_number($this->input->post('total', true))*1;
				$nominal_ppn = $this->to_number($this->input->post('nominal_ppn', true))*1;
				$total_ppn = $this->to_number($this->input->post('total_ppn', true))*1;
				$id_faktur = 1;
				$row_last_faktur = $this->db->select('*')->from('faktur_retail')->where('id_toko', $this->userdata->id_toko)->order_by('id', 'desc')->get()->row();
				if ($row_last_faktur) {
					$id_faktur = $row_last_faktur->id_faktur+1;
				}
	            $urutan = $this->Faktur_retail_model->get_faktur_hari_ini()->count+1;
	            $no_faktur = date('dmy').$id_supplier.$pembayaran.$urutan;
				$data_insert = array(
					'id_faktur' => $id_faktur,
					'id_toko' => $this->userdata->id_toko,
					'id_users' => $this->userdata->id_users,
					'tgl' => $tgl,
					'no_faktur' => $no_faktur,
					'id_supplier' => $id_supplier,
					'total' => $total,
					'ppn_nominal' => $nominal_ppn,
					'total_ppn' => $total_ppn,
					'pembayaran' => $pembayaran,
					'deadline' => $pembayaran=="1"?$jatuh_tempo:"",
				);
	            $this->Faktur_retail_model->insert($data_insert);
	            if ($pembayaran=="1") { // HUTANG / KREDIT
	            	$data_insert = array(
	            		'tgl' => $tgl,
						'id_toko' => $this->userdata->id_toko,
						'id_users' => $this->userdata->id_users,
						'id_supplier' => $id_supplier,
						'id_users' => $this->userdata->id_users,
						'id_faktur' => $id_faktur,
						'deadline' => $jatuh_tempo,
						'hutang' => $total,
						'kurang' => $total,
	            	);
	            	$this->db->insert('hutang', $data_insert);
	            	$id_hutang = $this->db->insert_id();
	            }
	            $nominal_diskon = 0;
	            foreach ($res_temp as $key) :
	            	$id_pembelian = 1;
	            	$row_last_pembelian = $this->db->where('id_toko', $this->userdata->id_toko)->order_by('id', 'desc')->get('pembelian')->row();
	            	if ($row_last_pembelian) {
	            		$id_pembelian = $row_last_pembelian->id_pembelian+1;
	            	}
		            $diskon_nominal = ($key->harga_satuan*$key->jumlah)-$key->subtotal;
		            $nominal_diskon += $diskon_nominal;
		            $data_insert = array(
						'id_pembelian' => $id_pembelian,
						'id_toko' => $this->userdata->id_toko,
						'id_users' => $this->userdata->id_users,
						'id_faktur' => $id_faktur,
						'id_produk' => $key->id_produk,
						'tgl_masuk' => $tgl,
						'tgl_expire' => $key->expire_date,
						'id_supplier' => $id_supplier,
						'pembayaran' => $pembayaran,
						'harga_satuan' => $key->harga_satuan,
						'jumlah' => $key->jumlah,
						'diskon' => $key->diskon,
						'total_bayar' => $key->subtotal,
						//'ppn' => (10/100)*$key->subtotal, 
						'ppn' => 0,
		            );
		            $this->db->insert('pembelian', $data_insert);
		            $data_insert = array(
						'id_toko' => $this->userdata->id_toko,
						'id_pembelian' => $id_pembelian,
						'id_produk' => $key->id_produk,
						'stok' => $key->jumlah,
		            );
		            $this->db->insert('stok_produk', $data_insert);
	            endforeach;
	            $this->db->where('id_toko', $this->userdata->id_toko);
	            $this->db->delete('pembelian_temp');
	            eval($this->Pengaturan_transaksi_model->accounting('pembelian'));
	            $this->session->set_flashdata('message', 'Save Record Success');
            	redirect('outlet/pembelian/create');
            } else {
	            $this->session->set_flashdata('message', 'Save Record Failed');
            	redirect('outlet/pembelian/create');
            }
		}
	}

    public function _rules() 
    {
        $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('tgl', 'Tanggal Masuk', 'trim|required');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

	public function update($id_faktur = '')
	{
        $active = array('active_pembelian_create' => 'active');
		$row_fr = $this->db->select('fr.*')
						   ->from('faktur_retail fr')
						   ->join('users u', 'fr.id_users=u.id_users AND fr.id_toko=u.id_toko')
						   ->where('fr.id_toko', $this->userdata->id_toko)
						   ->where('fr.id_faktur', $id_faktur)
						   ->where('u.id_cabang', $this->userdata->id_cabang)
						   ->get()->row();
		if ($row_fr) {
			$this->db->where('id_toko', $row_fr->id_toko);
			$this->db->where('id_users', $this->userdata->id_users);
			// $this->db->where('id_faktur', $row_fr->id_faktur);
			$this->db->delete('pembelian_temp_update');

			$res_p = $this->db->select('p.*')
							  ->from('pembelian p')
						   	  ->join('users u', 'p.id_users=u.id_users AND p.id_toko=u.id_toko')
							  ->where('p.id_toko', $row_fr->id_toko)
							  ->where('p.id_faktur', $row_fr->id_faktur)
						   	  ->where('u.id_cabang', $this->userdata->id_cabang)
						   	  ->group_by('p.id_pembelian')
							  ->get()->result();
			foreach ($res_p as $key) :
				$data_insert = array(
					'id_toko' => $key->id_toko,
					'id_faktur' => $key->id_faktur,
					'id_pembelian' => $key->id_pembelian,
					'id_produk' => $key->id_produk,
					'id_users' => $key->id_users,
					'expire_date' => $key->tgl_expire,
					'harga_satuan' => $key->harga_satuan,
					'jumlah' => $key->jumlah,
					'diskon' => $key->diskon,
					'subtotal' => $key->total_bayar,
				);
				$this->db->insert('pembelian_temp_update', $data_insert);
			endforeach;
	        $data = array(
	            'id_toko' => $this->userdata->id_toko,
	            'nama_toko' => $this->userdata->nama_toko,
	            'nama_user' => $this->userdata->email,
	            'active_pembelian_create' => 'active',
	            'id_modul' => $this->userdata->id_modul,
	            'nama_modul' => $this->userdata->nama_modul,
				'id_faktur' => set_value('id_faktur', $row_fr->id_faktur),
				'no_faktur' => set_value('no_faktur', $row_fr->no_faktur),
				'tgl' => set_value('tgl', $row_fr->tgl),
				'deadline' => set_value('deadline', $row_fr->deadline),
	        	'supplier' => set_value('supplier', $row_fr->id_supplier),
	        	'pembayaran' => set_value('pembayaran', $row_fr->pembayaran),
	            'data_supplier' => $this->db->where('id_toko', $this->userdata->id_toko)->get('supplier')->result(),
	        );
	        $this->view->render_outlet('pembelian2/pembelian_form_update', $data);
		} else {
            $this->session->set_flashdata('message', 'Record Not Found');
        	redirect('outlet/pembelian');
		}
	}

	public function list_temp_update()
	{
		header('Content-Type: application/json');
		$res_temp = $this->db->select('ptu.*, pr.nama_produk, sp.satuan AS nama_satuan')
							 ->from('pembelian_temp_update ptu')
							 ->join('users u', 'ptu.id_users=u.id_users AND ptu.id_toko=u.id_toko')
							 ->join('produk_retail pr', 'ptu.id_produk=pr.id_produk_2 AND ptu.id_toko=pr.id_toko')
							 ->join('satuan_produk sp', 'pr.satuan=sp.id_satuan AND pr.id_toko=sp.id_toko')
							 ->where('ptu.id_toko', $this->userdata->id_toko)
							 ->where('u.id_cabang', $this->userdata->id_cabang)
							 ->order_by('pr.nama_produk')
							 ->group_by('ptu.id')
							 ->get()->result();
		$no = 1;
		$html = '';
		$total = 0;
		foreach ($res_temp as $key) :
			$html .= '<tr>
						<td>'.$no.'</td>
						<td>'.$key->nama_produk.' ('.$key->nama_satuan.')</td>
						<td style="padding:0px;"><input type="text" class="form-control expire_date editable" data-id="'.$key->id.'" value="'.(!empty($key->expire_date)?$key->expire_date:date('d-m-2099')).'" style="border:none;" placeholder="dd-mm-yyyy" /></td>
						<td style="padding:0px;"><input type="text" class="form-control text-right numbering editable harga_satuan" data-id="'.$key->id.'" value="'.number_format($key->harga_satuan,0,',','.').'" style="border:none;" /></td>
						<td style="padding:0px;"><input type="text" class="form-control text-right numbering editable jumlah" data-id="'.$key->id.'" value="'.number_format($key->jumlah,0,',','.').'" style="border:none;" /></td>
						<td style="padding:0px;"><input type="text" class="form-control text-right numbering editable diskon" data-id="'.$key->id.'" value="'.number_format($key->diskon,0,',','.').'" maxlength="3" style="border:none;" /></td>
						<td class="text-right"><span class="subtotal" data-id="'.$key->id.'">'.number_format($key->subtotal,0,',','.').'</span></td>
						<td class="text-center" style="padding:0px;"><button type="button" class="btn btn-danger btn-xs btn-hapus-temp" data-id="'.$key->id.'"><i class="ace-icon fa fa-trash"></i></button></td>
					</tr>';
			$no++;
			$total+=$key->subtotal;
		endforeach;
		$data = array(
			'status' => "ok",
			'data' => $html,
		);
		echo json_encode($data);
	}

	public function tambah_temp_update()
	{
		header('Content-Type: application/json');
		$id_faktur = $this->input->post('id_faktur', true);
		$barcode = $this->input->post('barcode', true);
		$row_produk = $this->db->select('pr.*')
							   ->from('produk_retail pr')
							   ->join('users u', 'pr.id_users=u.id_users AND pr.id_toko=u.id_toko')
							   ->where('pr.barcode', $barcode)
							   ->where('pr.id_toko', $this->userdata->id_toko)
							   ->where('u.id_cabang', $this->userdata->id_cabang)
							   ->group_by('pr.id_produk_2')
							   ->get()->row();
		if ($row_produk) {
			$row_temp = $this->db->select('pt.*')
								 ->from('pembelian_temp_update pt')
							     ->join('users u', 'pt.id_users=u.id_users AND pt.id_toko=u.id_toko')
								 ->where('pt.id_produk', $row_produk->id_produk_2)
								 ->where('pt.id_toko', $this->userdata->id_toko)
							     ->where('u.id_cabang', $this->userdata->id_cabang)
							     ->group_by('pt.id')
								 ->get()->row();
			if ($row_temp) {
				$data_update = array(
					'jumlah' => $row_temp->jumlah+1
				);
				$this->db->where('id', $row_temp->id);
				$this->db->update('pembelian_temp_update', $data_update);
			} else {
				$data_insert = array(
					'id_toko' => $this->userdata->id_toko,
					'id_users' => $this->userdata->id_users,
					'id_faktur' => $id_faktur,
					'id_produk' => $row_produk->id_produk_2,
					'harga_satuan' => 0,
					'jumlah' => 1,
					'diskon' => 0,
					'subtotal' => 0,
				);
				$this->db->insert('pembelian_temp_update', $data_insert);
			}
		}
		$data = array(
			'status' => "ok"
		);
		echo json_encode($data);
	}

	public function update_temp_update()
	{
		header('Content-Type: application/json');
		$id = $this->input->post('id', true);
		$data_update = array(
			'expire_date' => $this->input->post('expire_date', true),
			'harga_satuan' => $this->input->post('harga_satuan', true),
			'jumlah' => $this->input->post('jumlah', true),
			'diskon' => $this->input->post('diskon', true),
			'subtotal' => $this->input->post('subtotal', true),
		);
		$this->db->where('id', $id);
		$this->db->where('id_toko', $this->userdata->id_toko);
		$this->db->update('pembelian_temp_update', $data_update);
		$data = array(
			'status' => "ok"
		);
		echo json_encode($data);
	}

	public function hapus_temp_update()
	{
		header('Content-Type: application/json');
		$id = $this->input->post('id', true);
		$this->db->where('id', $id);
		$this->db->delete('pembelian_temp_update');
		$data = array(
			'status' => "ok"
		);
		echo json_encode($data);
	}

	function to_number($str_num = "")
	{
		$str_num = str_replace('.', '', $str_num);
		$str_num = str_replace(',', '.', $str_num);
		return (double) $str_num*1;
	}

	public function update_action()
	{
		$this->_rules_update();
        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id_faktur', true));
        } else {
			$id_faktur = $this->input->post('id_faktur', true);
			$total_bruto = $this->to_number($this->input->post('total_bruto', true))*1;
			$total = $this->to_number($this->input->post('total', true))*1;
			$nominal_ppn = $this->to_number($this->input->post('nominal_ppn', true))*1;
			$total_ppn = $this->to_number($this->input->post('total_ppn', true))*1;
			$row_fr = $this->db->select('fr.*')
							   ->from('faktur_retail fr')
							   ->join('users u', 'fr.id_users=u.id_users AND fr.id_toko=u.id_toko')
							   ->where('fr.id_toko', $this->userdata->id_toko)
							   ->where('fr.id_faktur', $id_faktur)
							   ->where('u.id_cabang', $this->userdata->id_cabang)
							   ->get()->row();
			if ($row_fr) {
				$tgl = $row_fr->tgl;
				$id_supplier = $row_fr->id_supplier;
				$id_hutang = 0;
				$no_faktur = $row_fr->no_faktur;
	            $res_temp = $this->db->select('ptu.*, p.jumlah AS jumlah_beli')
	            					 ->from('pembelian_temp_update ptu')
							   		 ->join('users u', 'ptu.id_users=u.id_users AND ptu.id_toko=u.id_toko')
	            					 ->join('pembelian p', 'ptu.id_pembelian=p.id_pembelian AND ptu.id_toko=p.id_toko', 'left')
						             ->where('ptu.id_toko', $this->userdata->id_toko)
						             ->where('ptu.id_faktur', $id_faktur)
							   		 ->where('u.id_cabang', $this->userdata->id_cabang)
							   		 ->group_by('ptu.id')
							         ->get()->result();
	            if (count($res_temp) > 0) {
					$data_update = array(
						'total' => $total,
						'ppn_nominal' => $nominal_ppn,
						'total_ppn' => $total_ppn,
					);
					$this->db->where('id_toko', $this->userdata->id_toko);
					$this->db->where('id_faktur', $id_faktur);
					$this->db->update('faktur_retail', $data_update);
					if ($row_fr->pembayaran=="1") { // HUTANG
						$id_hutang = $row_hutang->id;
						$row_hutang = $this->db->select('h.*')
											   ->from('hutang h')
									   		   ->join('users u', 'h.id_users=u.id_users AND h.id_toko=u.id_toko')
											   ->where('h.id_toko', $this->userdata->id_toko)
											   ->where('h.id_faktur', $row_fr->id_faktur)
							   				   ->where('u.id_cabang', $this->userdata->id_cabang)
											   ->get()->row();
						if ($row_hutang) {
							$data_update_h = array();
							if ($row_hutang->hutang==$row_hutang->bayar) {
								$data_update_h['hutang'] = $total_ppn;
								$data_update_h['bayar'] = $total_ppn;
							} else {
								$data_update_h['hutang'] = $total_ppn;
								$data_update_h['sisa'] = $total_ppn - $row_hutang->bayar;
							}
							$this->db->where('id', $row_hutang->id);
			            	$this->db->update('hutang', $data_update_h);
						}
					}
					$res_jurnal = $this->db->select('j.*')
										   ->from('jurnal j')
										   ->where('j.id_toko', $this->userdata->id_toko)
										   ->like('j.kode', 'PEMBELIAN-TUNAI', 'both')
										   ->where('j.id_proses', $row_fr->id_faktur)
										   ->where('j.no_faktur', $row_fr->no_faktur)
										   ->get()->result();
					foreach ($res_jurnal as $key_j) :
						$this->db->where('id_toko', $this->userdata->id_toko);
						$this->db->where('id', $key_j->id);
						$this->db->delete('jurnal');
					endforeach;
					if ($res_jurnal) {
						$this->db->where('id_toko', $this->userdata->id_toko);
						$this->db->like('kode', 'PEMBELIAN-TUNAI', 'both');
						$this->db->where('id_proses', $row_fr->id_faktur);
						$this->db->where('no_faktur', $row_fr->no_faktur);
						$this->db->delete('jurnal');
					}
		            $nominal_diskon = 0;
	            	foreach ($res_temp as $key) {
	            		if (!empty($key->id_pembelian)) {
				            $diskon_nominal = ($key->harga_satuan*$key->jumlah)-$key->subtotal;
				            $nominal_diskon += $diskon_nominal;
				            $data_update = array(
								'tgl_expire' => $key->expire_date,
								'harga_satuan' => $key->harga_satuan,
								'jumlah' => $key->jumlah,
								'diskon' => $key->diskon,
								'total_bayar' => $key->subtotal,
								//'ppn' => (10/100)*$key->subtotal,
								'ppn' => 0,
				            );
				            $this->db->where('id_toko', $this->userdata->id_toko);
				            $this->db->where('id_pembelian', $key->id_pembelian);
				            $this->db->update('pembelian', $data_update);
				            $row_sp = $this->db->select('sp.*')
				            				   ->from('stok_produk sp')
				            				   ->where('sp.id_toko', $this->userdata->id_toko)
				            				   ->where('sp.id_pembelian', $key->id_pembelian)
				            				   ->get()->row();
							if ($row_sp) {
					            $data_update = array(
									'stok' => ($row_sp->stok-$key->jumlah_beli)+$key->jumlah,
					            );
					            $this->db->where('id_toko', $this->userdata->id_toko);
					            $this->db->where('id_pembelian', $key->id_pembelian);
					            $this->db->update('stok_produk', $data_update);
							}
	            		} else {
			            	$id_pembelian = 1;
			            	$row_last_pembelian = $this->db->where('id_toko', $this->userdata->id_toko)->order_by('id', 'desc')->get('pembelian')->row();
			            	if ($row_last_pembelian) {
			            		$id_pembelian = $row_last_pembelian->id_pembelian+1;
			            	}
				            $diskon_nominal = ($key->harga_satuan*$key->jumlah)-$key->subtotal;
				            $nominal_diskon += $diskon_nominal;
				            $data_insert = array(
								'id_pembelian' => $id_pembelian,
								'id_toko' => $this->userdata->id_toko,
								'id_users' => $this->userdata->id_users,
								'id_faktur' => $id_faktur,
								'id_produk' => $key->id_produk,
								'tgl_masuk' => $row_fr->tgl,
								'tgl_expire' => $key->expire_date,
								'id_supplier' => $row_fr->id_supplier,
								'pembayaran' => $row_fr->pembayaran,
								'harga_satuan' => $key->harga_satuan,
								'jumlah' => $key->jumlah,
								'diskon' => $key->diskon,
								'total_bayar' => $key->subtotal,
								//'ppn' => (10/100)*$key->subtotal,
								'ppn' => 0,
				            );
				            $this->db->insert('pembelian', $data_insert);
				            $data_insert = array(
								'id_toko' => $this->userdata->id_toko,
								'id_pembelian' => $id_pembelian,
								'id_produk' => $key->id_produk,
								'stok' => $key->jumlah,
				            );
				            $this->db->insert('stok_produk', $data_insert);
	            		}
	            	}
					$this->db->where('id_toko', $this->userdata->id_toko);
					$this->db->where('id_faktur', $id_faktur);
					$this->db->delete('pembelian_temp_update');
		            eval($this->Pengaturan_transaksi_model->accounting('pembelian'));
		            $this->session->set_flashdata('message', 'Update Record Success');
	            	redirect('outlet/pembelian');
	            } else {
		            $this->session->set_flashdata('message', 'Data Not Found');
		        	redirect('outlet/pembelian');
	            }
			} else {
	            $this->session->set_flashdata('message', 'Record Not Found');
	        	redirect('outlet/pembelian');
			}
        }
	}

    public function _rules_update() 
    {
        $this->form_validation->set_rules('id_faktur', 'ID Faktur', 'trim|required');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Pembelian.php */
/* Location: ./application/controllers/Pembelian.php */