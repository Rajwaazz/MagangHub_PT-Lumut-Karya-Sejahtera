<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Post;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $user app\models\User|null */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Artikel', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isAdmin = $user ? $user->isAdmin() : false;
$isOwner = $user ? ($model->username === $user->username) : false;

// Estimasi waktu baca
$wordCount = str_word_count(strip_tags($model->content));
$readTime = max(1, ceil($wordCount / 200));
?>
<div class="post-view" style="max-width: 920px; margin: 0 auto;">

    <!-- Top Action Toolbar -->
    <div class="row" style="margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div>
            <?= Html::a('<i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Kembali ke Daftar', ['index'], [
                'class' => 'btn btn-default shadow-sm',
                'style' => 'font-weight: 500;'
            ]) ?>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <!-- Tombol Admin Review saat Pending -->
            <?php if ($isAdmin && $model->isPending()): ?>
                <?= Html::a('<i class="fa-solid fa-check" style="margin-right: 5px;"></i> Setujui & Publikasikan', ['approve', 'id' => $model->idpost], [
                    'class' => 'btn btn-success shadow-sm',
                    'style' => 'font-weight: 600;',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah Anda yakin ingin menyetujui artikel ini untuk langsung dipublikasikan?',
                ]) ?>
                <button type="button" class="btn btn-danger shadow-sm btn-trigger-reject" style="font-weight: 600;">
                    <i class="fa-solid fa-xmark" style="margin-right: 5px;"></i> Tolak Artikel
                </button>
            <?php endif; ?>

            <!-- Tombol Ajukan Review untuk Author -->
            <?php if ($model->canSubmitReview()): ?>
                <?= Html::a('<i class="fa-solid fa-paper-plane" style="margin-right: 5px;"></i> ' . ($model->isRejected() ? 'Ajukan Ulang Review' : 'Ajukan Review'), ['submit-review', 'id' => $model->idpost], [
                    'class' => 'btn btn-warning shadow-sm',
                    'style' => 'font-weight: 600;',
                    'data-method' => 'post',
                    'data-confirm' => 'Ajukan artikel ini kepada Admin untuk ditinjau?',
                ]) ?>
            <?php endif; ?>

            <!-- Tombol Edit jika boleh diedit -->
            <?php if ($model->canEdit()): ?>
                <?= Html::a('<i class="fa-solid fa-pen-to-square" style="margin-right: 5px;"></i> ' . ($model->isRejected() ? 'Perbaiki Artikel' : 'Edit Artikel'), ['update', 'id' => $model->idpost], [
                    'class' => 'btn btn-primary shadow-sm',
                    'style' => 'font-weight: 500;'
                ]) ?>
            <?php endif; ?>

            <!-- Tombol Hapus -->
            <?php if ($user && ($isAdmin || ($isOwner && !$model->isPending()))): ?>
                <?= Html::a('<i class="fa-solid fa-trash-can"></i>', ['delete', 'id' => $model->idpost], [
                    'class' => 'btn btn-default shadow-sm text-danger',
                    'title' => 'Hapus Artikel',
                    'style' => 'color: #ef4444 !important;',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah Anda yakin ingin menghapus artikel ini?',
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Status Khusus -->
    <?php if ($model->isRejected()): ?>
        <div class="alert alert-danger" style="margin-bottom: 25px; padding: 20px; border-radius: 12px; border-left: 5px solid #ef4444; background-color: #fef2f2;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="font-size: 24px; color: #dc2626; margin-top: 2px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div style="flex-grow: 1;">
                    <h4 style="margin: 0 0 8px 0; font-weight: 700; color: #991b1b; font-size: 16px;">
                        Artikel Ini Ditolak oleh Admin
                    </h4>
                    <p style="margin: 0 0 10px 0; color: #7f1d1d; font-size: 13.5px;">
                        Berikut catatan perbaikan atau alasan penolakan yang diberikan oleh Admin:
                    </p>
                    <div style="background-color: #ffffff; border: 1px solid #fecaca; border-radius: 8px; padding: 14px 16px; color: #991b1b; font-size: 14px; line-height: 1.6;">
                        <?= nl2br(Html::encode($model->reject_reason)) ?>
                    </div>
                    <div style="margin-top: 12px; font-size: 13px; color: #7f1d1d; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Silakan klik tombol <strong>"Perbaiki Artikel"</strong> di atas untuk melakukan revisi, lalu ajukan review kembali.</span>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($model->isPending()): ?>
        <div class="alert alert-warning" style="margin-bottom: 25px; padding: 16px 20px; border-radius: 12px; border-left: 5px solid #f59e0b; display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 22px; color: #d97706;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <strong style="color: #92400e; font-size: 14px;">Sedang Menunggu Review Admin</strong>
                <p style="margin: 2px 0 0 0; color: #78350f; font-size: 13px;">
                    Artikel ini telah dikirimkan ke antrean review Admin. Konten artikel tidak dapat diedit sementara hingga proses review selesai.
                </p>
            </div>
        </div>
    <?php elseif ($model->isPublished()): ?>
        <div class="alert alert-success" style="margin-bottom: 25px; padding: 14px 18px; border-radius: 12px; border-left: 5px solid #10b981; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #059669;"></i>
            <span style="font-size: 13.5px; color: #166534;">
                Artikel ini telah <strong>disetujui dan aktif diterbitkan</strong>. Artikel dapat dibaca oleh seluruh pengunjung blog di halaman utama.
            </span>
        </div>
    <?php elseif ($model->isDraft()): ?>
        <div class="alert alert-info" style="margin-bottom: 25px; padding: 14px 18px; border-radius: 12px; border-left: 5px solid #3b82f6; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-info" style="font-size: 18px; color: #2563eb;"></i>
            <span style="font-size: 13.5px; color: #1e40af;">
                Artikel ini berstatus <strong>Draft pribadi</strong>. Hanya Anda yang dapat melihatnya. Klik <strong>"Ajukan Review"</strong> jika sudah siap dikirim ke Admin.
            </span>
        </div>
    <?php endif; ?>

    <!-- Tampilan Konten Artikel Card Modern -->
    <div class="panel card-modern" style="margin-bottom: 30px;">
        <div class="panel-body" style="padding: 40px 45px;">
            <!-- Metadata Bar -->
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;">
                <?= $model->getStatusBadge() ?>
                <span style="color: #94a3b8;">&bull;</span>
                <span class="text-muted" style="font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-regular fa-calendar" style="color: #94a3b8;"></i>
                    <?= date('d F Y, H:i', strtotime($model->date)) ?> WIB
                </span>
                <span style="color: #94a3b8;">&bull;</span>
                <span class="text-muted" style="font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-regular fa-user" style="color: #94a3b8;"></i>
                    Penulis: <strong style="color: #334155;"><?= Html::encode($model->username) ?></strong>
                </span>
                <span style="color: #94a3b8;">&bull;</span>
                <span class="text-muted" style="font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-regular fa-clock" style="color: #94a3b8;"></i>
                    <?= $readTime ?> menit baca
                </span>
            </div>

            <!-- Judul Artikel -->
            <h1 style="margin-top: 0; margin-bottom: 25px; font-weight: 800; color: #0f172a; font-size: 30px; line-height: 1.35; letter-spacing: -0.5px;">
                <?= Html::encode($model->title) ?>
            </h1>

            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 25px 0 35px 0;">

            <!-- Konten Artikel -->
            <div class="article-content" style="font-size: 16px; line-height: 1.85; color: #334155;">
                <?= $model->content ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog Alasan Penolakan untuk Admin -->
<?php if ($isAdmin): ?>
<style>
.modal-backdrop { z-index: 1040 !important; }
#modalRejectPost { z-index: 1055 !important; }
#modalRejectPost .modal-dialog { z-index: 1060 !important; position: relative; }
</style>
<div class="modal fade" id="modalRejectPost" tabindex="-1" role="dialog" aria-labelledby="modalRejectPostLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden; margin-top: 60px;">
            <?= Html::beginForm(['reject', 'id' => $model->idpost], 'post', ['id' => 'formRejectPost']) ?>
            <div class="modal-header" style="background-color: #fef2f2; border-bottom: 1px solid #fee2e2; padding: 18px 24px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #64748b; opacity: 0.8; font-size: 24px;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalRejectPostLabel" style="font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i> Tolak Publikasi Artikel
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px; background-color: #ffffff;">
                <p style="font-size: 13.5px; color: #475569; margin-bottom: 8px;">Artikel yang akan ditolak:</p>
                <div style="font-weight: 700; font-size: 15px; color: #0f172a; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
                    <?= Html::encode($model->title) ?>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="reject_reason_view" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                        Tuliskan Alasan Penolakan / Catatan Perbaikan <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        name="reject_reason" 
                        id="reject_reason_view" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Tuliskan catatan detail mengapa artikel ditolak dan bagian mana yang perlu diperbaiki oleh Author..." 
                        required 
                        style="font-size: 13.5px; line-height: 1.6;"></textarea>
                    <span class="help-block text-muted" style="font-size: 12px; margin-top: 6px; margin-bottom: 0;">
                        <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i> Catatan ini akan ditampilkan secara langsung di dashboard author.
                    </span>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger" style="font-weight: 600; padding: 8px 18px;">
                    <i class="fa-solid fa-paper-plane" style="margin-right: 5px;"></i> Kirim Penolakan
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('.btn-trigger-reject').on('click', function(e) {
    e.preventDefault();
    $('#modalRejectPost').appendTo('body').modal('show');
    setTimeout(function() {
        $('#reject_reason_view').focus();
    }, 450);
});
JS;
$this->registerJs($js);
?>
<?php endif; ?>
