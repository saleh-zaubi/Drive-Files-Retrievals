<?php

use yii\db\Migration;

/**
 * Class m210828_170420_user_google_auth_token
 */
class m210828_170420_user_google_auth_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user_google_auth_token', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(1000)->notNull(),
            'created_at' => $this->integer()->notNull()->defaultValue(time()),
            'updated_at' => $this->integer()->notNull()->defaultValue(time()),
        ], $tableOptions);

         // add foreign key for table `user`
         $this->addForeignKey(
            'fk-googke-token-user',
            'user_google_auth_token',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-googke-token-user', 'user_google_auth_token');
        $this->dropTable('user_google_auth_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210828_170420_user_google_auth_token cannot be reverted.\n";

        return false;
    }
    */
}
