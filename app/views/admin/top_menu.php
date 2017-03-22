<?php
/**
 * @var $menu array
 */
$segments = URI::segments();
array_shift($segments);
$current = implode('/', $segments);


?><nav class="clearfix white bg-black">
    <div class="sm-col">
        <a href="<?= URL::to('admin'); ?>" class="inline-block white button p2 btn-transparent <?=("" == $current ? 'active' : '');?>"><?= trans('admin.home'); ?></a>
        <?php foreach($menu as $perm => $name) :?>
            <a href="<?= URL::to('admin/'.$perm) ;?>" class="inline-block white button p2 btn-transparent <?=($perm == $current ? 'active' : '');?>"><?= trans($name); ?></a>
        <?php endforeach;?>
    </div>
    <div class="sm-col-right">
        <?php if(Auth::check()) : ?>
            <a class="inline-block white button p2 btn-transparent" href="<?= URL::to('admin/logout') ;?>"><?= trans('admin.logout') ;?> <?= Auth::user()->item('name') ;?></a>
        <?php endif;?>
    </div>
</nav>