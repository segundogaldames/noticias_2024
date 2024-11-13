<?php
use models\Role;

class rolesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Roles',
            'subject' => 'Lista de Roles',
            'roles' => Role::select('id','nombre')->get(), #SElECT id, nombre FROM roles;
            'warning' => 'No hay roles registrados'
        ];

        $this->_view->load('roles/index', compact('options','msg_success','msg_error'));
    }

}