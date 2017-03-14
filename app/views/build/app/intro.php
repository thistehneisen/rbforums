<div class="intro">
    <h1>Noskaidro, kāds ēdiens<br>
        raksturo Tavu mīlestību!</h1>
    <a class="btn" href="<?= URL::to( 'start' );?>">Sākt testu</a>
</div>
<?php if(!isset($dismissI)) :?>
    <a href="<?= URL::to( 'rules' ); ?>" data-fancybox-type="iframe" class="info">i</a>
<?php endif;?>