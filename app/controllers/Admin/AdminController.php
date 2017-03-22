<?php

class AdminController extends BaseController {
    protected $layoutTemplate = 'layouts.admin';
    public $perPage = 100;

    protected static $scopes = [ 'manage_pages', 'publish_pages' ];

    public function __construct() {
        parent::__construct();
        if ( ! Auth::check() and URI::segment( 1 ) !== 'login' ) {
            $tail = URL::segments();
            array_shift( $tail );
            URL::redirect( 'admin/login/' . implode( '/', $tail ) );
        }
    }

    public function getLogin() {
        $this->layout->add( 'content', View::make( 'admin.login' ) );
    }

    public function postLogin() {
        $user = Auth::authenticate( Input::get( 'email' ), Input::get( 'password' ) );
        if ( $user and objectGet( $user, 'is_admin', 0 ) == 1 ) {
            $url = implode( ',', func_get_args() );
            redirect( 'admin/' . $url );
        } else {
            if ( $user ) {
                Session::setFlash( 'error', trans( 'admin.authentication_failed_not_admin' ) );
            } else {
                Session::setFlash( 'error', trans( 'admin.authentication_failed' ) );
            }
            redirect( URL::current() );
        }
    }

    public function getLogout() {
        Auth::destroy();
        redirect( 'admin' );
    }

    public function getIndex() {
        $this->layout->add( 'content', View::make( 'admin.dashboard' ) );
    }

    public function getDay1( $tab = null, $page = 1 ) {
        $approved = 0;
        switch ( $tab ) {
            case 'approved':
                $approved = 1;
                break;
            case 'disapproved':
                $approved = - 1;
                break;
            default:
                $tab = 'new';
        }

        $data['curPage'] = $page;
        $data['link']    = 'day1';
        $data['items']   = ( new Form1() )->where( 'approved', $approved )->orderBy( 'pubstamp', 'desc' )->get()->result();

        $codes = [0 => 'nenoteikts'];
        if(!$data['items']->isEmpty()) {
            $cids = [];
            foreach($data['items'] as $i) {
                $cids[] = $i->code_id;
            }

            if(!empty($cids)) {
                $codesArray = (new Codes())->select('id, content')->whereIn('id', $cids)->get()->result();
                if(!$codesArray->isEmpty()) {
                    foreach ($codesArray as $c) {
                        $codes[$c->id] = $c->content;
                    }
                }
            }
        }

        $data['codes'] = $codes;

        $itemCount       = ( new Form1() )->where( 'approved', $approved )->count();
        $data['pages']   = ceil( $itemCount / $this->perPage );
        $data['tab']     = $tab;
        $this->layout->add( 'content', View::make( 'admin.day-1', $data ) );
    }

    public function getDay2( $tab = null, $page = 1 ) {
        $approved = 0;
        switch ( $tab ) {
            case 'approved':
                $approved = 1;
                break;
            case 'disapproved':
                $approved = - 1;
                break;
            default:
                $tab = 'new';
        }

        $data['curPage'] = $page;
        $data['link']    = 'day2';
        $data['items']   = ( new Form2() )->where( 'approved', $approved )->orderBy( 'pubstamp', 'desc' )->get()->result();
        $itemCount       = ( new Form2() )->where( 'approved', $approved )->count();
        $data['pages']   = ceil( $itemCount / $this->perPage );
        $data['tab']     = $tab;
        $this->layout->add( 'content', View::make( 'admin.day-2', $data ) );
    }

    public function getMedia( $tab = null, $page = 1 ) {
        $approved = 0;
        switch ( $tab ) {
            case 'approved':
                $approved = 1;
                break;
            case 'disapproved':
                $approved = - 1;
                break;
            default:
                $tab = 'new';
        }

        $data['curPage'] = $page;
        $data['link']    = 'media';
        $data['items']   = ( new Form3() )->where( 'approved', $approved )->orderBy( 'pubstamp', 'desc' )->get()->result();
        $itemCount       = ( new Form3() )->where( 'approved', $approved )->count();
        $data['pages']   = ceil( $itemCount / $this->perPage );
        $data['tab']     = $tab;
        $this->layout->add( 'content', View::make( 'admin.media', $data ) );
    }

    public function getContacts( $page = 1 ) {
        if ( ! $page ) {
            $page = 1;
        }
        $data['curPage'] = $page;
        $data['link']    = 'contacts';
        $data['items']   = ( new Form4() )->orderBy( 'pubstamp', 'desc' )->get()->result();
        $itemCount       = ( new Form4() )->count();
        $data['pages']   = ceil( $itemCount / $this->perPage );
        $this->layout->add( 'content', View::make( 'admin.contacts', $data ) );
    }

    public function getExport() {
        $this->layout->add( 'content', View::make( 'admin.export' ) );
    }

