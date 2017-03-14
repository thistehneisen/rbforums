<?php
Class ManageUsers {
    private $cli = null;

    public function __construct(Console $cliObject) {
        $this->cli = $cliObject;
        switch($this->cli->getCommand()) {
            case "create":
                $this->create();
                break;
            case "password":
                $this->changePassword();
                break;
            default:
                echo $this->cli->getCommand();
                break;
        }
    }

    public function create()
    {
        if(count($this->cli->getParameter()) < 4) {
            $this->cli->error('Please provide all arguments.'."\n".' Command usage: php do admin:create Name Surname email password');
        } else {
            $email = $this->cli->getParameter(2);
            if(!validEmail($email)) {
                $this->cli->error('Please provide valid e-mail!');
            } else {
                $exists = (new User())->select('id')->where('email', $email)->get()->scalar();
                if($exists) {
                    $this->cli->error('User with this e-mail already exists!');
                } else {
                    (new User())->create([
                        'name' => $this->cli->getParameter(0),
                        'surname' => $this->cli->getParameter(1),
                        'name_surname' => $this->cli->getParameter(0).' '.$this->cli->getParameter(1),
                        'email' => $email,
                        'status' => '1',
                        'is_admin' => '1',
                        'password' => Auth::salt($this->cli->getParameter(3)),
                    ]);
                    $this->cli->success('User created successfully');
                }
            }
        }
    }

    public function changePassword()
    {
        if(count($this->cli->getParameter()) < 2) {
            $this->cli->error('Please provide all arguments.'."\n".' Command usage: php do admin:password email newPassword');
        } else {
            $user = (new User())->where('email', $this->cli->getParameter(0))->get()->row();
            $password = Auth::salt($this->cli->getParameter(1));

            if(!$user->isEmpty()) {
                $user->password = $password;
                $user->save();
                $this->cli->success('Password updated!');
            } else {
                $this->cli->error('Did not find such user!');
            }

        }
    }
}