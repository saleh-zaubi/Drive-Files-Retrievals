<?php

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-info">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-info">
        <?= nl2br(Html::encode($message)) ?>
        <a href="<?= $authUrl ?>" class="alert-link">Authorize</a>
    </div>

</div>
