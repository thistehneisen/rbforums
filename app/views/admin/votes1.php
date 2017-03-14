<section class="container px2 py3">
	<table class="table-light">
		<tr>
			<td>Kopā unikāli lietotāji ielogojušies:</td>
			<td><?=$logged;?></td>
		</tr>
		<tr>
			<td>Kopā Twitter lietotāji:</td>
			<td><?=$twt;?></td>
		</tr>
		<tr>
			<td>Kopā Facebook lietotāji: </td>
			<td><?=$fb;?></td>
		</tr>
		<tr>
			<td>Kopā admini: </td>
			<td><?=$admins;?></td>
		</tr>
		<tr>
			<td>Cik lietotāji nosūtījuši vismaz 1 kartiņu: </td>
			<td><?=$user1;?></td>
		</tr>
		<tr>
			<td>Cik lietotāji nosūtījuši vismaz 2 kartiņas: </td>
			<td><?=$user2;?></td>
		</tr>
		<tr>
			<td>Cik lietotāji nosūtījuši vismaz 3 kartiņas:</td>
			<td><?=$user3;?></td>
		</tr>
		<tr>
			<td>Cik nosūtītas kartinas nr.1: </td>
			<td><?=$cards1;?></td>
		</tr>
		<tr>
			<td>Cik nosūtītas kartinas nr.2: </td>
			<td><?=$cards2;?></td>
		</tr>
		<tr>
			<td>Cik nosūtītas kartinas nr.3: </td>
			<td><?=$cards3;?></td>
		</tr>
		<tr>
			<td>Cik nosūtītas kartinas nr.4: </td>
			<td><?=$cards4;?></td>
		</tr>
		<tr>
			<td>Cik kopā nosūtītas kartiņas:</td>
			<td><?=($cards1 + $cards2 + $cards3 + $cards4);?></td>
		</tr>
	</table>

	<h3>Indeksu tops:</h3>
	<table class="table-light">
		<?php if($indexes) :?>
		<?php foreach($indexes as $i) :?>
				<tr>
					<td><?=$i->postal_code;?></td>
					<td><?=$i->ct;?></td>
				</tr>
			<?php endforeach;?>
		<?php endif;?>
	</table>
</section>