<?php

class AppController extends BaseController {
    public $user = null;

    private $letters = [
        'A',
        'Ā',
        'B',
        'C',
        'Č',
        'D',
        'E',
        'Ē',
        'F',
        'G',
        'Ģ',
        'H',
        'I',
        'Ī',
        'J',
        'K',
        'Ķ',
        'L',
        'Ļ',
        'M',
        'N',
        'Ņ',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'Š',
        'T',
        'U',
        'Ū',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'Ž',
    ];

    public function __construct() {
        parent::__construct();
        View::share( 'shareImg', URL::to( '/assets/img/rb-share2.png' ) );
        View::share( 'shareTitle', Config::get( 'app.title', '' ) );
        View::share( 'shareDesc', Config::get( 'app.description', '' ) );
    }

    public function getIndex() {

        $regData = [
            'salutation' => [ 'hide' => 'Choose', 'Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.' ],
            'title'      => [ 'hide' => 'Choose', 'Dr.' => 'Dr.', 'Prof.' => 'Prof.', 'Prof. Dr.' => 'Prof. Dr.', 'Other' => 'Other' ],
            'industry'   => array_merge( [ 'hide' => 'Choose' ], industryList() ),
            'country'    => array_merge( [ 'hide' => 'Choose' ], countryList() ),
        ];

        $data['page'] = View::make( 'app.home' );

        $data['page'] .= View::make( 'app.programme' );

        $data['page'] .= View::make( 'app.speakers', [ 'speakers' => Config::get( 'speakers', [] ) ] );

        $letters        = [];
        $letterCtObject = new Suppliers();
        foreach ( $this->letters as $k => $letter ) {
            $lc = $letterCtObject->where( 'company like', $letter . '% collate utf8_bin' )->count();
//            var_dump([$letter => $lc]);
            if ( $lc > 0 ) {
                $letters[] = '<a href="#" class="suppliers_search_letter ' . ( $k == 0 ? 'active' : '' ) . '" data-letter="' . $letter . '">' . $letter . '</a>';
            } else {
                $letters[] = $letter;
            }
        }

        $data['page'] .= View::make( 'app.suppliers', [
            'industries' => $regData['industry'],
            'letters'    => $letters,
            'companies'  => $letterCtObject->where('company like', 'A% collate utf8_bin')->groupBy('company')->orderBy('company')->get()->result(),
        ] );
        $data['page'] .= View::make( 'app.location' );

        $data['page'] .= View::make( 'app.header' );

        $data['page'] .= View::make( 'app.registration', $regData );
        $data['page'] .= View::make( 'app.media' );
        $data['page'] .= View::make( 'app.contacts' );
        $data['page'] .= View::make( 'app.organizer' );

        return $this->layout->add( 'content', View::make( 'app.index', $data ) );
    }

    public function getSuppliers() {
        $type = Input::get('type', 'alphabet');
        $key = Input::get('key', 'A');
        $suppliers = null;
        if($type == 'alphabet') {
            $suppliers = (new Suppliers())->where('company like', $key . '% collate utf8_bin')->groupBy('company')->orderBy('company')->get()->result();
        } else {
            $suppliers = (new Suppliers())->where('industry', $key)->groupBy('company')->orderBy('company')->get()->result();
        }
        $html = '';
        if(!$suppliers->isEmpty()) {
            foreach ( $suppliers as $supplier ) {
                $html .= '<p><strong>'.$supplier->company.'</strong><br>'.($supplier->industry == 'hide' ? '' : $supplier->industry). '</p>';
            }
        } else {

        }

        return Response::json(['success' => 'ok', 'html' => $html]);
    }

    public function getExit() {
        Session::destroy();
        redirect( '/' );
    }

    public function validate() {
        $code           = trim( strtoupper( Input::get( 'code' ) ) );
        $response['ok'] = 'ney';
        if ( $code ) {
            $response['ok'] = ( ( new Codes() )->where( 'content', $code )->where( 'used', 0 )->count() > 0 ? 'ok' : 'ney' );
        }

        return Response::json( $response );
    }

