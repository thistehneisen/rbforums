<nav class="clearfix white bg-black">
    <div class="sm-col">
        <a href="<?= URL::to('admin'); ?>" class="button py2 button-transparent"><?= trans('admin.home'); ?></a>
        <?php foreach($menu as $perm => $name) :?>
            <a href="<?= URL::to('admin/'.$perm) ;?>" class="button py2 button-transparent"><?= trans($name); ?></a>
        <?php endforeach;?>
    </div>
    <div class="sm-col-right">
        <?php if(Auth::check()) : ?>
            <a class="button py2 button-transparent" href="<?= URL::to('admin/logout') ;?>"><?= trans('admin.logout') ;?> <?= Auth::user()->item('name') ;?></a>
        <?php endif;?>
    </div>
</nav>