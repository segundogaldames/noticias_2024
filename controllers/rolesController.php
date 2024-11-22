<?php
use models\Role;

class rolesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    #metodo head
    public function index()
    {
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Roles',
            'subject' => 'Lista de Roles',
            'roles' => Role::select('id','nombre')->orderBy('id','desc')->get(), #SElECT id, nombre FROM roles;
            'warning' => 'No hay roles registrados',
            'link_create' => 'roles/create',
            'button_create' => 'Nuevo Rol'
        ];

        $this->_view->load('roles/index', compact('options','msg_success','msg_error'));
    }

    #metodo get
    public function create()
    {
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Roles',
            'subject' => 'Nuevo Rol',
            'role' => Session::get('data'),
            'action' => 'create',
            'send' => $this->encrypt($this->getForm()),
            'process' => 'roles/store'
        ];

        $this->_view->load('roles/create', compact('options','msg_success','msg_error'));
    }

    #metodo post
    public function store()
    {
        #print_r($_POST);exit;
        $this->validateForm('roles/create',[
            'nombre' => Filter::getText('nombre')
        ]);

        #comprobar que no haya otro rol con el mismo nombre
        $rol = Role::select('id')->where('nombre', Filter::getText('nombre'))->first();

        if($rol){
            Session::set('msg_error','El rol ingresado ya está registrado... intenta con otro');
            $this->redirect('roles/create');
        }

        $role = new Role;
        $role->nombre = Filter::getText('nombre');
        $role->save();

        Session::destroy('data');
        Session::set('msg_success','El rol se ha registrado correctamente');
        $this->redirect('roles');
    }

    public function show($id = null)
    {
        Validate::validateModel(Role::class, $id, 'error/error');
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Roles',
            'subject' => 'Detalle de Rol',
            'role' => Role::find(Filter::filterInt($id)),
            'warning' => 'No hay un rol asociado'
        ];

        $this->_view->load('roles/show', compact('options','msg_success','msg_error'));
    }

    #metodo get
    public function edit($id = null)
    {
        Validate::validateModel(Role::class, $id, 'error/error');

        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Roles',
            'subject' => 'Editar Rol',
            'role' => Role::find(Filter::filterInt($id)),
            'action' => 'edit',
            'send' => $this->encrypt($this->getForm()),
            'process' => "roles/update/{$id}"
        ];

        $this->_view->load('roles/edit', compact('options','msg_success','msg_error'));

    }

    #metodo put
    public function update($id = null)
    {
        Validate::validateModel(Role::class, $id, 'error/error');
        $this->validatePUT();

        $this->validateForm('roles/create',[
            'nombre' => Filter::getText('nombre')
        ]);

        $role = Role::find(Filter::filterInt($id));
        $role->nombre = Filter::getText('nombre');
        $role->save();

        Session::destroy('data');
        Session::set('msg_success','El rol se ha modificado correctamente');
        $this->redirect('roles/show/' . $id);
    }
}