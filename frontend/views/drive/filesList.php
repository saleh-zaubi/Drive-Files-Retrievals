<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Files List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (count($files->getFiles()) == 0) : ?>
        No files found!
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <td>Title</td>
                    <td>ThumbnailLink</td>
                    <td>EmbedLink</td>
                    <td>ModifiedDate</td>
                    <td>FileSize(MB)</td>
                    <td>OwnerNames</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files->getFiles() as $file) : ?>
                    <tr>
                        <td><?= $file->getName() ?></td>
                        <td><?= $file->getThumbnailLink() ?></td>
                        <td><a href="<?= $file->getWebContentLink() ?>" target="_blank">Download</a></td>
                        <td><?= Yii::$app->formatter->asDatetime($file->getModifiedTime()) ?></td>
                        <td><?= round(($file->getSize() / 1024) / 1024, 2) ?></td>
                        <td><?= implode(",", array_map(function ($owner) {
                                return is_object($owner) ? $owner->displayName : '';
                            }, $file->getOwners())) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>