<?php
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphUser;

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
        $this->getVotes();
    }

	public function getVotes2()
	{
		$layout['totalI'] = (new Cards())->where('user !=', 'upload')->where('status >=', 0)->count();
		$layout['totalUpl'] = (new Cards())->where('user', 'upload')->where('status >=', 0)->count();
		$this->layout->add('content', View::make('admin.votes', $layout));
	}

	public function getVotes()
	{
		$layout['logged'] = (new User())->count();
		$layout['twt'] = (new User())->where('network_type', 'twitter')->count();
		$layout['fb'] = (new User())->where('network_type', 'facebook')->count();
		$layout['admins'] = (new User())->where('network_type', '')->count();

		$layout['user1'] = (new User())->where('status', 1)->count();
		$layout['user2'] = (new User())->where('status', 2)->count();
		$layout['user3'] = (new User())->where('status', 3)->count();

		$layout['cards1'] = (new Cards())->where('card_id', 1)->count();
		$layout['cards2'] = (new Cards())->where('card_id', 2)->count();
		$layout['cards3'] = (new Cards())->where('card_id', 3)->count();
		$layout['cards4'] = (new Cards())->where('card_id', 4)->count();

		$layout['indexes'] = (new Cards())->select('postal_code, COUNT(postal_code) as ct')->orderBy('ct', 'desc')->groupBy('postal_code')->get()->result();

		$this->layout->add('content', View::make('admin.votes1', $layout));
	}

	public function getBans($page = 1) {
		$page--;
		if($page < 0) $page = 0;
		$layout['cards'] = (new Cards())->orderBy('pubstamp', 'desc')->limit($this->perPage, ($page * $this->perPage))->get()->result();
		$layout['perPage'] = $this->perPage;
		$layout['pages'] = ceil((new Cards())->count() / $this->perPage);
		$layout['curpage'] = $page + 1;
        $layout['link'] = 'bans';
		$this->layout->add('content', View::make('admin.bans', $layout));
	}

	public function getBans2($page = 1) {
		$page--;
		if($page < 0) $page = 0;
		$this->perPage = 1000;
		$layout['cards'] = (new Cards())->limit($this->perPage, ($page * $this->perPage))->get()->result();
		$layout['perPage'] = $this->perPage;
		$layout['pages'] = ceil((new Cards())->count() / $this->perPage);
		$layout['curpage'] = $page + 1;
        $layout['link'] = 'bans2';
		$this->layout->add('content', View::make('admin.bans', $layout));
	}

	public function postBan() {
		$id = (int)Input::get('id');
		$status = (int)Input::get('status');
		if($id) {
			$c = (new Cards())->find($id);
			if(!$c->isEmpty()) {
				$c->status = $status;
				$c->save();
			}
		}
		exit;
	}

	public function postFailed() {
		$id = (int)Input::get('id');
		$status = (int)Input::get('status');
		if($id) {
			$c = (new Cards())->find($id);
			if(!$c->isEmpty()) {
				$c->failed = $status;
				$c->save();
			}
		}
		exit;
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