<?php

class AdminController extends BaseController {
    protected $layoutTemplate = 'layouts.admin';
	public $perPage = 25;

    protected static $scopes = ['manage_pages', 'publish_pages'];

    public function __construct()
    {
        parent::__construct();
        if(!Auth::check() and URI::segment(1) !== 'login') {
            $tail = URL::segments();
            array_shift($tail);
            URL::redirect('admin/login/'.implode('/', $tail));
        }
    }

    public function getLogin()
    {
        $this->layout->add('content', View::make('admin.login'));
    }

    public function postLogin() {
        $user = Auth::authenticate(Input::get('email'), Input::get('password'));
        if($user and objectGet($user, 'is_admin', 0) == 1) {
            $url = implode(',', func_get_args());
            redirect('admin/'.$url);
        } else {
            if($user) {
                Session::setFlash('error', trans('admin.authentication_failed_not_admin'));
            } else {
                Session::setFlash('error', trans('admin.authentication_failed'));
            }
            redirect(URL::current());
        }
    }

    public function getLogout()
    {
        Auth::destroy();
        redirect('admin');
    }

    public function getIndex()
    {
        $this->layout->add('content', View::make('admin.dashboard'));
    }

	public function getExport() {
		$this->layout->add('content', View::make('admin.export'));
	}

	public function postExport() {
		$cardId = Input::get('card');
		$cards = (new Cards())->where('card_id', $cardId)->get()->result();
		$toExcel = [];
		foreach($cards as $c) {
			$toExcel[] = [
				'Nr' => $c->id,
				'No' => $c->name,
				'Kam' => $c->name_to,
				'Adrese' => $c->street.', '.$c->city,
				'Indekss' => 'LV-'.$c->postal_code,
			];
		}

		sendDwonloadHeaders('card-'.$cardId.'-export-'.date('dmyGis').'.csv');
		echo array2csv($toExcel, Input::get('delimiter', ','));
		exit;
	}
}