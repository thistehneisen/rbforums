<?php
/**
 * @var $items Form3
 * @var $pages integer
 * @var $curPage integer
 * @var $link string
 * @var $tab string
 */
?>
<section class="container px2 py3">
    <div class="right">
        <a href="<?= URL::to('admin/' . $link);?>" class="btn btn-primary mb1 <?= ($tab == 'new' ? 'bg-gray' : '');?>">Neapstrādātie</a>
        <a href="<?= URL::to('admin/' . $link . '/approved/');?>" class="btn btn-primary mb1 <?= ($tab == 'approved' ? 'bg-gray' : '');?>">Apstiprinātie</a>
        <a href="<?= URL::to('admin/' . $link. '/disapproved/');?>" class="btn btn-primary mb1 <?= ($tab == 'disapproved' ? 'bg-gray' : '');?>">Noraidītie</a>
    </div>
    <div class="clearfix"></div>
    <?php if ( ! $items->isEmpty() ) : ?>
        <div class="center">
            <?php for ( $i = 1; $i <= $pages; $i ++ ) : ?>
                <a href="<?= URL::to( 'admin/' . $link . '/' . $tab . '/' . $i ); ?>" class="btn btn-primary mb1 <?= ( $curPage == $i ? 'bg-gray' : '' ); ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
        <div class="overflow-auto">
            <table class="table-light overflow-hidden bg-white border rounded">
                <thead class="bg-darken-1">
                <tr>
                    <?php if($tab == 'new') :?>
                        <th> </th>
                        <th> </th>
                    <?php endif;?>
                    <th>Vārds, Uzvārds</th>
                    <th>E-pasts</th>
                    <th>Tel.</th>
                    <th>Uz 1. dienu</th>
                    <th>Uz 2. dienu</th>
                    <th>Amats</th>
                    <th>Medija nosaukums</th>
                    <th>Weblapa</th>
                </tr>
                </thead>
                <?php foreach ( $items as $item ) : ?>
                    <tr>
                        <?php if($tab == 'new') :?>
                            <td class="p0 py1 px1"><a href="#" data-id="<?=$item->id;?>" class="btn btn-primary bg-green btn-small white m0 ok-media-2">jā</a></td>
                            <td class="p0 py1 px1"><a href="#" data-id="<?=$item->id;?>" class="btn btn-primary bg-red btn-small white m0 ney-media-2">nē</a></td>
                        <?php endif; ?>
                        <td><?=$item->name_surname;?></td>
                        <td><?=$item->email;?></td>
                        <td><?=$item->phone;?></td>
                        <td><?=($item->day_1 == 1 ? 'Jā' : 'Nē');?></td>
                        <td><?=($item->day_2 == 1 ? 'Jā' : 'Nē');?></td>
                        <td><?=$item->position;?></td>
                        <td><?=$item->name_of_media;?></td>
                        <td><a href="<?=(!stristr($item->website, 'http') ? 'http://' : '') ;?><?=$item->website;?>" target="_blank"><?=$item->website;?></a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="center mt2">
            <?php for ( $i = 1; $i <= $pages; $i ++ ) : ?>
                <a href="<?= URL::to( 'admin/' . $link . '/' . $tab . '/' . $i ); ?>" class="btn btn-primary mb1 <?= ( $curPage == $i ? 'bg-gray' : '' ); ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>

    <?php else : ?>
        Saraksts ir tukšs
    <?php endif; ?>


</section>