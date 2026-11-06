<?php

use yii\db\Migration;

class m261106_032949_add_status_and_reject_reason_to_post extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%post}}', 'status', $this->string(20)->notNull()->defaultValue('draft'));
        $this->addColumn('{{%post}}', 'reject_reason', $this->text()->null());

        // Update post dummy yang sudah ada menjadi published agar tetap tampil
        $this->update('{{%post}}', ['status' => 'published']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%post}}', 'reject_reason');
        $this->dropColumn('{{%post}}', 'status');

        return true;
    }
}
