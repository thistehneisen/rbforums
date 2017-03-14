<?php
/**
 * @var array $questions
 * @var array $results
 * @var object $test
 */
?>
<section class="page">
    <article>
        <header>
            <h1>Noskaidro, kāds ēdiens raksturo Tavu mīlestību!</h1>
        </header>
            <div class="q-ct">
                <?php if ( $test->type == 0 ) : ?>
                <?php foreach ( $questions as $k => $q ) : ?>
                    <div data-cur="<?= $k; ?>" class="q q<?= ( $k + 1 ); ?><?= ( $k == 0 ? ' current' : '' ); ?>">
                        <h2><?= $q['q']; ?></h2>
                        <div class="answers">
                            <?php for ( $i = 0; $i < 4; $i ++ ) : ?>
                                <a <?= ( $k > 0 ? 'class="img"' : '' ); ?> href="" data-id="<?= $i ?>">
                                    <?php if ( $k == 0 ) : ?>
                                        <?= $q['a'][ $i ] ?>
                                    <?php else : ?>
                                        <span class="id"><?= ( $i + 1 ); ?>.</span>
                                        <img src="/assets/img/<?= $q['a'][ $i ]; ?>" alt="">
                                    <?php endif; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <a href="#" class="answer">Atbildēt</a>
                <div class="bullets">
                    <div class="b b1 on"></div>
                    <div class="b b2"></div>
                    <div class="b b3"></div>
                    <div class="b b4"></div>
                    <div class="b b5"></div>
                    <div class="b b6"></div>
                </div>
                <?php endif; ?>
                <script type="text/javascript" src="//www.draugiem.lv/api/api.js"></script>
                <script>window.twttr = (function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0],
                            t = window.twttr || {};
                        if (d.getElementById(id)) return t;
                        js = d.createElement(s);
                        js.id = id;
                        js.src = "https://platform.twitter.com/widgets.js";
                        fjs.parentNode.insertBefore(js, fjs);

                        t._e = [];
                        t.ready = function(f) {
                            t._e.push(f);
                        };

                        return t;
                    }(document, "script", "twitter-wjs"));</script>
                <?php foreach ( $results as $rk => $res ) : ?>
                    <div class="res r<?=$rk;?> <?=($rk == $test->type ? 'current' : '');?>">
                        <div class="soc-icons">
                            <iframe src="https://www.facebook.com/plugins/share_button.php?href=<?=urlencode(URL::to(URL::current()));?>&layout=button&size=small&mobile_iframe=true&appId=252416751453129&width=59&height=20" width="59" height="20" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" class="fb"></iframe>
                            <div id="draugiemLike<?=$rk;?>"></div>
                            <script type="text/javascript">
                                var p = {
                                    mobile:true
                                };
                                new DApi.Like(p).append('draugiemLike<?=$rk;?>');
                            </script>

                            <a class="twitter-share-button"
                               href="<?=urlencode(URL::to(URL::current()));?>"
                               data-text="<?= $res['title']; ?>">Tweet</a>
                        </div>
                        <div class="img">
                            <img src="<?= URL::to( 'assets/img/' . $res['pic'] ); ?>" alt="">
                            <h2><?= $res['title']; ?></h2>
                        </div>
                        <div class="desc">
                            <?= $res['description']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
    </article>
    <?php if(!isset($dismissI)) :?>
        <a href="<?= URL::to( 'rules' ); ?>" data-fancybox-type="iframe" class="info">i</a>
    <?php endif;?>
</section>