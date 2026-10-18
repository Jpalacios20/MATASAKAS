<?php
class C_Kardex_Accessory extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        $this->view->set_js('index');

        $this->view->set_menu(array(
            'modules' => $this->segment->get('modules'),
            'view' => 'Kardex_Accessory'
        ));

        $this->view->set_view('index');
    }
}