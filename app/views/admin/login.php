<form action="" method="post" class="sm-col-6 login">
    <?php if($error = Session::flash('error')) :?>
        <div class="center h4">
            <span class="orange"><?=$error;?></span>
        </div>
    <?php endif;?>
    <h1><?=trans('admin.login');?></h1>
    <input type="text" name="email" placeholder="<?=trans('admin.email');?>" class="block field col-12 mb1" />
    <input type="password" name="password" placeholder="<?=trans('admin.password');?>" class="block field col-12 mb1" />
    <input type="submit" name="login" class="block btn btn-primary col-12" value="<?=trans('admin.login');?>" />
</form>