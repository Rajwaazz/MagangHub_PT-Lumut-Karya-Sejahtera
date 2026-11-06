<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$isRejected = $model->isRejected();
$this->title = ($isRejected ? 'Perbaiki Artikel: ' : 'Edit Artikel: ') . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Artikel', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->idpost]];
$this->params['breadcrumbs'][] = $isRejected ? 'Perbaiki' : 'Edit';
?>
<div class="post-update" style="max-width: 980px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0 0 6px 0; font-weight: 800; color: #0f172a; font-size: 24px; letter-spacing: -0.5px;">
            <i class="fa-solid <?= $isRejected ? 'fa-triangle-exclamation text-danger' : 'fa-pen-to-square text-primary' ?>" style="margin-right: 8px;"></i>
            <?= Html::encode($this->title) ?>
        </h2>
        <p class="text-muted" style="margin: 0; font-size: 14px;">
            <?= $isRejected 
                ? 'Perbaiki artikel sesuai dengan arahan dan catatan penolakan dari admin, kemudian ajukan ulang review.' 
                : 'Perbarui konten artikel Anda.' ?>
        </p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
