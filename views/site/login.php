<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Masuk ke Sistem';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login" style="max-width: 440px; margin: 40px auto;">

    <div class="panel card-modern" style="border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);">
        <div class="panel-body" style="padding: 40px 32px;">
            
            <div class="text-center" style="margin-bottom: 30px;">
                <div style="width: 52px; height: 52px; border-radius: 12px; background-color: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 14px;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3 style="margin: 0 0 6px 0; font-weight: 800; color: #0f172a; font-size: 22px; letter-spacing: -0.5px;">
                    Masuk ke BlogHub
                </h3>
                <p class="text-muted" style="margin: 0; font-size: 13.5px;">
                    Silakan masukkan akun Anda untuk melanjutkan.
                </p>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'enableClientValidation' => true,
            ]); ?>

                <div style="margin-bottom: 18px;">
                    <label style="font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Username
                    </label>
                    <?= $form->field($model, 'username')->textInput([
                        'id' => 'input-username',
                        'autofocus' => true,
                        'placeholder' => 'Masukkan username Anda...',
                        'style' => 'height: 44px; border-radius: 8px; font-size: 14px;'
                    ])->label(false) ?>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Password
                    </label>
                    <?= $form->field($model, 'password')->passwordInput([
                        'id' => 'input-password',
                        'placeholder' => 'Masukkan password Anda...',
                        'style' => 'height: 44px; border-radius: 8px; font-size: 14px;'
                    ])->label(false) ?>
                </div>

                <div style="margin-bottom: 22px;">
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'style' => 'margin-top: 2px;'
                    ])->label('Ingat saya di perangkat ini') ?>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <?= Html::submitButton('<i class="fa-solid fa-right-to-bracket" style="margin-right: 6px;"></i> Masuk Sekarang', [
                        'class' => 'btn btn-primary btn-block btn-lg shadow-sm',
                        'style' => 'font-weight: 600; border-radius: 8px; padding: 11px; font-size: 14.5px;',
                        'name' => 'login-button'
                    ]) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
