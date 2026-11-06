<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = 'Tulis Artikel Baru';
$this->params['breadcrumbs'][] = ['label' => 'Artikel', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-create" style="max-width: 980px; margin: 0 auto;">

    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0 0 6px 0; font-weight: 800; color: #0f172a; font-size: 24px; letter-spacing: -0.5px;">
            <i class="fa-solid fa-pen-to-square" style="color: #2563eb; margin-right: 8px;"></i>
            <?= Html::encode($this->title) ?>
        </h2>
        <p class="text-muted" style="margin: 0; font-size: 14px;">
            Tulis ide dan konten artikel Anda. Anda dapat menyimpannya sebagai draf atau langsung mengajukannya untuk ditinjau oleh Admin.
        </p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
