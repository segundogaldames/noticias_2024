<?php
use models\Usuario;
use models\Role;

class usuariosController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Usuarios',
            'subject' => 'Lista de Usuarios',
            'usuarios' => Usuario::with('role')->get(), 
            #SElECT * FROM roles join usuarios on usuarios.role_id = role.id;
            'warning' => 'No hay usuarios registrados'
        ];

        $this->_view->load('usuarios/index', compact('options','msg_success','msg_error'));
    }

    public function create()
    {
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Usuarios',
            'subject' => 'Nuevo Usuario',
            'usuario' => Session::get('data'),
            'action' => 'create',
            'send' => $this->encrypt($this->getForm()),
            'process' => 'usuarios/store'
        ];

        $this->_view->load('usuarios/create', compact('options','msg_success','msg_error'));
    }

    public function store()
    {
        #print_r($_POST);exit;
        $this->validateForm('usuarios/create',[
            'run' => Filter::getText('run'),
            'nombre' => Filter::getText('nombre'),
            'email' => $this->validateEmail(Filter::getPostParam('email')),
            'password' => Filter::getText('password'),
            'role' => Filter::getText('role')
        ]);

        #verificar que el largo del password sea igual o mayor que 8
        if (strlen(Filter::getText('password')) < 8) {
            Session::set('msg_error','El password debe contener al menos 8 caracteres');
            $this->redirect('usuarios/create');
        }

        #verificar que el password y el confirmado sean iguales
        if (Filter::getText('password') != Filter::getText('password_confirm')) {
            Session::set('msg_error','Los passwords ingresados no coinciden');
            $this->redirect('usuarios/create');
        }

        #comprobar que no haya otro usuario con el mismo email
        $usuario = Usuario::select('id')->where('email', Filter::getPostParam('email'))->first();

        if($usuario){
            Session::set('msg_error','El usuario ingresado ya está registrado... intenta con otro');
            $this->redirect('usuarios/create');
        }

        $usuario = new Usuario;
        $usuario->run = Filter::getText('run');
        $usuario->nombre = Filter::getText('nombre');
        $usuario->email = Filter::getText('email');
        $usuario->password = Filter::getText('password');
        $usuario->activo = 2; #2 es igual a inactivo
        $usuario->role_id = Filter::getInt('role');
        $usuario->save();

        Session::destroy('data');
        Session::set('msg_success','El usuario se ha registrado correctamente');
        $this->redirect('usuarios');
    }

    public function show($id = null)
    {
        Validate::validateModel(Usuario::class, $id, 'error/error');
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Usuarios',
            'subject' => 'Detalle de Usuario',
            'usuario' => Usuario::with('role')->find(Filter::filterInt($id)), 
            #SElECT * FROM roles join usuarios on usuarios.role_id = role.id;
            'warning' => 'No hay un usuario asociado'
        ];

        $this->_view->load('usuarios/show', compact('options','msg_success','msg_error'));
    }

    public function edit($id = null)
    {
        Validate::validateModel(Usuario::class, $id, 'error/error');
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'Usuarios',
            'subject' => 'Editar Usuario',
            'usuario' => Usuario::with('role')->find(Filter::filterInt($id)),
            'action' => 'edit',
            'send' => $this->encrypt($this->getForm()),
            'process' => "usuarios/update/{$id}"
        ];

        $this->_view->load('usuarios/edit', compact('options','msg_success','msg_error'));
    }

    public function update($id = null)
    {
        Validate::validateModel(Usuario::class, $id, 'error/error');
        $this->validatePUT();

        $this->validateForm("usuarios/edit/{$id}",[
            'run' => Filter::getText('run'),
            'nombre' => Filter::getText('nombre'),
            'activo' => Filter::getText('activo'),
            'role' => Filter::getText('role')
        ]);

        $usuario = Usuario::find(Filter::filterInt($id));
        $usuario->run = Filter::getText('run');
        $usuario->nombre = Filter::getText('nombre');
        $usuario->activo = Filter::getInt('activo');
        $usuario->role_id = Filter::getInt('role');

        Session::destroy('data');
        Session::set('msg_success','El usuario se ha modificado correctamente');
        $this->redirect('usuarios/show/' . $id);
    }
}