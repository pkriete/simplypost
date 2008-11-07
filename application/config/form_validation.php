<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
				'backend/login' => array(
								array(
									'field'	=> 'username',
									'label' => 'username',
									'rules' => 'strip_tags|trim|required'
								),
								array(
									'field'	=> 'password',
									'label' => 'password',
									'rules' => 'trim|required'
								),
								array(
									'field'	=> 'act_s',
									'label' => 'act_s',
									'rules' => 'check_login|required'
								)
				),
				'register' => array(
								array(
									'field'	=> 'email',
									'label' => 'e-mail',
									'rules' => 'trim|required|valid_email|check_email'
								),
								array(
									'field'	=> 'username',
									'label' => 'username',
									'rules' => 'strip_tags|trim|required|max_length[15]|check_username'
								),
								array(
									'field'	=> 'password',
									'label' => 'password',
									'rules' => 'trim|required'
								),
								array(
									'field'	=> 'p_confirm',
									'label' => 'confirmation',
									'rules' => 'trim|required|pwd_match[password]'
								)
				),
				'project/create' => array(
								array(
									'field'	=> 'title',
									'label' => 'title',
									'rules' => 'strip_tags|trim|required|p_title_unique'
								),
								array(
									'field'	=> 'description',
									'label' => 'description',
									'rules' => 'strip_tags|trim|'
								)
				),
				'folder/create' => array(
								array(
									'field'	=> 'title',
									'label' => 'title',
									'rules' => 'strip_tags|trim|required'
								),
								array(
									'field'	=> 'description',
									'label' => 'description',
									'rules' => 'strip_tags|trim'
								)
				),
);

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */