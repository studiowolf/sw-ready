
<h2>Wordpress van <?php if($company['url']): ?><a target="_blank" href="<?php echo $company['url'] ?>"><?php endif; ?><?php echo $company['name']; ?><?php if($company['url']): ?></a><?php endif; ?></h2>
<p>Heb je hulp nodig? Of wil je een probleem melden? Neem direct contact op met de juiste persoon via de onderstaande gegevens:</p>
<ul class="list">
    <?php if($contact['name']): ?>
        <li>Wie: <strong><?php echo $contact['name'] ?></strong></li>
        <li>
            <?php if($contact['phone']): ?>
                Telefoon: <strong><?php echo $contact['phone'] ?></strong>
            <?php elseif($company['phone']): ?>
                Telefoon: <strong><?php echo $company['phone'] ?></strong>
            <?php endif;?>
        </li>
        <li>
            <?php if($contact['email']): ?>
                E-mail: <strong><?php echo $contact['email'] ?></strong>
            <?php elseif($company['phone']): ?>
                E-mail: <strong><?php echo $company['email'] ?></strong>
            <?php endif;?>
        </li>
    <?php endif;?>
</ul>
<!--<p>Dit systeem is voor het laatst ge&uuml;pdate op <strong>11 oktober 2012</strong>.</p>-->
<br/>