<section class="media-accreditation" id="media-accreditation">
    <a name="media-accreditation"></a>
    <header>
        <h2>Media accreditation form</h2>
    </header>

    <div class="intro">
        To apply for a Media Pass, please fill in the media accreditation form below. We will get back to you as soon as possible with an answer to your request. Together with an email confirming your
        accreditation request you will additionally receive practical information for the media.
    </div>

    <div class="content">
        <div class="forms">
            <div class="form" id="form3">

                <?= Form::open(); ?>
                <div class="col-wrap">
                    <div class="col">
                        <?= Form::label( 'form3-name_surname', 'Name, surname *' ); ?>
                        <?= Form::text( 'name_surname', old( 'name_surname' ), [ 'data-validate' => "require", 'id' => 'form3-name_surname' ] ); ?>

                        <?= Form::label( 'form3-email', 'Email *' ); ?>
                        <?= Form::text( 'email', old( 'email' ), [ 'data-validate' => "require|email", 'id' => 'form3-email' ] ); ?>

                        <?= Form::label( 'form3-phone', 'Telephone number *' ); ?>
                        <?= Form::text( 'phone', old( 'phone' ), [ 'data-validate' => "require", 'id' => 'form3-phone' ] ); ?>

                        <div class="col">
                            <?= Form::checkbox( 'day_1', '1', ( old( 'day_1', 0 ) == 1 ), [ 'id' => 'form3-day_1' ] ); ?>
                            <label for="form3-day_1">
                                Register for Day 1
                            </label>
                        </div>
                        <div class="col">
                            <?= Form::checkbox( 'day_2', '1', ( old( 'day_2', 0 ) == 1 ), [ 'id' => 'form3-day_2' ] ); ?>
                            <label for="form3-day_2">
                                Register for Day 2
                            </label>
                        </div>
                        <div class="clearfix"></div>
                        <div class="help-block">
                            <label>&nbsp;</label>
                            Practical information about media possibilities during the events will be sent to registered media following confirmation</div>
                    </div>
                    <div class="col">
                        <?= Form::label( 'form3-position', 'Position *' ); ?>
                        <?= Form::text( 'position', old( 'position' ), [ 'data-validate' => "require", 'id' => 'form3-position' ] ); ?>

                        <?= Form::label( 'form3-name_of_media', 'Name of media *' ); ?>
                        <?= Form::text( 'name_of_media', old( 'name_of_media' ), [ 'data-validate' => "require", 'id' => 'form3-name_of_media' ] ); ?>

                        <?= Form::label( 'form3-website', 'Website *' ); ?>
                        <?= Form::text( 'website', old( 'website' ), [ 'data-validate' => "require", 'id' => 'form3-website' ] ); ?>

                        <div class="empty-space"></div>

                        <?= Form::button( 'register-form3', 'Send', [ 'class' => 'button register' ] ); ?>

                    </div>
                    <div class="clearfix"></div>
                </div><!-- .col-wrap -->
                <?= Form::close(); ?>

                <div class="thanks off">
                    <h2>Thank you, we have received your registration.</h2>
                    <p>Please wait for confirmation from the organizer. Your participation <strong>is approved only by receiving the confirmation e-mail!</strong> </p>
                </div>
            </div> <!-- form3 -->
        </div>
    </div>

</section>