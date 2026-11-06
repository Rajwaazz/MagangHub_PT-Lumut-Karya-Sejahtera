<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;

/**
 * PostController implements the CRUD and review workflow actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['?', '@'], // Publik dan user login boleh melihat action view
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            // Hanya Author yang memiliki hak untuk membuat artikel baru
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAuthor();
                        },
                    ],
                    [
                        'actions' => ['index', 'update', 'delete', 'submit-review'],
                        'allow' => true,
                        'roles' => ['@'], // Harus login untuk dashboard & aksi
                    ],
                    [
                        'actions' => ['approve', 'reject'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
                        },
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'submit-review' => ['POST'],
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists Post models with status filtering and dashboard metrics.
     * @return mixed
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $status = Yii::$app->request->get('status');

        $query = Post::find();

        if ($user->isAuthor()) {
            // Author hanya melihat draf dan artikel miliknya sendiri
            $query->where(['username' => $user->username]);
            $baseCountQuery = Post::find()->where(['username' => $user->username]);

            $counts = [
                'all' => (clone $baseCountQuery)->count(),
                Post::STATUS_DRAFT => (clone $baseCountQuery)->andWhere(['status' => Post::STATUS_DRAFT])->count(),
                Post::STATUS_PENDING => (clone $baseCountQuery)->andWhere(['status' => Post::STATUS_PENDING])->count(),
                Post::STATUS_PUBLISHED => (clone $baseCountQuery)->andWhere(['status' => Post::STATUS_PUBLISHED])->count(),
                Post::STATUS_REJECTED => (clone $baseCountQuery)->andWhere(['status' => Post::STATUS_REJECTED])->count(),
            ];
        } else {
            // Admin bertindak sebagai reviewer: melihat semua artikel yang masuk ke siklus review (pending, published, rejected)
            // Draft pribadi milik author yang belum diajukan tidak dicampuri oleh admin
            $query->where(['!=', 'status', Post::STATUS_DRAFT]);
            $baseCountQuery = Post::find()->where(['!=', 'status', Post::STATUS_DRAFT]);

            $counts = [
                'all' => (clone $baseCountQuery)->count(),
                Post::STATUS_PENDING => Post::find()->where(['status' => Post::STATUS_PENDING])->count(),
                Post::STATUS_PUBLISHED => Post::find()->where(['status' => Post::STATUS_PUBLISHED])->count(),
                Post::STATUS_REJECTED => Post::find()->where(['status' => Post::STATUS_REJECTED])->count(),
                Post::STATUS_DRAFT => 0,
            ];
        }

        // Filter status jika parameter status valid diberikan
        if (!empty($status) && in_array($status, [Post::STATUS_DRAFT, Post::STATUS_PENDING, Post::STATUS_PUBLISHED, Post::STATUS_REJECTED])) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy('date DESC'),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
            'counts' => $counts,
            'user' => $user,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException if author attempts to access another's private post
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if (Yii::$app->user->isGuest) {
            if (!$model->isPublished()) {
                throw new ForbiddenHttpException('Artikel ini belum dipublikasikan.');
            }
        } else {
            // Author hanya boleh melihat artikel miliknya sendiri atau artikel orang lain yang sudah published
            if ($user->isAuthor() && $model->username !== $user->username && !$model->isPublished()) {
                throw new ForbiddenHttpException('Anda tidak memiliki izin untuk melihat artikel ini.');
            }
        }

        return $this->render('view', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Creates a new Post model (Khusus Author).
     * @return mixed
     * @throws ForbiddenHttpException jika diakses bukan oleh Author
     */
    public function actionCreate()
    {
        $user = Yii::$app->user->identity;

        if (!$user->isAuthor()) {
            throw new ForbiddenHttpException('Akses ditolak. Fitur membuat artikel baru hanya diperuntukkan bagi Author. Peran Admin adalah meninjau dan mengelola artikel.');
        }

        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {
            $model->date = date('Y-m-d H:i:s');
            $model->username = $user->username;

            $submitAction = Yii::$app->request->post('submit_action');
            if ($submitAction === 'submit_review') {
                $model->status = Post::STATUS_PENDING;
            } else {
                $model->status = Post::STATUS_DRAFT;
            }

            if ($model->save()) {
                if ($model->isPending()) {
                    Yii::$app->session->setFlash('success', 'Artikel berhasil dibuat dan langsung diajukan untuk direview oleh Admin.');
                } else {
                    Yii::$app->session->setFlash('info', 'Artikel berhasil disimpan sebagai Draft.');
                }
                return $this->redirect(['view', 'id' => $model->idpost]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException if not allowed to edit
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        // Cek izin edit
        if (!$model->canEdit()) {
            if ($model->isPending()) {
                Yii::$app->session->setFlash('warning', 'Artikel sedang dalam proses review oleh Admin dan tidak dapat diedit saat ini.');
                return $this->redirect(['view', 'id' => $model->idpost]);
            }
            throw new ForbiddenHttpException('Anda tidak memiliki izin untuk mengedit artikel ini.');
        }

        if ($model->load(Yii::$app->request->post())) {
            $submitAction = Yii::$app->request->post('submit_action');
            if ($submitAction === 'submit_review') {
                $model->status = Post::STATUS_PENDING;
            } else {
                $model->status = Post::STATUS_DRAFT;
            }

            $model->date = date('Y-m-d H:i:s');
            if ($model->save()) {
                if ($model->isPending()) {
                    Yii::$app->session->setFlash('success', 'Artikel berhasil diperbarui dan telah diajukan ulang untuk direview oleh Admin.');
                } else {
                    Yii::$app->session->setFlash('success', 'Perubahan pada artikel berhasil disimpan sebagai Draft.');
                }
                return $this->redirect(['view', 'id' => $model->idpost]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Author mengajukan artikel draft / ditolak untuk review oleh admin (status -> pending).
     * @param integer $id
     * @return mixed
     */
    public function actionSubmitReview($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        // Verifikasi kepemilikan artikel atau Admin
        if (!$user->isAdmin() && $model->username !== $user->username) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak untuk mengajukan review untuk artikel ini.');
        }

        if (!in_array($model->status, [Post::STATUS_DRAFT, Post::STATUS_REJECTED])) {
            Yii::$app->session->setFlash('warning', 'Hanya artikel berstatus Draft atau Ditolak yang dapat diajukan untuk review.');
            return $this->redirect(['index']);
        }

        $model->status = Post::STATUS_PENDING;
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Artikel "' . Html::encode($model->title) . '" berhasil diajukan untuk review. Admin akan segera meninjau artikel Anda.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal mengajukan artikel untuk review.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Admin menyetujui artikel (status -> published).
     * @param integer $id
     * @return mixed
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        if (!$model->isPending()) {
            Yii::$app->session->setFlash('warning', 'Hanya artikel berstatus Menunggu Review yang dapat disetujui.');
            return $this->redirect(['index']);
        }

        $model->status = Post::STATUS_PUBLISHED;
        $model->reject_reason = null; // Reset alasan penolakan jika sebelumnya pernah ditolak

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Artikel "' . Html::encode($model->title) . '" berhasil disetujui dan telah dipublikasikan ke publik!');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menyetujui artikel.');
        }

        return $this->redirect(['index', 'status' => Post::STATUS_PENDING]);
    }

    /**
     * Admin menolak artikel dengan alasan (status -> rejected).
     * @param integer $id
     * @return mixed
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);

        if (!$model->isPending()) {
            Yii::$app->session->setFlash('warning', 'Hanya artikel berstatus Menunggu Review yang dapat ditolak.');
            return $this->redirect(['index']);
        }

        $reason = Yii::$app->request->post('reject_reason');
        if (empty(trim($reason))) {
            Yii::$app->session->setFlash('error', 'Alasan penolakan wajib diisi agar Author mengetahui apa yang harus diperbaiki.');
            return $this->redirect(['index', 'status' => Post::STATUS_PENDING]);
        }

        $model->status = Post::STATUS_REJECTED;
        $model->reject_reason = trim($reason);

        if ($model->save(false)) {
            Yii::$app->session->setFlash('danger', 'Artikel "' . Html::encode($model->title) . '" telah ditolak. Alasan penolakan telah dikirimkan ke Author.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menolak artikel.');
        }

        return $this->redirect(['index', 'status' => Post::STATUS_PENDING]);
    }

    /**
     * Deletes an existing Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException if not allowed to delete
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if (!$user->isAdmin() && $model->username !== $user->username) {
            throw new ForbiddenHttpException('Anda tidak memiliki izin untuk menghapus artikel ini.');
        }

        $title = $model->title;
        $model->delete();
        Yii::$app->session->setFlash('success', 'Artikel "' . Html::encode($title) . '" berhasil dihapus.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman artikel yang diminta tidak ditemukan.');
    }
}
