<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">

        <?php if (Yii::$app->user->getIsGuest()) : ?>
            <div class="text-left">
                <p>Register an account, it doesn't need verification!</p>
                <p>Log in</p>
                <p>Click on "Drive Files List" link in the menu to display your google drive files!</p>
            </div>
        <?php else : ?>
            <p>Click on "Drive Files List" link in the menu to display your google drive files!</p>
        <?php endif; ?>

    </div>

</div>