<?php
/**
 * @var string $name_surname
 * @var string $email
 * @var string $phone
 * @var string $position
 * @var string $name_of_media
 * @var string $website
 */
?><p><strong>Email from RailBaltica forum landing page.</strong></p>
<p><em>Media accreditation form</em></p>
<p>
    Name, Surname: <?=$name_surname;?><br>
    Email: <?=$email;?><br>
    Telephone nr.: <?=$phone;?><br>
    Position: <?=$position;?><br>
    Name of media: <?=$name_of_media;?><br>
    Website: <?=$website;?><br>
    Register for Day 1: <?=(isset($day_1) ? 'YES' : 'NO');?><br>
    Register for Day 2: <?=(isset($day_2) ? 'YES' : 'NO');?><br>
</p>