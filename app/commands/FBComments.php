<?php
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;

Class FBComments extends DB {
    private $cli = null;

    public function __construct(Console $cliObject) {
        parent::__construct();
        $this->cli = $cliObject;
        switch($this->cli->getCommand()) {
            case "check":
            default:
                $this->check();
                break;
        }
    }

    public function check()
    {
        $verbose = boolval($this->cli->getAttribute(['verbose', 'v']));
        if($verbose) {
            $this->cli->info('Gathering posts...');
        }

        $posts = (new Post())->where('status', 1)->get()->result();
        if($verbose) {
            $this->cli->info('Got '.iterator_count($posts) . ' posts');
            $this->cli->info('Checking comments...');
        }
        if(count($posts) > 0) {
            FacebookSession::setDefaultApplication(Config::get('social.fb_app_id'), Config::get('social.fb_app_secret'));
            foreach($posts as $p) {
                $session = new FacebookSession($p->question_token);

                try {
                    $comments = [];
                    $request = new FacebookRequest(
                        $session, 'GET', '/' . $p->post_id . '/comments'
                    );
                    do {
                        $response = $request->execute();
                        $graphObject = $response->getGraphObject();
                        $coms = $graphObject->getProperty('data');
                        if(!is_null($coms)) {
                            $coms = $coms->asArray();
                            $comments = array_merge($comments, $coms);
                        }
                    } while ($request = $response->getRequestForNextPage());

                    if(count($comments)) {
                        foreach($comments as $c) {
                            /**
                             * statusi
                             *  * 0 - need attention
                             *  * 1 - approved
                             *  * 2 - declined
                             */
                            $commentId = objectGet($c, 'id');
                            $message = objectGet($c, 'message');
                            $from = objectGet($c, 'from');
                            $userId = objectGet($from, 'id');
                            $existing = (new Comment())->where('comment_id', $commentId)->get()->first();
                            $hasComment = (new Comment())
                                ->where('user_id', $userId)
                                ->where('post_id', $p->post_id)
                                ->where('status', 1)
                                ->get()->first();
                            $newData = [
                                'comment_id' => $commentId,
                                'user_id' => $userId,
                                'user_name' => objectGet($from, 'name'),
                                'post_id' => $p->post_id,
                                'message' => $message,
                                'created_time' => date('Y-m-d G:i:s', strtotime(objectGet($c, 'created_time'))),
                                'like_count' => objectGet($c, 'like_count'),
                                'user_likes' => (int)objectGet($c, 'user_likes'),
                            ];

                            if($existing->isEmpty()) {
                                $comment = (new Comment());
                                if($hasComment->isEmpty()) {
                                    $answers = [];
                                    for($i = 1; $i <= 5; $i++) {
                                        $needles = explode('|', $p->{'a'.$i});
                                        foreach($needles as $n) {
                                            $n = trim($n);
                                            if(!empty($n) and stristr($message, $n)) {
                                                $answers[] = $i;
                                            }
                                        }
                                    }

                                    if(empty($answers)) {
                                        $newData['answer'] = 0;
                                        $newData['status'] = 0;
                                    } else {
                                        if(count($answers) == 1) {
                                            $newData['answer'] = $answers[0];
                                            $newData['status'] = 1;
                                        }
                                    }
                                } else {
                                    $newData['answer'] = 0;
                                    $newData['status'] = 2; //decline
                                }
                                $comment->create($newData);
                            }
                        }
                    }
                } catch (FacebookRequestException $e) {
                    $this->cli->info('Facebook error: '.$e->getMessage());
                } catch (\Exception $e) {
                    $this->cli->info('Error: '.$e->getMessage());
                }

                $a1 = (new Comment())->where('post_id', $p->post_id)->where('answer', 1)->count();
                $a2 = (new Comment())->where('post_id', $p->post_id)->where('answer', 2)->count();
                $a3 = (new Comment())->where('post_id', $p->post_id)->where('answer', 3)->count();
                $a4 = (new Comment())->where('post_id', $p->post_id)->where('answer', 4)->count();
                $a5 = (new Comment())->where('post_id', $p->post_id)->where('answer', 5)->count();

                if(!$p->isEmpty()) {
                    $p->ac1 = $a1;
                    $p->ac2 = $a2;
                    $p->ac3 = $a3;
                    $p->ac4 = $a4;
                    $p->ac5 = $a5;
                    $p->save();
                }
            }
        } else {
            if($verbose) {
                $this->cli->info('Nothing to do!');
            }
        }
    }
}