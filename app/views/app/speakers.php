<?php
/**
 * @var $speakers array
 */
?>
<section class="speakers" id="speakers">
    <header>
        <h2>Speakers</h2>
    </header>
    <div class="content">
        <style>
            <?php foreach ($speakers as $k => $s) :?>
            section.speakers .content .speaker.s<?=$k;?> {
                background-image: url('<?= URL::to( 'assets/img/speakers/bw/' . $s['picture'] ); ?>');
            }
            section.speakers .content .speaker.s<?=$k;?>:hover, section.speakers .content .speaker.s<?=$k;?>.active {
                background-image: url('<?= URL::to( 'assets/img/speakers/color/' . $s['picture'] ); ?>');

            }
            <?php endforeach;?>
        </style>
        <?php foreach ( $speakers as $k => $speaker ) : ?>
            <?php if ( $k == 0 ) : ?>
                <div class="col1">
            <?php endif; ?>
            <a href="#" data-id="<?=$k;?>" data-img="<?= URL::to( 'assets/img/speakers/color/' . $speaker['picture'] ); ?>" class="speaker s<?=$k;?> <?=($k==0 ? 'active' : '');?>">
                <span class="name"><?=$speaker['name'];?></span>
                <span class="position"><?=$speaker['position'];?></span>
            </a>
            <div class="a-description a<?=$k;?>">
                <?=nl2p($speaker['about']);?>
            </div>
            <?php if ( $k == 1 ) : ?>
                </div>
                <div class="main-description">
                    <div class="image">
                        <img src="<?= URL::to( 'assets/img/speakers/color/' . $speakers[0]['picture'] ); ?>" alt="">
                    </div>
                    <h2><?= $speakers[0]['name']; ?></h2>
                    <h3><?= $speakers[0]['position']; ?></h3>
                    <div class="about">
                        <?= nl2p( $speakers[0]['about'] ); ?>
                    </div>
                </div>
                <div class="col2">
            <?php endif; ?>

            <?php if ( $k == 3 ) : ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="clearfix"></div>
    </div>
</section>