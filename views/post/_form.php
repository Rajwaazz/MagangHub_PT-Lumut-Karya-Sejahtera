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
                        <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i> Silakan lakukan perbaikan pada teks artikel di bawah, kemudian pilih tombol <strong>"Simpan & Ajukan Ulang Review"</strong> untuk mengirimkannya kembali ke Admin.
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
            <?php if ($isAdmin): ?>
                <div style="background-color: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 14px 18px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px;">
                    <i class="fa-solid fa-shield-halved" style="color: #7c3aed; font-size: 18px;"></i>
                    <div style="font-size: 13px; color: #5b21b6;">
                        <strong>Otoritas Admin:</strong> Sebagai Admin/Editor Utama, Anda dapat <strong>langsung mempublikasikan artikel</strong> tanpa perlu menunggu review, atau menyimpannya sebagai draft.
                    </div>
                </div>
            <?php else: ?>
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px;">
                    <i class="fa-solid fa-lightbulb" style="color: #f59e0b; font-size: 18px;"></i>
                    <div style="font-size: 13px; color: #475569;">
                        <strong>Alur Author:</strong> Pilih <strong>"Simpan Draft"</strong> bila artikel belum rampung ditulis. Pilih <strong>"Simpan & Ajukan Review"</strong> agar artikel masuk ke antrean Admin untuk ditinjau dan disetujui sebelum terbit.
                    </div>
                </div>
            <?php endif; ?>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0 20px 0;">

            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php if ($isAdmin): ?>
                        <!-- Tombol Langsung Publikasikan untuk Admin -->
                        <button type="submit" name="submit_action" value="publish" class="btn btn-success shadow-sm" style="font-weight: 600; padding: 10px 22px; font-size: 14px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 6px;"></i> Publikasikan Sekarang
                        </button>

                        <!-- Tombol Simpan Draft -->
                        <button type="submit" name="submit_action" value="draft" class="btn btn-default" style="font-weight: 500; padding: 10px 18px; font-size: 14px;">
                            <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Simpan sebagai Draft
                        </button>
                    <?php else: ?>
                        <!-- Tombol Simpan & Ajukan Review untuk Author -->
                        <button type="submit" name="submit_action" value="submit_review" class="btn btn-success shadow-sm" style="font-weight: 600; padding: 10px 22px; font-size: 14px;">
                            <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> 
                            <?= $model->isRejected() ? 'Simpan & Ajukan Ulang Review' : 'Simpan & Ajukan Review' ?>
                        </button>

                        <!-- Tombol Simpan Draft -->
                        <button type="submit" name="submit_action" value="draft" class="btn btn-default" style="font-weight: 500; padding: 10px 18px; font-size: 14px;">
                            <i class="fa-solid fa-floppy-disk" style="margin-right: 6px;"></i> Simpan sebagai Draft
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Tombol Batal -->
                <?= Html::a('<i class="fa-solid fa-arrow-left" style="margin-right: 4px;"></i> Kembali ke Dashboard', ['index'], [
                    'class' => 'btn btn-link text-muted',
                    'style' => 'text-decoration: none; font-size: 13.5px;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
