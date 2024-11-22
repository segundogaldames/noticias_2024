<?php

use models\Usuario;

class loginController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        list($msg_success, $msg_error) = $this->getMessages();

        $options = [
            'title' => 'LogIn',
            'subject' => 'Iniciar Sesión',
            'process' => 'login/store',
            'send' => $this->encrypt($this->getForm())
        ];

        $this->_view->load('login/login', compact('options','msg_success','msg_error'));
    }

    public function store()
    {
        $this->validateForm('login/login',[
            'email' => $this->validateEmail(Filter::getPostParam('email')),
            'password' => Filter::getText('password')
        ]);

        $usuario = Usuario::with('role')
            ->where('email', Filter::getPostParam('email'))
            ->where('password',Filter::getText('password'))
            ->where('activo', 1)
            ->first();

        if(!$usuario){
            Session::set('msg_error','El email o el password no están registrados');
            $this->redirect('login/login');
        }

        Session::set('authenticate', true);
        Session::set('user_id', $usuario->id);
        Session::set('user_name', $usuario->nombre);
        Session::set('user_rol', $usuario->role->nombre);
        Session::set('time', time());

        $this->redirect();
    }

    public function logout()
    {
        Session::destroy();

        $this->redirect();
    }
}