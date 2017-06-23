<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170621_025820_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('收货人'),
            'province'=>$this->string(20)->comment('省'),
            'city'=>$this->string(20)->comment('市'),
            'area'=>$this->string(40)->comment('区/县'),
            'detail_address'=>$this->string()->comment('详细地址'),
            'phone'=>$this->char(11)->comment('电话'),
            'status'=>$this->integer(1)->comment('默认'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
