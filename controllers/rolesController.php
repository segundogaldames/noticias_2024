<?php

class rolesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $this->_view->load('roles/index');
    }

}