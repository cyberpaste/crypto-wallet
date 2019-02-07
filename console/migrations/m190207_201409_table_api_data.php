<?php

use yii\db\Migration;

/**
 * Class m190207_201409_table_api_data
 */
class m190207_201409_table_api_data extends Migration {

    public function safeUp() {
        $this->createTable('api_requests', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'market_id' => $this->integer()->notNull(),
            'data' => $this->text(),
            'updated' => $this->timestamp(),
        ]);
        $this->createIndex('api_requests_index', 'api_requests', ['user_id', 'market_id']);
    }

    public function safeDown() {
        $this->dropTable('api_requests');
    }

}
