<?php
/**
 * @var $items Form4
 * @var $pages integer
 * @var $curPage integer
 * @var $link string
 */
?>
<section class="container px2 py3">
    <div class="clearfix"></div>
    <?php if ( ! $items->isEmpty() ) : ?>
        <div class="center">
            <?php for ( $i = 1; $i <= $pages; $i ++ ) : ?>
                <a href="<?= URL::to( 'admin/' . $link . '/' . $i ); ?>" class="btn btn-primary mb1 <?= ( $curPage == $i ? 'bg-gray' : '' ); ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
        <div class="overflow-auto">
            <table class="table-light overflow-hidden bg-white border rounded">
                <thead class="bg-darken-1">
                <tr>
                    <th>Vārds, Uzvārds</th>
                    <th>E-pasts</th>
                    <th>Tel.</th>
                    <th>Ziņa</th>
                    <th>Laiks</th>
                </tr>
                </thead>
                <?php foreach ( $items as $item ) : ?>
                    <tr>
                        <td><?=$item->first_name;?> <?=$item->last_name?></td>
                        <td><?=$item->email;?></td>
                        <td><?=$item->phone;?></td>
                        <td><?=$item->message;?></td>
                        <td><?=date('d.m.Y H:i', $item->pubstamp);?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="center mt2">
            <?php for ( $i = 1; $i <= $pages; $i ++ ) : ?>
                <a href="<?= URL::to( 'admin/' . $link . '/' . $i ); ?>" class="btn btn-primary mb1 <?= ( $curPage == $i ? 'bg-gray' : '' ); ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>

    <?php else : ?>
        Saraksts ir tukšs
    <?php endif; ?>


</section>