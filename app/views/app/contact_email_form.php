<?php
/**
 * @var string $first_name
 * @var string $last_name
 * @var string $email
 * @var string $phone
 * @var string $message
 */
?><p><strong>Filled contact form by <?= $first_name; ?> <?= $last_name; ?></strong></p>
    <p>
        Name, Surname: <?= $first_name; ?> <?= $last_name; ?><br>
        Email: <?= $email; ?><br>
        Telephone nr.: <?= $phone; ?><br>
        Message:
    </p>
<?= nl2p( $message ); ?>