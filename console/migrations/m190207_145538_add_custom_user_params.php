<?php

use yii\db\Migration;

/**
 * Class m190207_145538_add_custom_user_params
 */
class m190207_145538_add_custom_user_params extends Migration {

    public function safeUp() {
        $this->createTable('user_params', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'market_id' => $this->integer()->notNull(),
            'slug' => $this->string()->notNull(),
            'description' => $this->text(),
        ]);

        $this->createTable('user_params_values', [
            'id' => $this->primaryKey(),
            'param_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'value' => $this->string(),
        ]);

        $this->createTable('markets', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'description' => $this->text(),
        ]);
        $this->createIndex('user_params_index', 'user_params_values', ['user_id']);
    }

    public function safeDown() {
        $this->dropTable('user_params');
        $this->dropIndex('user_params_index', 'user_params_values');
        $this->dropTable('user_params_values');
        $this->dropTable('markets');
    }

}
