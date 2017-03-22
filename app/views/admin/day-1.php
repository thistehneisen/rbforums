<?php
/**
 * @var $items Form1
 * @var $pages integer
 * @var $curPage integer
 * @var $link string
 * @var $tab string
 * @var $codes array
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
                    <th>Kods</th>
                    <th>Vārds, Uzvārds</th>
                    <th>Uzruna</th>
                    <th>Ranks</th>
                    <th>E-pasts</th>
                    <th>Tel.</th>
                    <th>Vajag vīzu</th>
                    <th>Uzņēmums</th>
                    <th>Industrija</th>
                    <th>Amats</th>
                    <th>Valsts</th>
                    <th>Pilsēta</th>
                </tr>
                </thead>
                <?php foreach ( $items as $item ) : ?>
                    <tr>
                        <?php if($tab == 'new') :?>
                            <td class="p0 py1 px1"><a href="#" data-id="<?=$item->id;?>" class="btn btn-primary bg-green btn-small white m0 ok-day-1">jā</a></td>
                            <td class="p0 py1 px1"><a href="#" data-id="<?=$item->id;?>" class="btn btn-primary bg-red btn-small white m0 ney-day-1">nē</a></td>
                            <td class="p0 py1 px1"><a href="#" data-id="<?=$item->id;?>" class="btn btn-primary bg-fuchsia btn-small lime m0 del-day-1">dzēst</a></td>
                        <?php endif; ?>
                        <td><?=arrayGet($codes, $item->code_id, 'nezināms');?></td>
                        <td><?=$item->first_name;?> <?=$item->last_name;?></td>
                        <td><?=$item->salutation;?></td>
                        <td><?=$item->title;?></td>
                        <td><?=$item->email;?></td>
                        <td><?=$item->phone;?></td>
                        <td><?=($item->need_visa_invite == 1 ? 'Jā' : 'Nē');?></td>
                        <td><?=$item->company;?></td>
                        <td><?=$item->industry;?></td>
                        <td><?=$item->position;?></td>
                        <td><?=getCountry($item->country);?></td>
                        <td><?=$item->city;?></td>
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