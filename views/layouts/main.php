<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Top Progress Indicator for Smooth Page Loading -->
<div id="top-progress-bar"></div>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<i class="fa-solid fa-feather-pointed" style="color: #2563eb; margin-right: 6px;"></i> <span><strong>Blog</strong>Hub</span>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top navbar-modern',
        ],
    ]);

    $isGuest = Yii::$app->user->isGuest;
    $isAdmin = !$isGuest && Yii::$app->user->identity->isAdmin();

    $menuItems = [
        ['label' => '<i class="fa-solid fa-house" style="margin-right: 5px;"></i> Home', 'url' => ['/site/index']],
    ];

    if (!$isGuest) {
        $postLabel = '<i class="fa-solid fa-newspaper" style="margin-right: 5px;"></i> Artikel';
        if ($isAdmin) {
            $pendingCount = \app\models\Post::find()->where(['status' => \app\models\Post::STATUS_PENDING])->count();
            if ($pendingCount > 0) {
                $postLabel .= ' <span class="badge" style="background-color: #f59e0b; margin-left: 4px;" title="' . $pendingCount . ' artikel menunggu review">' . $pendingCount . '</span>';
            }
        }
        $menuItems[] = [
            'label' => $postLabel,
            'url' => ['/post/index'],
            'encode' => false,
        ];

        if ($isAdmin) {
            $menuItems[] = ['label' => '<i class="fa-solid fa-users-gear" style="margin-right: 5px;"></i> Kelola Akun', 'url' => ['/account/index']];
        }
    }

    if ($isGuest) {
        $menuItems[] = [
            'label' => '<i class="fa-solid fa-right-to-bracket" style="margin-right: 5px;"></i> Masuk',
            'url' => ['/site/login']
        ];
    } else {
        $user = Yii::$app->user->identity;
        $initial = strtoupper(substr($user->name, 0, 1));
        $roleClass = $isAdmin ? 'admin' : '';
        $roleLabel = $isAdmin ? 'Admin' : 'Author';
        $roleBadgeBg = $isAdmin ? '#ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;' : '#e0f2fe; color: #0369a1; border: 1px solid #bae6fd;';

        $menuItems[] = '<li style="display: flex; align-items: center; padding: 12px 15px;">'
            . '<div style="display: flex; align-items: center; gap: 10px;">'
            . '<span class="avatar-circle ' . $roleClass . '">' . $initial . '</span>'
            . '<span style="font-weight: 600; font-size: 13px; color: #1e293b;">' . Html::encode($user->name) . '</span>'
            . '<span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; ' . $roleBadgeBg . '">' . $roleLabel . '</span>'
            . Html::beginForm(['/site/logout'], 'post', ['style' => 'display: inline; margin: 0;'])
            . Html::submitButton(
                '<i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar',
                ['class' => 'btn btn-default btn-xs', 'style' => 'border-radius: 6px; padding: 4px 8px; margin-left: 5px; color: #64748b;']
            )
            . Html::endForm()
            . '</div>'
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 text-muted">
                &copy; <?= date('Y') ?> <strong>BlogHub</strong> Publishing Workflow System. All rights reserved.
            </div>
            <div class="col-sm-6 text-right text-muted">
                <span style="display: inline-flex; align-items: center; gap: 6px;">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></span>
                    Sistem Aktif &bull; Yii Framework 2.0
                </span>
            </div>
        </div>
    </div>
</footer>

<?php
$smoothJs = <<<JS
function startProgressBar() {
    var bar = $('#top-progress-bar');
    bar.removeClass('done').addClass('loading');
}
function finishProgressBar() {
    var bar = $('#top-progress-bar');
    bar.addClass('done').removeClass('loading');
    setTimeout(function() {
        bar.css('width', '0%');
    }, 350);
}

// Hook Pjax lifecycle untuk smooth state
$(document).on('pjax:start', function() {
    startProgressBar();
    $('#post-pjax-container').addClass('pjax-loading-state');
});

$(document).on('pjax:end pjax:complete', function() {
    finishProgressBar();
    $('#post-pjax-container').removeClass('pjax-loading-state');
});

// Smooth loading feedback untuk navigasi reguler
$(document).on('click', 'a:not([target="_blank"]):not([href^="#"]):not([data-method]):not([data-confirm]):not([data-toggle]):not(.btn-trigger-reject)', function(e) {
    var href = $(this).attr('href');
    if (href && href !== '#' && href.indexOf('javascript:') !== 0) {
        startProgressBar();
    }
});
JS;
$this->registerJs($smoothJs);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
