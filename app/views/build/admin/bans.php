<section class="container px2 py3">
	<?php if ( ! $cards->isEmpty() ) : ?>
		<div class="center">
			<?php for ( $i = 1; $i <= $pages; $i ++ ) : ?>
				<a href="<?= URL::to('admin/'.$link.'/'.$i);?>" class="button button-primary mb1 <?=($curpage == $i ? '' : 'bg-gray');?>"><?=$i;?></a>
			<?php endfor; ?>
		</div>
		<table>
			<tr>
				<th>Bilde</th>
				<th>No kā</th>
				<th>Kam</th>
				<th>Nerādīt sākumā</th>
				<th>Nesūtīt</th>
				<th>Nesanāca izprintēt</th>
			</tr>
			<?php foreach ( $cards as $card ) : ?>
				<tr>
					<td>
						<img class="admin-img" src="<?= ( new Assets() )->find( $card->img_id )->getUrl( 280, 280 ) ?>" alt="">
					</td>
					<td><?= $card->name; ?></td>
					<td><?= $card->name_to; ?><br><span
							style="font-size: 12px;"><?= $card->street; ?> <?= $card->city; ?>
							LV-<?= $card->postal_code; ?></span></td>
					<td>
						<input type="checkbox" <?= ( $card->status < 1 ? ' checked="checked"' : '' ); ?> class="ban-fe"
						       id="ban-fe-<?= $card->id; ?>" data-id="<?= $card->id; ?>">
					</td>
					<td>
						<input type="checkbox" <?= ( $card->status == - 1 ? ' checked="checked"' : '' ); ?>
						       class="ban-card" id="ban-card-<?= $card->id; ?>" data-id="<?= $card->id; ?>">
					</td>
					<td>
						<input type="checkbox" <?= ( $card->failed == 1 ? ' checked="checked"' : '' ); ?>
						       class="failed-card" id="failed-card-<?= $card->id; ?>" data-id="<?= $card->id; ?>">
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<div class="center">
			<?php for ( $i = 1; $i <= $pages; $i ++ ) : ?>
				<a href="<?= URL::to('admin/'.$link.'/'.$i);?>" class="button button-primary mb1 <?=($curpage == $i ? '' : 'bg-gray');?>"><?=$i;?></a>
			<?php endfor; ?>
		</div>
	<?php endif; ?>


</section>