    public function registerOne() {
        $validatorConfig = [
            'salutation'        => 'required',
            'last_name'         => 'required',
            'first_name'        => 'required',
            'registration_code' => 'required|trim',
            'email'             => 'required|email',
            'phone'             => 'required',
            'company'           => 'required',
            'industry'          => 'required',
            'position'          => 'required',
            'country'           => 'required',
            'city'              => 'required',
        ];

        $validator           = new Validator( $validatorConfig );
        $response['success'] = 'fail';
        if ( $validator->execute( Input::all() ) ) {
            $code = ( new Codes() )->where( 'content', strtoupper( Input::get( 'registration_code' ) ) )->where( 'used', 0 )->get()->row();
            if ( ! $code->isEmpty() ) {
                $code->used     = 1;
                $code->pubstamp = time();
                $code->ip       = Request::ip();
                $code->save();

                ( new Form1() )->create( array_merge( Input::all(), [
                    'ip'       => Request::ip(),
                    'agent'    => Request::agent(),
                    'pubstamp' => time(),
                    'code_id'  => $code->id,
                ] ) );

                $response['success'] = 'ok';

            } else {
                $response['errors'] = [ 'registration_code' => 'Registration code used or not valid' ];
            }
        } else {
            $response['errors'] = $validator->getErrors();
        }

        return Response::json( $response );
    }

    public function registerTwo() {
        $validatorConfig = [
            'salutation' => 'required',
            'last_name'  => 'required',
            'first_name' => 'required',
            'email'      => 'required|email',
            'phone'      => 'required',
            'company'    => 'required',
            'industry'   => 'required',
            'position'   => 'required',
            'country'    => 'required',
            'city'       => 'required',
        ];

        $validator           = new Validator( $validatorConfig );
        $response['success'] = 'fail';
        if ( $validator->execute( Input::all() ) ) {
            ( new Form2() )->create( array_merge( Input::all(), [
                'ip'       => Request::ip(),
                'agent'    => Request::agent(),
                'pubstamp' => time(),
                'info_stand' => 0, // disabled info stand
                'cs_visit' => 0, // disabled excursion
            ] ) );

            $response['success'] = 'ok';
        } else {
            $response['errors'] = $validator->getErrors();
        }

        return Response::json( $response );
    }

    public function registerThree() {
        $validatorConfig = [
            'name_surname'  => 'required',
            'email'         => 'required|email',
            'phone'         => 'required',
            'position'      => 'required',
            'name_of_media' => 'required',
            'website'       => 'required',
        ];

        $validator           = new Validator( $validatorConfig );
        $response['success'] = 'fail';
        if ( $validator->execute( Input::all() ) ) {
            ( new Form3() )->create( array_merge( Input::all(), [
                'ip'       => Request::ip(),
                'agent'    => Request::agent(),
                'pubstamp' => time(),
            ] ) );

            $response['success'] = 'ok';

            $messageText = View::make( 'app.accreditation_email_form', Input::all() );

            $message = Swift_Message::newInstance();
            $message
                ->setSubject( "Media accreditation form" )
                ->setFrom( [ Input::get( 'email' ) => Input::get( 'name_surname' ) ] )
                ->setTo( [ Config::get( 'app.email' ) ] )
                ->setBody( $messageText, "text/html" );

            $transport = Swift_SendmailTransport::newInstance();

            $mailer = Swift_Mailer::newInstance( $transport );

            $response['mailer_result'] = $mailer->send( $message );

        } else {
            $response['errors'] = $validator->getErrors();
        }

        return Response::json( $response );
    }

    public function registerContacts() {
        $validatorConfig = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email',
            'message'    => 'required',
        ];

        $validator           = new Validator( $validatorConfig );
        $response['success'] = 'fail';
        if ( $validator->execute( Input::all() ) ) {
            ( new Form4() )->create( array_merge( Input::all(), [
                'ip'       => Request::ip(),
                'agent'    => Request::agent(),
                'pubstamp' => time(),
            ] ) );

            $response['success'] = 'ok';

            $messageText = View::make( 'app.contact_email_form', Input::all() );

            $message = Swift_Message::newInstance();
            $message
                ->setSubject( "Contact form from RailBaltica Forum landing page" )
                ->setFrom( [ Input::get( 'email' ) => Input::get( 'first_name' ) . ' ' . Input::get( 'last_name' ) ] )
                ->setTo( [ Config::get( 'app.email' ) ] )
                ->setBody( $messageText, "text/html" );

            $transport = Swift_SendmailTransport::newInstance();

            $mailer = Swift_Mailer::newInstance( $transport );

            $response['mailer_result'] = $mailer->send( $message );

        } else {
            $response['errors'] = $validator->getErrors();
        }

        return Response::json( $response );
    }
}