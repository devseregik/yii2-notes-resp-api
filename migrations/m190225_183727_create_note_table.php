<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%note}}`.
 */
class m190225_183727_create_note_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%note}}', [
            'id'           => $this->primaryKey(),
            'user_id'      => $this->integer()->notNull(),
            'title'        => $this->string()->notNull(),
            'text'         => $this->text()->notNull(),
            'published_at' => $this->integer(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer(),
            'deleted_at'   => $this->integer(),
        ]);
        $this->addForeignKey('{{%fk-note_user}}', '{{%note}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-note_user}}', '{{%note}}');
        $this->dropTable('{{%note}}');
    }
}
