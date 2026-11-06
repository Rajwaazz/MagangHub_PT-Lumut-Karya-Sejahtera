<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $data app\models\Post[] */

$this->title = 'BlogHub - Portal Artikel & Wawasan';
?>
<div class="site-index">

    <!-- Hero Header Modern -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 16px; padding: 50px 35px; margin-bottom: 40px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15); position: relative; overflow: hidden;">
        <div style="max-width: 720px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.3); color: #93c5fd; padding: 5px 14px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 18px;">
                <i class="fa-solid fa-sparkles"></i> Portal Artikel & Publikasi Terkurasi
            </div>
            <h1 style="font-size: 36px; font-weight: 800; line-height: 1.25; margin: 0 0 15px 0; letter-spacing: -0.5px;">
                Eksplorasi Gagasan & Pengetahuan Terbaik
            </h1>
            <p style="font-size: 16px; color: #94a3b8; line-height: 1.6; margin: 0 0 25px 0; font-weight: 400;">
                Kumpulan artikel pilihan yang telah ditinjau dan disetujui secara ketat oleh tim redaksi untuk memastikan kualitas dan keakuratan informasi.
            </p>
            <?php if (!Yii::$app->user->isGuest): ?>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <?php if (Yii::$app->user->identity->isAdmin()): ?>
                        <?= Html::a('<i class="fa-solid fa-clipboard-check" style="margin-right: 6px;"></i> Antrean Review & Moderasi', ['/post/index'], [
                            'class' => 'btn btn-primary btn-lg shadow-sm',
                            'style' => 'font-weight: 600; padding: 10px 24px; font-size: 14px; border-radius: 8px;'
                        ]) ?>
                    <?php else: ?>
                        <?= Html::a('<i class="fa-solid fa-plus" style="margin-right: 6px;"></i> Tulis Artikel Baru', ['/post/create'], [
                            'class' => 'btn btn-primary btn-lg shadow-sm',
                            'style' => 'font-weight: 600; padding: 10px 24px; font-size: 14px; border-radius: 8px;'
                        ]) ?>
                        <?= Html::a('<i class="fa-solid fa-table-columns" style="margin-right: 6px;"></i> Buka Dashboard', ['/post/index'], [
                            'class' => 'btn btn-default btn-lg',
                            'style' => 'font-weight: 500; padding: 10px 20px; font-size: 14px; border-radius: 8px; background: rgba(255, 255, 255, 0.1); color: #fff; border-color: rgba(255, 255, 255, 0.2);'
                        ]) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section Heading -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px;">
        <div>
            <h3 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 20px;">
                Artikel Terbaru
            </h3>
            <p class="text-muted" style="margin: 4px 0 0 0; font-size: 13.5px;">
                Daftar artikel yang baru saja disetujui dan diterbitkan.
            </p>
        </div>
        <div style="font-size: 13px; color: #64748b;">
            Total: <strong><?= count($data) ?> artikel</strong>
        </div>
    </div>

    <!-- Article Cards List -->
    <div class="body-content">
        <?php if (empty($data)): ?>
            <div class="panel card-modern text-center" style="padding: 60px 20px;">
                <div style="font-size: 50px; color: #cbd5e1; margin-bottom: 16px;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 style="color: #475569; font-weight: 700; margin: 0 0 8px 0;">Belum Ada Artikel yang Diterbitkan</h3>
                <p class="text-muted" style="max-width: 450px; margin: 0 auto 20px auto; font-size: 14px;">
                    Artikel yang baru diajukan oleh Author saat ini masih dalam antrean review oleh Admin.
                </p>
                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAuthor()): ?>
                    <?= Html::a('<i class="fa-solid fa-plus"></i> Buat Artikel Pertama', ['/post/create'], [
                        'class' => 'btn btn-primary',
                        'style' => 'font-weight: 600;'
                    ]) ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($data as $row): 
                    $wordCount = str_word_count(strip_tags($row->content));
                    $readTime = max(1, ceil($wordCount / 200));
                    $authorInitial = strtoupper(substr($row->username, 0, 1));
                ?>
                    <div class="col-md-6" style="margin-bottom: 30px;">
                        <div class="panel card-modern" style="height: 100%; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="panel-body" style="padding: 28px; flex-grow: 1; display: flex; flex-direction: column;">
                                <!-- Top Tags & Read Time -->
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                                    <span class="badge-status badge-status-published">
                                        <i class="fa-solid fa-circle-check"></i> Published
                                    </span>
                                    <span class="text-muted" style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-regular fa-clock"></i> <?= $readTime ?> menit baca
                                    </span>
                                </div>

                                <!-- Judul Artikel -->
                                <h3 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 700; line-height: 1.4;">
                                    <?= Html::a(Html::encode($row->title), ['/post/view', 'id' => $row->idpost], [
                                        'style' => 'color: #0f172a; text-decoration: none;'
                                    ]) ?>
                                </h3>

                                <!-- Snippet Konten -->
                                <p class="text-muted" style="font-size: 14px; line-height: 1.65; flex-grow: 1; margin-bottom: 22px;">
                                    <?= Html::encode(StringHelper::truncateWords(strip_tags($row->content), 26, '...')) ?>
                                </p>

                                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 0 0 16px 0;">

                                <!-- Author Info & Baca Selengkapnya -->
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px;">
                                            <?= $authorInitial ?>
                                        </div>
                                        <div>
                                            <div style="font-size: 12.5px; font-weight: 600; color: #334155;">
                                                <?= Html::encode($row->username) ?>
                                            </div>
                                            <div style="font-size: 11px; color: #94a3b8;">
                                                <?= date('d M Y', strtotime($row->date)) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?= Html::a('Baca <i class="fa-solid fa-arrow-right" style="font-size: 11px; margin-left: 4px;"></i>', ['/post/view', 'id' => $row->idpost], [
                                        'class' => 'btn btn-default btn-sm',
                                        'style' => 'font-weight: 600; border-radius: 6px;'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
