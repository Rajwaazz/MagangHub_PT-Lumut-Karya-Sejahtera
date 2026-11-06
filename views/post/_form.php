<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form yii\widgets\ActiveForm */

$user = Yii::$app->user->identity;
$isAdmin = $user && $user->isAdmin();
?>

<div class="post-form" style="max-width: 980px; margin: 0 auto;">

    <?php if ($model->isRejected() && !empty($model->reject_reason)): ?>
        <div class="alert alert-danger" style="margin-bottom: 25px; padding: 20px; border-radius: 12px; border-left: 5px solid #ef4444; background-color: #fef2f2;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="font-size: 22px; color: #dc2626; margin-top: 2px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div style="flex-grow: 1;">
                    <h4 style="margin: 0 0 6px 0; font-weight: 700; color: #991b1b; font-size: 15px;">
                        Catatan Revisi dari Admin:
                    </h4>
                    <div style="background-color: #ffffff; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; color: #991b1b; font-size: 13.5px; line-height: 1.6; margin: 8px 0 10px 0;">
                        <?= nl2br(Html::encode($model->reject_reason)) ?>
                    </div>
                    <p style="margin: 0; font-size: 13px; color: #7f1d1d;">
                        <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i> Silakan lakukan perbaikan pada teks artikel di bawah, simpan perubahan draft, lalu tekan tombol <strong>"Ajukan Review"</strong> di Dashboard untuk mengirimkannya kembali ke Admin.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="panel card-modern">
        <div class="panel-body" style="padding: 35px 40px;">
            <?php $form = ActiveForm::begin([
                'id' => 'post-form',
                'enableClientValidation' => true,
            ]); ?>

            <div style="margin-bottom: 25px;">
                <label style="font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 8px;">
                    Judul Artikel <span class="text-danger">*</span>
                </label>
                <?= $form->field($model, 'title')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Masukkan judul artikel yang menarik dan deskriptif...',
                    'style' => 'font-size: 16px; font-weight: 600; padding: 12px 16px; border-radius: 8px;'
                ])->label(false) ?>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 8px;">
                    Isi Konten Artikel <span class="text-danger">*</span>
                </label>
                <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                    'options' => ['rows' => 12],
                    'preset' => 'standard',
                ])->label(false) ?>
            </div>

            <!-- Petunjuk Alur Publikasi Sesuai Role -->
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-lightbulb" style="color: #f59e0b; font-size: 18px;"></i>
                <div style="font-size: 13px; color: #475569;">
                    <strong>Alur Publikasi Author:</strong> Artikel baru akan disimpan terlebih dahulu dengan status <strong>Draft</strong>. Setelah disimpan, Anda dapat memeriksa kembali dan menekan tombol <strong>"Ajukan Review"</strong> di Dashboard saat artikel siap ditinjau oleh Admin.
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0 20px 0;">

            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <!-- Tombol Tunggal Simpan sebagai Draft -->
                    <button type="submit" class="btn btn-primary shadow-sm" style="font-weight: 600; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> 
                        <?= $model->isRejected() ? 'Simpan Perubahan Draft' : 'Simpan sebagai Draft' ?>
                    </button>
                </div>

                <!-- Tombol Batal -->
                <?= Html::a('<i class="fa-solid fa-arrow-left" style="margin-right: 4px;"></i> Batal & Kembali', ['index'], [
                    'class' => 'btn btn-default',
                    'style' => 'font-size: 13.5px; padding: 9px 18px;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