    public function postExport() {
        $type    = Input::get( 'type', 'day1' );
        $toExcel = [];

        switch ( $type ) {
            case 'day2':
                $items = new Form2();
                $items->where( 'approved', 1 )->orderBy( 'pubstamp' )->get()->result();
                if ( ! $items->isEmpty() ) {
                    foreach ( $items as $i ) {
                        $toExcel[] = [
                            'Uzruna'   => $i->salutation,
                            'Vārds'   => $i->first_name,
                            'Uzvārds'   => $i->last_name,
                            'Uzņēmums'   => $i->company,
                            'Amats'   => $i->position,
                            'Industrija'   => $i->industry,
                            'Valsts'   => getCountry($i->country),
                            'E-pasts'   => $i->email,
                            'Telefons'   => $i->phone,
                            'Vīza' => ($i->need_visa_invite == 1 ? 'Jā' : 'Nē'),
                            'Stends' => ($i->info_stand == 1 ? 'Jā' : 'Nē'),
                            'Dalās ar kontaktiem' => ($i->full_suppliers_list == 1 ? 'Jā' : 'Nē'),
                            'Dosies ekskursijā' => ($i->cs_visit == 1 ? 'Jā' : 'Nē'),
                        ];
                    }
                }
                break;
            case 'media':
                $items = new Form3();
                $items->where( 'approved', 1 )->orderBy( 'pubstamp' )->get()->result();
                if ( ! $items->isEmpty() ) {
                    foreach ( $items as $i ) {
                        $toExcel[] = [
                            'Vārds, uzvārds'   => $i->name_surname,
                            'Medijs'   => $i->name_of_media,
                            'Amats'   => $i->position,
                            'E-pasts'   => $i->email,
                            'Telefons'   => $i->phone,
                            'Pirmā diena' => ($i->day_1 == 1 ? 'Jā' : 'Nē'),
                            'Otrā diena diena' => ($i->day_2 == 1 ? 'Jā' : 'Nē'),
                        ];
                    }
                }
                break;
            default:
                $items = new Form1();
                $items->where( 'approved', 1 )->orderBy( 'pubstamp' )->get()->result();
                if ( ! $items->isEmpty() ) {
                    foreach ( $items as $i ) {
                        $toExcel[] = [
                            'Vārds'   => $i->first_name,
                            'Uzvārds'   => $i->last_name,
                            'Uzņēmums'   => $i->company,
                            'Amats'   => $i->position,
                            'Industrija'   => $i->industry,
                            'Valsts'   => getCountry($i->country),
                            'E-pasts'   => $i->email,
                            'Telefons'   => $i->phone,
                            'Vīza' => ($i->need_visa_invite == 1 ? 'Jā' : 'Nē')
                        ];
                    }
                }
                break;
        }

        sendDwonloadHeaders( 'RB-forum-' . $type . '-' . date( 'd-m-Y-G-i' ) . '.csv' );
        echo array2csv( $toExcel, Input::get( 'delimiter', ',' ) );
        exit;
    }

    public function postSetStatus() {
        $response['success'] = 'fail';
        $type                = Input::get( 'type' );
        switch ( $type ) {
            case 'day1' :
                $item = ( new Form1() )->find( (int) Input::get( 'id', 0 ) );
                if ( ! $item->isEmpty() ) {
                    $status         = (int) Input::get( 'status', 0 );
                    $item->approved = $status;
                    $item->save();
                    $response['success'] = 'ok';
                    $response['mail']    = 'ok';

                    if($status > -2) {
                        $mail    = new Mail();
                        $subject = ( $status == 1 ? "Your Registration Confirmed | Rail Baltica Global Forum | Day 1" : "Registration refusal for Rail Baltica Global Forum I Watch live | Day 1" );
                        $mail->setSubject( $subject );
                        $mail->setFrom( "railbaltica@vilands.lv" );
                        $mail->setTo( [ $item->email ] );
                        $body = ( $status == 1 ? View::make( 'admin.email_approve_day1' ) : View::make( 'admin.email_disapprove_day1' ) );
                        $mail->setBody( $body, 'text/html' );
                        if ( ! $mail->send() ) {
                            $response['mail'] = 'fail';
                        }
                    }

                }
                break;
            case 'day2' :
                $item = ( new Form2() )->find( (int) Input::get( 'id', 0 ) );
                if ( ! $item->isEmpty() ) {
                    $status         = (int) Input::get( 'status', 0 );
                    $item->approved = $status;
                    $item->save();
                    $response['success'] = 'ok';
                    $response['mail']    = 'ok';

                    if($status > -2) {
                        $mail    = new Mail();
                        $subject = ( $status == 1 ? "Your Registration Confirmed | Rail Baltica Global Forum | Day 2" : "Registration refusal for Rail Baltica Global Forum I Watch live | Day 2" );
                        $mail->setSubject( $subject );
                        $mail->setFrom( "railbaltica@vilands.lv" );
                        $mail->setTo( [ $item->email ] );
                        $body = ( $status == 1 ? View::make( 'admin.email_approve_day1' ) : View::make( 'admin.email_disapprove_day1' ) );
                        $mail->setBody( $body, 'text/html' );
                        if ( ! $mail->send() ) {
                            $response['mail'] = 'fail';
                        }
                    }
                }
                break;

            case 'media' :
                $item = ( new Form3() )->find( (int) Input::get( 'id', 0 ) );
                if ( ! $item->isEmpty() ) {
                    $status         = (int) Input::get( 'status', 0 );
                    $item->approved = $status;
                    $item->save();
                    $response['success'] = 'ok';
                    $response['mail']    = 'ok';

                    if ( $status == 1 ) {
                        $mail    = new Mail();
                        $subject = "Your Registration Confirmed | Rail Baltica Global Forum";
                        $mail->setSubject( $subject );
                        $mail->setFrom( "railbaltica@vilands.lv" );
                        $mail->setTo( [ $item->email ] );
                        $body = View::make( 'admin.email_approve_media' );
                        $mail->setBody( $body, 'text/html' );
                        if ( ! $mail->send() ) {
                            $response['mail'] = 'fail';
                        }
                    }
                }
                break;
        }

        return Response::json( $response );
    }
}