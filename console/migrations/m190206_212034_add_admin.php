<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m190206_212034_add_admin
 */
class m190206_212034_add_admin extends Migration {

    public function safeUp() {
        $this->addColumn('user', 'role', $this->string()->notNull());
        $user = new User;
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->setPassword('admin');
        $user->role = 'admin';
        if (!$user->save()) {
            throw new Exception('Error save ' . print_R($user->getErrors(), 1));
        }
    }

    public function safeDown() {
        $this->delete('user', ['username' => 'admin', 'email' => 'admin@admin.com']);
        $this->dropColumn('user', 'role');
    }

}
