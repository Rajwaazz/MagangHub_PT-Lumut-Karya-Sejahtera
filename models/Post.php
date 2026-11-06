<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "post".
 *
 * @property int $idpost
 * @property string $title
 * @property string $content
 * @property string $date
 * @property string $username
 * @property string $status
 * @property string|null $reject_reason
 *
 * @property Account $username0
 */
class Post extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED = 'rejected';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required', 'message' => '{attribute} tidak boleh kosong.'],
            [['title', 'content', 'reject_reason'], 'string'],
            [['date'], 'safe'],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['status'], 'in', 'range' => [
                self::STATUS_DRAFT,
                self::STATUS_PENDING,
                self::STATUS_PUBLISHED,
                self::STATUS_REJECTED,
            ]],
            [['username'], 'string', 'max' => 45],
            [['username'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['username' => 'username']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idpost' => 'ID',
            'title' => 'Judul Artikel',
            'content' => 'Konten Artikel',
            'date' => 'Tanggal & Waktu',
            'username' => 'Penulis',
            'status' => 'Status Publikasi',
            'reject_reason' => 'Alasan Penolakan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsername0()
    {
        return $this->hasOne(Account::className(), ['username' => 'username']);
    }

    /**
     * Daftar status dan label teks
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Menunggu Review',
            self::STATUS_PUBLISHED => 'Diterbitkan (Published)',
            self::STATUS_REJECTED => 'Ditolak (Rejected)',
        ];
    }

    /**
     * Dapatkan teks status
     */
    public function getStatusLabel()
    {
        $list = self::getStatusList();
        return isset($list[$this->status]) ? $list[$this->status] : ucfirst($this->status);
    }

    /**
     * Badge visual status yang informatif dan menarik untuk UI
     */
    public function getStatusBadge()
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return '<span class="badge-status badge-status-draft"><i class="fa-solid fa-file-lines"></i> Draft</span>';
            case self::STATUS_PENDING:
                return '<span class="badge-status badge-status-pending"><i class="fa-solid fa-clock"></i> Menunggu Review</span>';
            case self::STATUS_PUBLISHED:
                return '<span class="badge-status badge-status-published"><i class="fa-solid fa-circle-check"></i> Published</span>';
            case self::STATUS_REJECTED:
                return '<span class="badge-status badge-status-rejected"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>';
            default:
                return '<span class="badge-status badge-status-draft">' . Html::encode($this->status) . '</span>';
        }
    }

    /**
     * Cek status artikel
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Cek apakah user saat ini boleh mengedit artikel ini
     * Hanya Author pemilik artikel yang boleh mengedit saat status Draft atau Ditolak (Revisi)
     */
    public function canEdit()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $identity = Yii::$app->user->identity;

        // Hanya Author pemilik artikel yang dapat mengedit saat status Draft atau Ditolak
        if ($identity->isAuthor() && $this->username === $identity->username) {
            return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
        }

        return false;
    }

    /**
     * Cek apakah author boleh mengajukan review
     */
    public function canSubmitReview()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $identity = Yii::$app->user->identity;

        // Hanya Author pemilik artikel yang mengajukan review (Admin tidak perlu mengajukan review)
        return $identity->isAuthor() 
            && ($this->username === $identity->username) 
            && in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    /**
     * Cek apakah admin boleh approve/reject artikel
     */
    public function canReview()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $identity = Yii::$app->user->identity;
        return strtolower($identity->role) === 'admin' && $this->status === self::STATUS_PENDING;
    }
}
