Door <?php if($company['url']): ?><a target="_blank" href="<?php echo $company['url'] ?>"><?php endif; ?><?php echo $company['name']; ?><?php if($company['url']): ?></a><?php endif; ?>. Hulp nodig?
<?php if($company['phone']): ?>
    Bel naar <?php echo $company['phone']?>
<?php endif;?>
<?php if($company['phone'] && $company['email']): ?>
    of mail
<?php else:?>
    Mail
<?php endif;?>
<?php if($company['email']): ?>
    naar <a href="mailto:<?php echo $company['email']?>"><?php echo $company['email']?></a>.
<?php endif; ?>