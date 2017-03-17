<?php
/**
 * @var array $salutation
 * @var array $title
 * @var array $industry
 * @var array $country
 */
?>
<section class="registration" id="registration">
    <a name="registration"></a>
    <header>
        <h2>Registration form</h2>
    </header>

    <div class="content">
        <div class="buttons">
            <a href="#" class="active" data-form="day1">
                <span>Register for Day 1<br>
                <strong>Please, use your unique registration code</strong></span>
            </a>
            <a href="#" data-form="day2">
                <span>Register for Day 2</span>
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="forms">
            <div class="form" id="form1">
                <h2>Registration closing date April 9, 2017</h2>
                <div class="intro">This is a registration form for Rail Baltica Global Forum 2017. Registration is open till April 9, 2017.
                    To register for Day 1, please use the unique registration code you have received together with the invitation to the event.<br>
                    To register for Day 2, click to open and fill out the event registration form and wait for the confirmation e-mail. Your registration is approved only when you receive confirmation
                    e-mail from the organizer.
                </div>

                <?= Form::open(); ?>
                <div class="col-wrap">
                    <div class="col">
                        <div class="short">
                            <?= Form::label( 'registration_code', 'Registration code' ); ?>
                            <?= Form::text( 'registration_code', old( 'registration_code' ), ['maxlength' => '8']); ?>
                        </div>
                        <div class="shorter">
                            <label>&nbsp;</label>
                            <?= Form::button( 'validate', 'Validate', [ 'class' => 'validate-code' ] ); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col disabled">
                        <div class="short">
                            <?= Form::label( 'salutation', 'Salutation *' ); ?>
                            <?= Form::select( 'salutation', $salutation, old( 'salutation'), ['data-validate' => "require"]); ?>
                        </div>
                        <div class="shorter">
                            <?= Form::label( 'title', 'Title' ); ?>
                            <?= Form::select( 'title', $title, old( 'title' )); ?>
                        </div>
                        <div class="clearfix"></div>
                        <?= Form::label( 'first_name', 'First name *' ); ?>
                        <?= Form::text( 'first_name', old( 'first_name' ) , ['data-validate' => "require"]); ?>

                        <?= Form::label( 'last_name', 'Last name *' ); ?>
                        <?= Form::text( 'last_name', old( 'last_name' ) , ['data-validate' => "require"]); ?>

                        <div class="short">
                            <?= Form::label( 'email', 'Email *' ); ?>
                            <?= Form::text( 'email', old( 'email' ), ['data-validate' => "require|email"]); ?>
                        </div>
                        <div class="shorter">
                            <?= Form::label( 'phone', 'Phone nr. *' ); ?>
                            <?= Form::text( 'phone', old( 'phone' ) , ['data-validate' => "require"]); ?>
                        </div>
                        <div class="clearfix"></div>
                        <?= Form::checkbox( 'need_visa_invite', '1', ( old( 'need_visa_invite', 0 ) == 1 ) ); ?>
                        <label for="need_visa_invite">
                           I need invitation for Visa
                        </label>
