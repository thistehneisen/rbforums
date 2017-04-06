<?php
/**
 * @var array $industries
 * @var array $letters
 * @var Suppliers $companies
 */
?><section class="suppliers" id="suppliers">
    <div class="container">
        <article>
            <header>
                <h1>SUPPLIERS' NETWORKING</h1>
                <h2>The Industry Suppliers' Day - a platform for building cooperation bridges</h2>
                <h3>LIST OF SUPPLIERS</h3>
            </header>
            <div class="entry">
                <?= Form::open() ;?>
                <div class="col c25">
                    <?= Form::select('suppliers_search_type', ['alphabet' => 'A-Z', 'industry' => 'Industry']);?>
                </div>
                <div class="col c75">
                    <div class="alphabet">
                        <?= implode(' ', $letters);?>
                    </div>
                    <div class="industry-selector" style="display: none;">
                        <?= Form::select('suppliers_search_industry', $industries);?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="loader" id="suppliers-loader" style="display: none;"></div>
                <div class="companies" id="suppliers-companies">
                    <?php if ( ! $companies->isEmpty() ) : ?>
                        <?php foreach ( $companies as $c ) : ?>
                            <p>
                                <strong><?=$c->company;?></strong><br>
                                <?=($c->industry == 'hide' ? '' : $c->industry);?>
                            </p>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?= Form::close();?>
            </div>
            <footer>
                <aside>
                    <h3>SUPPLIERS' MEETING POINT</h3>
                    <div class="entry">A special meeting point area will be provided in order to support suppliers' partnerships and contact building. In this area there is a possibility to apply for a limited number of spaces for your info stand. <span class="hl">(Registration closed. The limit has been reached.)</span></div>
                </aside>
                <aside>
                    <h4>Technical information about info stands (PDF file)</h4>
                    <a href="<?= URL::to( 'assets/media/PDF_info_suppliers_meeting_point_stands.pdf' ); ?>" target="_blank" class="button">Download pdf</a>
                </aside>
                <div class="clearfix"></div>
            </footer>
        </article>
    </div>
</section>