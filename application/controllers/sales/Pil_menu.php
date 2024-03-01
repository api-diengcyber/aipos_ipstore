<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pil_menu extends AI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->models('Pil_menu_model');
  }

  public function index()
  {
    $this->db->select('l.*, GROUP_CONCAT(DISTINCT um.id_menu) AS menus');
    $this->db->from('users_level_lookup l');
    $this->db->join('user_menu um', 'l.id=um.level', 'left');
    $this->db->group_by('l.id');
    $res_user = $this->db->get()->result();
    $data = array(
      'active_pil_menu' => 'active',
      'action' => site_url('admin/pil_menu/create_action'),
      'data_users' => $res_user,
      'data_pil_menu' => $this->Pil_menu_model->get_all($this->userdata->id_toko),
    );
    $this->rview('pil_menu/pil_menu_list', $data);
  } 
    
	public function create_action() 
	{
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
		$this->db->truncate('user_menu');
		$list_menu = $this->input->post('list_menu');
		foreach ($list_menu as $level => $value) {
			foreach ($value as $id_menu => $value2) {
				$data_insert = array(
					'id_toko' => $this->userdata->id_toko,
					'level' => $level,
					'id_menu' => $id_menu,
				);
				$this->db->insert('user_menu', $data_insert);
			}
		}
		$this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
		redirect('admin/pil_menu','refresh');
	}

	public function update()
	{
		$this->db->select('*');
		$this->db->from('pil_menu');
		$this->db->where('(var_active IS NULL OR var_active="")');
		$res_parent = $this->db->get()->result();
    $data = array(
			'active_pil_menu' => 'active',
			'data_parent' => $res_parent,
      'data_pil_menu' => $this->Pil_menu_model->get_all($this->userdata->id_toko),
    );
    $this->rview('pil_menu/pil_menu_update', $data);
	}

	public function update_action()
	{
		$nama_menu = $this->input->post('nama_menu');
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
		$this->db->truncate('pil_menu');
		$this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
		$new_id_parent = 0;
		$array_ids = array();
		foreach ($nama_menu as $old_id => $nama) {
			$id_parent = $this->input->post('id_parent')[$old_id];
			$nama_icon = $this->input->post('nama_icon')[$old_id];
			$var_active = $this->input->post('var_active')[$old_id];
			$controller = $this->input->post('controller')[$old_id];
			if (empty($id_parent)) {
				$new_id_parent = 0;
			}
			$data_update = array(
				'id_toko' => $this->userdata->id_toko,
				'nama_menu' => $nama,
				'id_parent' => $new_id_parent,
				'icon' => $nama_icon,
				'var_active' => $var_active,
				'controller' => $controller,
			);
			$this->db->insert('pil_menu', $data_update);
			$new_id = $this->db->insert_id();
			$array_ids[$old_id] = $new_id;
			if (empty($id_parent)) {
				$new_id_parent = $new_id;
			}
		}

		$res = $this->db->order_by('id_menu', 'asc')->get('user_menu')->result();
		foreach ($res as $key) {
			$new_id = $array_ids[$key->id_menu];
			$data_upd = array(
				'id_menu' => $new_id
			);
			$this->db->where('id', $key->id);
			$this->db->update('user_menu', $data_upd);
		}
		

		// foreach ($array_ids as $old_id => $new_id) {
		// 	// $data_upd = array(
		// 	// 	'id_menu' => $new_id
		// 	// );
		// 	// $this->db->where('id_menu', $old_id);
		// 	// $this->db->update('user_menu', $data_upd);
		// 	// $this->db->reset_query();
		// 	$this->db->query('UPDATE user_menu SET id_menu="'.$new_id.'" WHERE id_menu="'.$old_id.'"');
			
		// }

			// $data_upd2 = array(
			// 	'id_parent' => $new_id
			// );
			// $this->db->where('id_parent', $old_id);
			// $this->db->update('pil_menu', $data_upd2);

		redirect('admin/pil_menu/update','refresh');
	}

}

/* End of file Pil_menu.php */
/* Location: ./application/controllers/Pil_menu.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2020-02-18 02:28:42 */
/* http://harviacode.com */