<!--                        --><?//= Form::checkbox( 'potential_supplier', '1', ( old( 'potential_supplier', 0 ) == 1 ) ); ?>
<!--                        <label for="potential_supplier">-->
<!--                            I am interested in this event as potential supplier-->
<!--                        </label>-->
<!--                        --><?//= Form::checkbox( 'agree_to_supp_catalogue', '1', ( old( 'agree_to_supp_catalogue', 0 ) == 1 ) ); ?>
<!--                        <label for="agree_to_supp_catalogue">-->
<!--                            I agree, that my data will be used to make the Suppliers’ Catalog. (It would be available publicly.)-->
<!--                        </label>-->
                    </div>
                    <div class="col disabled">
                        <?= Form::label( 'company', 'Company / Organization *' ); ?>
                        <?= Form::text( 'company', old( 'company' ) , ['data-validate' => "require"]); ?>

                        <?= Form::label( 'industry', 'Industry *' ); ?>
                        <?= Form::select( 'industry', $industry, old( 'industry' ), ['data-validate' => "require"] ); ?>

                        <?= Form::label( 'position', 'Position *' ); ?>
                        <?= Form::text( 'position', old( 'position' ), ['data-validate' => "require"] ); ?>

                        <div class="short">
                            <?= Form::label( 'country', 'Country *' ); ?>
                            <?= Form::select( 'country', $country, old( 'country' ), ['data-validate' => "require"]); ?>
                        </div>
                        <div class="shorter">
                            <?= Form::label( 'city', 'City *' ); ?>
                            <?= Form::text( 'city', old( 'city' ), ['data-validate' => "require"]); ?>
                        </div>
                        <div class="clearfix"></div>

                    </div>
                    <div class="clearfix"></div>
                </div><!-- .col-wrap -->
                <?= Form::button('register-form1', 'Register', ['class' => 'button register', 'disabled' => 'disabled']);?>
                <?= Form::close(); ?>

                <div class="thanks off">
                    <h2>Thank you!</h2>
                    <p>If you are interested to participate in Day 2, please register for it in addition <a href="#" class="form-2-jump">here</a>.</p>
                </div>
            </div> <!-- form1 -->

            <div class="form off" id="form2">
                <h2>Registration closing date 9.04.2017</h2>
                <div class="intro">
                    <p>Please take into account that your registration is approved only when you receive a confirmation e-mail from the organizer.</p>
                    <p>There is a big interest of suppliers to participate in this day. Don't hesitate to register as soon as possible. In order to serve the interest of all, we will confirm registration for a maximum of three persons from one company. All other participation will be under the consideration depending on the available space.</p>
                    <p>A couple of days before the event, the potential suppliers registering for DAY 2 “Industry Supplier’s Day” have a possibility to receive the List of contacts of all suppliers attending the event for better networking possibilities. To receive the List you have to share your contacts as well.</p>

                    <p><strong>During DAY 2  supplier's are also offered to:</strong></p>
                    <ul>
                        <li>attend special Suppliers' Meeting point for future partnerships and contact building;</li>
                        <li>participate in the Suppliers' Meeting point with your info stand following the principle of first-come, first-served as there is a limited number of spaces available. If you are interested in placing your info stand, please indicate it in the registration form by first reading the instruction;</li>
                        <li>special on-the-spot visit to the site – Riga Central Railway Station. Detailed information about the technicalities of the visit will be sent to interested suppliers directly by e-mail.</li>
                    </ul>

                </div>

                <?= Form::open(); ?>
                <div class="col-wrap">
                    <div class="col">
                        <div class="short">
                            <?= Form::label( 'form2-salutation', 'Salutation *' ); ?>
                            <?= Form::select( 'salutation', $salutation, old( 'salutation'), ['data-validate' => "require", 'id'=> 'form2-salutation']); ?>
                        </div>
                        <div class="shorter">
                            <?= Form::label( 'form2-title', 'Title' ); ?>
                            <?= Form::select( 'title', $title, old( 'title' ), ['id'=> 'form2-title']); ?>
                        </div>
                        <div class="clearfix"></div>
                        <?= Form::label( 'form2-first_name', 'First name *' ); ?>
                        <?= Form::text( 'first_name', old( 'first_name' ) , ['data-validate' => "require", 'id'=> 'form2-first_name']); ?>

                        <?= Form::label( 'form2-last_name', 'Last name *' ); ?>
                        <?= Form::text( 'last_name', old( 'last_name' ) , ['data-validate' => "require", 'id'=> 'form2-last_name']); ?>

                        <div class="short">
                            <?= Form::label( 'form2-email', 'Email *' ); ?>
                            <?= Form::text( 'email', old( 'email' ), ['data-validate' => "require|email", 'id'=> 'form2-email']); ?>
                        </div>
                        <div class="shorter">
                            <?= Form::label( 'form2-phone', 'Phone nr. *' ); ?>
                            <?= Form::text( 'phone', old( 'phone' ) , ['data-validate' => "require", 'id'=> 'form2-phone']); ?>
                        </div>
                        <div class="clearfix"></div>
                        <?= Form::checkbox( 'need_visa_invite', '1', ( old( 'need_visa_invite', 0 ) == 1 ), ['id'=> 'form2-need_visa_invite'] ); ?>
                        <label for="form2-need_visa_invite">
                            I need invitation for Visa
                        </label>
                        <?= Form::checkbox( 'info_stand', '1', ( old( 'info_stand', 0 ) == 1 ), ['id'=> 'form2-info_stand'] ); ?>
                        <label for="form2-info_stand">
                            I need a place for info stand in the Suppliers' Meeting point
                        </label>
                        <?= Form::checkbox( 'full_suppliers_list', '1', ( old( 'full_suppliers_list', 0 ) == 1 ), ['id'=> 'form2-full_suppliers_list'] ); ?>
                        <label for="form2-full_suppliers_list">
                            I want to receive the full list of suppliers and their contacts participating in DAY 2 “Industry Supplier’s Day”, as well as to include my contact information in the List. (The List will be shared only with those who had given permission to include their contacts in it.)
                        </label>
                        <?= Form::checkbox( 'cs_visit', '1', ( old( 'cs_visit', 0 ) == 1 ) , ['id'=> 'form2-cs_visit']); ?>
                        <label for="form2-cs_visit">
                            I apply for the on-the-spot visit to the Riga Central Railway Station
                        </label>
                    </div>
                    <div class="col">
                        <?= Form::label( 'form2-company', 'Company / Organization *' ); ?>
                        <?= Form::text( 'company', old( 'company' ) , ['data-validate' => "require", 'id'=> 'form2-company']); ?>

                        <?= Form::label( 'form2-industry', 'Industry *' ); ?>
                        <?= Form::select( 'industry', $industry, old( 'industry' ), ['data-validate' => "require", 'id'=> 'form2-industry'] ); ?>

                        <?= Form::label( 'form2-position', 'Position *' ); ?>
                        <?= Form::text( 'position', old( 'position' ), ['data-validate' => "require", 'id'=> 'form2-position'] ); ?>

                        <div class="short">
                            <?= Form::label( 'form2-country', 'Country *' ); ?>
                            <?= Form::select( 'country', $country, old( 'country' ), ['data-validate' => "require", 'id'=> 'form2-country']); ?>
                        </div>
                        <div class="shorter">
                            <?= Form::label( 'form2-city', 'City *' ); ?>
                            <?= Form::text( 'city', old( 'city' ), ['data-validate' => "require", 'id'=> 'form2-city']); ?>
                        </div>
                        <div class="clearfix"></div>

                    </div>
                    <div class="clearfix"></div>
                </div><!-- .col-wrap -->
                <?= Form::button('register-form2', 'Register', ['class' => 'button register']);?>
                <?= Form::close(); ?>

                <div class="thanks off">
                    <h2>Thank you, we have received your registration.</h2>
                    <p><strong>NB: Your registration is approved only by receiving the confirmation e-mail!</strong><br>
                        We will get back to you as soon as possible!</p>
                </div>
            </div> <!-- form2 -->


        </div> <!-- forms -->

    </div> <!-- content -->

</section>