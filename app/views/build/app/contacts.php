<section class="contacts" id="contacts">
    <a name="contacts"></a>
    <header>
        <h2>Send us a message</h2>
    </header>

    <div class="content">
        <div class="forms">
            <div class="form" id="form4">

                <?= Form::open(); ?>
                <div class="col-wrap">
                    <div class="col col3">
                        <?= Form::label( 'form4-first_name', 'First name *' ); ?>
                        <?= Form::text( 'first_name', old( 'first_name' ), [ 'data-validate' => "require", 'id' => 'form4-first_name' ] ); ?>

                        <?= Form::label( 'form4-last_name', 'Last name *' ); ?>
                        <?= Form::text( 'last_name', old( 'last_name' ), [ 'data-validate' => "require", 'id' => 'form4-last_name' ] ); ?>

                        <?= Form::label( 'form4-email', 'Email *' ); ?>
                        <?= Form::text( 'email', old( 'email' ), [ 'data-validate' => "require|email", 'id' => 'form4-email' ] ); ?>

                        <?= Form::label( 'form4-phone', 'Telephone number' ); ?>
                        <?= Form::text( 'phone', old( 'phone' ), [ 'id' => 'form4-phone' ] ); ?>

                    </div>
                    <div class="col col6">
                        <?= Form::label( 'form4-message', 'Message *' ); ?>
                        <?= Form::textarea( 'message', old( 'message' ), [ 'data-validate' => "require", 'id' => 'form4-message' ] ); ?>
                        <div class="empty-space"></div>
                        <?= Form::button( 'register-form4', 'Send', [ 'class' => 'button half' ] ); ?>
                        <div class="clearfix"></div>

                    </div>
                    <div class="clearfix"></div>
                </div><!-- .col-wrap -->
                <?= Form::close(); ?>
                <div class="thanks off">
                    <div><span>Thank You!</span></div>
                </div>
            </div> <!-- form4 -->
        </div>
    </div>

</section>