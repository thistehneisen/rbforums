<?php

class AppController extends BaseController {
    public $user = null;

    public function __construct() {
        parent::__construct();
        View::share( 'shareImg', URL::to( '/assets/img/rb-share2.png' ) );
        View::share( 'shareTitle', Config::get( 'app.title', '' ) );
        View::share( 'shareDesc', Config::get( 'app.description', '' ) );
    }

    public function getIndex() {

        $data['page'] = View::make( 'app.home' );

        $data['page'] .= View::make( 'app.programme' );

        $data['page'] .= View::make( 'app.speakers', [ 'speakers' => Config::get( 'speakers', [] ) ] );
        $data['page'] .= View::make( 'app.suppliers' );
        $data['page'] .= View::make( 'app.location' );

        $data['page'] .= View::make( 'app.header' );

        $regData = [
            'salutation' => [ 'hide' => 'Choose', 'Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.' ],
            'title'      => [ 'hide' => 'Choose', 'Dr.' => 'Dr.', 'Prof.' => 'Prof.', 'Prof. Dr.' => 'Prof. Dr.', 'Other' => 'Other' ],
            'industry'   => array_merge( [ 'hide' => 'Choose' ], industryList() ),
            'country'    => array_merge( [ 'hide' => 'Choose' ], countryList() ),
        ];

        $data['page'] .= View::make( 'app.registration', $regData );
        $data['page'] .= View::make( 'app.media' );
        $data['page'] .= View::make( 'app.contacts' );
        $data['page'] .= View::make( 'app.organizer' );

        return $this->layout->add( 'content', View::make( 'app.index', $data ) );
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