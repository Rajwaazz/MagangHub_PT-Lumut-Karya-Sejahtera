<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Post;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $currentStatus string|null */
/* @var $counts array */
/* @var $user app\models\User */

$isAdmin = $user->isAdmin();
$this->title = $isAdmin ? 'Manajemen Artikel & Review' : 'Dashboard Artikel Saya';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <!-- Header Section -->
    <div class="row" style="margin-bottom: 25px; display: flex; align-items: center; flex-wrap: wrap;">
        <div class="<?= $user->isAuthor() ? 'col-sm-8' : 'col-sm-12' ?>">
            <h2 style="margin: 0 0 6px 0; font-weight: 800; color: #0f172a; font-size: 24px; letter-spacing: -0.5px;">
                <i class="fa-solid <?= $isAdmin ? 'fa-shield-halved' : 'fa-pen-nib' ?>" style="color: #2563eb; margin-right: 8px;"></i>
                <?= Html::encode($this->title) ?>
            </h2>
            <p class="text-muted" style="margin: 0; font-size: 14px;">
                <?= $isAdmin 
                    ? 'Review artikel yang diajukan Author. Setujui untuk tayang di publik atau tolak dengan catatan revisi.' 
                    : 'Kelola artikel Anda, simpan draf, periksa catatan revisi, dan ajukan review ke Admin.' ?>
            </p>
        </div>
        <?php if ($user->isAuthor()): ?>
            <div class="col-sm-4 text-right">
                <?= Html::a('<i class="fa-solid fa-plus" style="margin-right: 6px;"></i> Tulis Artikel Baru', ['create'], [
                    'class' => 'btn btn-primary btn-lg shadow-sm',
                    'style' => 'font-weight: 600; padding: 10px 20px; font-size: 14px;'
                ]) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alert Notifikasi Penting untuk Author / Admin -->
    <?php if ($user->isAuthor() && !empty($counts[Post::STATUS_REJECTED])): ?>
        <div class="alert alert-danger" style="margin-bottom: 25px; display: flex; align-items: flex-start; gap: 12px;">
            <div style="font-size: 20px; color: #dc2626; margin-top: 2px;">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <strong style="font-size: 14px;">Perhatian: Ada <?= $counts[Post::STATUS_REJECTED] ?> artikel yang memerlukan revisi</strong>
                <p style="margin: 4px 0 0 0; color: #7f1d1d; font-size: 13px;">
                    Admin telah menolak artikel Anda dengan catatan perbaikan. Silakan periksa alasan penolakan pada tabel di bawah, edit artikel, lalu ajukan ulang.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isAdmin && !empty($counts[Post::STATUS_PENDING])): ?>
        <div class="alert alert-warning" style="margin-bottom: 25px; display: flex; align-items: flex-start; gap: 12px;">
            <div style="font-size: 20px; color: #d97706; margin-top: 2px;">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <strong style="font-size: 14px;">Antrean Review: <?= $counts[Post::STATUS_PENDING] ?> artikel baru menunggu konfirmasi</strong>
                <p style="margin: 4px 0 0 0; color: #78350f; font-size: 13px;">
                    Silakan periksa kelayakan konten dan pilih <strong>Setujui</strong> untuk mempublikasikan atau <strong>Tolak</strong> disertai alasan yang jelas.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pjax Container for Ultra Smooth AJAX Transitions without Full Page Refresh -->
    <?php Pjax::begin([
        'id' => 'post-pjax-container',
        'timeout' => 8000,
        'enablePushState' => true,
        'options' => ['class' => 'pjax-container']
    ]); ?>

    <!-- Summary KPI Stat Cards -->
    <div class="row" style="margin-bottom: 15px;">
        <?php if ($isAdmin): ?>
            <!-- STAT CARDS UNTUK ADMIN (4 KARTU: SEMUA, PENDING, PUBLISHED, REJECTED) -->
            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="<?= Url::to(['post/index']) ?>" class="stat-card-link">
                    <div class="stat-card <?= empty($currentStatus) ? 'active border-all' : '' ?>">
                        <div class="stat-icon all"><i class="fa-solid fa-folder-open"></i></div>
                        <div class="stat-details">
                            <div class="stat-number"><?= $counts['all'] ?></div>
                            <div class="stat-label">Total Masuk</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_PENDING]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_PENDING ? 'active border-pending' : '' ?>">
                        <div class="stat-icon pending"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #d97706;"><?= $counts[Post::STATUS_PENDING] ?></div>
                            <div class="stat-label">Menunggu Review</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_PUBLISHED]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_PUBLISHED ? 'active border-published' : '' ?>">
                        <div class="stat-icon published"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #059669;"><?= $counts[Post::STATUS_PUBLISHED] ?></div>
                            <div class="stat-label">Diterbitkan</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_REJECTED]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_REJECTED ? 'active border-rejected' : '' ?>">
                        <div class="stat-icon rejected"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #dc2626;"><?= $counts[Post::STATUS_REJECTED] ?></div>
                            <div class="stat-label">Ditolak</div>
                        </div>
                    </div>
                </a>
            </div>

        <?php else: ?>
            <!-- STAT CARDS UNTUK AUTHOR (5 KARTU) -->
            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= Url::to(['post/index']) ?>" class="stat-card-link">
                    <div class="stat-card <?= empty($currentStatus) ? 'active border-all' : '' ?>">
                        <div class="stat-icon all"><i class="fa-solid fa-folder-open"></i></div>
                        <div class="stat-details">
                            <div class="stat-number"><?= $counts['all'] ?></div>
                            <div class="stat-label">Semua</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-4 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_PENDING]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_PENDING ? 'active border-pending' : '' ?>">
                        <div class="stat-icon pending"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #d97706;"><?= $counts[Post::STATUS_PENDING] ?></div>
                            <div class="stat-label">Menunggu Review</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-4 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_PUBLISHED]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_PUBLISHED ? 'active border-published' : '' ?>">
                        <div class="stat-icon published"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #059669;"><?= $counts[Post::STATUS_PUBLISHED] ?></div>
                            <div class="stat-label">Diterbitkan</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_REJECTED]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_REJECTED ? 'active border-rejected' : '' ?>">
                        <div class="stat-icon rejected"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #dc2626;"><?= $counts[Post::STATUS_REJECTED] ?></div>
                            <div class="stat-label">Ditolak</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_DRAFT]) ?>" class="stat-card-link">
                    <div class="stat-card <?= $currentStatus === Post::STATUS_DRAFT ? 'active border-draft' : '' ?>">
                        <div class="stat-icon draft"><i class="fa-solid fa-file-pen"></i></div>
                        <div class="stat-details">
                            <div class="stat-number" style="color: #64748b;"><?= $counts[Post::STATUS_DRAFT] ?></div>
                            <div class="stat-label">Draft</div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Filter Tabs Nav-Pills Modern -->
    <div>
        <ul class="nav nav-pills-modern">
            <li class="<?= empty($currentStatus) ? 'active' : '' ?>">
                <a href="<?= Url::to(['post/index']) ?>">
                    Semua <span class="badge" style="background-color: #94a3b8;"><?= $counts['all'] ?></span>
                </a>
            </li>
            <li class="<?= $currentStatus === Post::STATUS_PENDING ? 'active' : '' ?>">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_PENDING]) ?>">
                    <i class="fa-solid fa-clock-rotate-left"></i> Menunggu Review 
                    <span class="badge" style="background-color: #f59e0b;"><?= $counts[Post::STATUS_PENDING] ?></span>
                </a>
            </li>
            <li class="<?= $currentStatus === Post::STATUS_PUBLISHED ? 'active' : '' ?>">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_PUBLISHED]) ?>">
                    <i class="fa-solid fa-circle-check"></i> Published 
                    <span class="badge" style="background-color: #10b981;"><?= $counts[Post::STATUS_PUBLISHED] ?></span>
                </a>
            </li>
            <li class="<?= $currentStatus === Post::STATUS_REJECTED ? 'active' : '' ?>">
                <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_REJECTED]) ?>">
                    <i class="fa-solid fa-circle-xmark"></i> Ditolak 
                    <span class="badge" style="background-color: #ef4444;"><?= $counts[Post::STATUS_REJECTED] ?></span>
                </a>
            </li>
            <?php if ($user->isAuthor()): ?>
                <li class="<?= $currentStatus === Post::STATUS_DRAFT ? 'active' : '' ?>">
                    <a href="<?= Url::to(['post/index', 'status' => Post::STATUS_DRAFT]) ?>">
                        <i class="fa-solid fa-file-pen"></i> Draft 
                        <span class="badge" style="background-color: #64748b;"><?= $counts[Post::STATUS_DRAFT] ?></span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- GridView Table Card Modern -->
    <div class="panel card-modern" style="margin-top: 10px;">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-modern', 'style' => 'margin-bottom: 0;'],
            'layout' => "<div class='table-responsive' style='margin-bottom: 0;'>{items}</div>\n<div class='panel-footer' style='background: #fff; padding: 14px 20px; border-top: 1px solid #e2e8f0;'><div class='row' style='display: flex; align-items: center;'><div class='col-sm-6 text-muted' style='font-size: 13px;'>{summary}</div><div class='col-sm-6 text-right'>{pager}</div></div></div>",
            'emptyText' => '<div class="text-center" style="padding: 50px 20px;"><div style="font-size: 42px; color: #cbd5e1; margin-bottom: 12px;"><i class="fa-solid fa-inbox"></i></div><h4 style="color: #64748b; font-weight: 600;">Tidak ada artikel pada kategori ini</h4><p class="text-muted" style="font-size: 13px;">Belum ada artikel yang sesuai dengan filter status yang dipilih.</p></div>',
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => '#',
                    'headerOptions' => ['style' => 'width: 45px; text-align: center; vertical-align: middle;'],
                    'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; color: #94a3b8; font-weight: 600;'],
                ],
                [
                    'attribute' => 'title',
                    'label' => 'Judul Artikel',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'vertical-align: middle;'],
                    'contentOptions' => ['style' => 'vertical-align: middle;'],
                    'value' => function ($model) {
                        $html = '<div style="margin-bottom: 4px;">'
                            . Html::a(Html::encode($model->title), ['view', 'id' => $model->idpost], [
                                'style' => 'font-weight: 700; font-size: 15px; color: #0f172a; text-decoration: none; line-height: 1.4;',
                                'class' => 'article-title-link'
                            ])
                            . '</div>';

                        $html .= '<div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">'
                            . '<span><i class="fa-solid fa-user-pen" style="color: #94a3b8; margin-right: 4px;"></i> ' . Html::encode($model->username) . '</span>'
                            . '<span><i class="fa-solid fa-calendar-day" style="color: #94a3b8; margin-right: 4px;"></i> ' . date('d M Y, H:i', strtotime($model->date)) . ' WIB</span>'
                            . '</div>';

                        // Tampilan alasan penolakan jika artikel ditolak
                        if ($model->isRejected() && !empty($model->reject_reason)) {
                            $html .= '<div class="rejection-box">'
                                . '<div style="font-weight: 700; margin-bottom: 3px; display: flex; align-items: center; gap: 6px;">'
                                . '<i class="fa-solid fa-triangle-exclamation"></i> Catatan Penolakan Admin:'
                                . '</div>'
                                . '<div style="line-height: 1.5;">' . Html::encode($model->reject_reason) . '</div>'
                                . '</div>';
                        }

                        return $html;
                    },
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Status',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width: 150px; text-align: center; vertical-align: middle; white-space: nowrap;'],
                    'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; white-space: nowrap;'],
                    'value' => function ($model) {
                        return $model->getStatusBadge();
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Aksi',
                    'headerOptions' => ['style' => 'width: 320px; min-width: 320px; text-align: center; vertical-align: middle; white-space: nowrap;'],
                    'contentOptions' => ['style' => 'text-align: center; vertical-align: middle; white-space: nowrap; padding: 12px 10px;'],
                    'template' => '{custom_actions}',
                    'buttons' => [
                        'custom_actions' => function ($url, $model, $key) use ($isAdmin, $user) {
                            $buttons = [];

                            // === AKSI UNTUK ADMIN ===
                            if ($isAdmin && $model->isPending()) {
                                // Tombol Setujui (Approve)
                                $buttons[] = Html::a(
                                    '<i class="fa-solid fa-check"></i> Setujui',
                                    ['approve', 'id' => $model->idpost],
                                    [
                                        'class' => 'btn btn-action-custom btn-success',
                                        'title' => 'Setujui dan Publikasikan',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Apakah Anda yakin ingin menyetujui artikel "' . Html::encode($model->title) . '" dan menerbitkannya?',
                                    ]
                                );

                                // Tombol Tolak (Reject)
                                $buttons[] = '<button type="button" class="btn btn-action-custom btn-danger btn-trigger-reject" '
                                    . 'data-id="' . $model->idpost . '" '
                                    . 'data-title="' . Html::encode($model->title) . '" '
                                    . 'title="Tolak Artikel dengan Catatan">'
                                    . '<i class="fa-solid fa-xmark"></i> Tolak</button>';
                            }

                            // === AKSI UNTUK AUTHOR (Ajukan Review) ===
                            if ($model->canSubmitReview()) {
                                $isRejected = $model->isRejected();
                                $buttons[] = Html::a(
                                    '<i class="fa-solid fa-paper-plane"></i> ' . ($isRejected ? 'Ajukan Ulang' : 'Ajukan Review'),
                                    ['submit-review', 'id' => $model->idpost],
                                    [
                                        'class' => 'btn btn-action-custom btn-warning',
                                        'title' => 'Kirim ke Admin untuk Direview',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Ajukan artikel "' . Html::encode($model->title) . '" kepada Admin untuk ditinjau?',
                                    ]
                                );
                            }

                            // Tombol Edit
                            if ($model->canEdit()) {
                                $buttons[] = Html::a(
                                    '<i class="fa-solid fa-pen-to-square"></i> Edit',
                                    ['update', 'id' => $model->idpost],
                                    [
                                        'class' => 'btn btn-action-custom btn-default',
                                        'title' => 'Edit / Perbaiki Artikel'
                                    ]
                                );
                            }

                            // Tombol Detail
                            $buttons[] = Html::a(
                                '<i class="fa-solid fa-eye"></i> Detail',
                                ['view', 'id' => $model->idpost],
                                [
                                    'class' => 'btn btn-action-custom btn-default',
                                    'title' => 'Lihat Detail Artikel'
                                ]
                            );

                            // Tombol Hapus
                            if ($isAdmin || ($model->username === $user->username && !$model->isPending())) {
                                $buttons[] = Html::a(
                                    '<i class="fa-solid fa-trash-can"></i> Hapus',
                                    ['delete', 'id' => $model->idpost],
                                    [
                                        'class' => 'btn btn-action-custom btn-action-delete',
                                        'title' => 'Hapus Artikel',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Apakah Anda yakin ingin menghapus artikel ini?',
                                    ]
                                );
                            }

                            return '<div class="action-btn-group">' . implode(' ', $buttons) . '</div>';
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>

    <?php Pjax::end(); ?>
</div>

<!-- Modal Dialog Alasan Penolakan untuk Admin -->
<style>
.modal-backdrop { z-index: 1040 !important; }
#modalRejectPost { z-index: 1055 !important; }
#modalRejectPost .modal-dialog { z-index: 1060 !important; position: relative; }
</style>
<div class="modal fade" id="modalRejectPost" tabindex="-1" role="dialog" aria-labelledby="modalRejectPostLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden; margin-top: 60px;">
            <?= Html::beginForm(['reject', 'id' => 0], 'post', ['id' => 'formRejectPost']) ?>
            <div class="modal-header" style="background-color: #fef2f2; border-bottom: 1px solid #fee2e2; padding: 18px 24px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #64748b; opacity: 0.8; font-size: 24px;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalRejectPostLabel" style="font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i> Tolak Publikasi Artikel
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px; background-color: #ffffff;">
                <p style="font-size: 13.5px; color: #475569; margin-bottom: 8px;">Artikel yang akan ditolak:</p>
                <div id="rejectArticleTitle" style="font-weight: 700; font-size: 15px; color: #0f172a; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;"></div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="reject_reason" style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                        Alasan Penolakan / Catatan Revisi <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        name="reject_reason" 
                        id="reject_reason" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Contoh: Mohon perbaiki tata bahasa pada paragraf ke-2 dan sertakan sumber referensi yang relevan sebelum diajukan kembali..." 
                        required 
                        style="font-size: 13.5px; line-height: 1.6;"></textarea>
                    <span class="help-block text-muted" style="font-size: 12px; margin-top: 6px; margin-bottom: 0;">
                        <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i> Catatan ini akan ditampilkan secara jelas di dashboard author agar mereka mengetahui apa yang harus direvisi.
                    </span>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
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
$rejectUrlBase = Url::to(['post/reject', 'id' => 'POST_ID_PLACEHOLDER']);
$js = <<<JS
$(document).on('click', '.btn-trigger-reject', function(e) {
    e.preventDefault();
    var postId = $(this).data('id');
    var postTitle = $(this).data('title');
    
    var formAction = '{$rejectUrlBase}'.replace('POST_ID_PLACEHOLDER', postId);
    $('#formRejectPost').attr('action', formAction);
    $('#rejectArticleTitle').text(postTitle);
    $('#reject_reason').val('');
    
    $('#modalRejectPost').appendTo('body').modal('show');
    setTimeout(function() {
        $('#reject_reason').focus();
    }, 450);
});
JS;
$this->registerJs($js);
?>
