<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_ticket__cdata".
 *
 * @property integer $ticket_id
 * @property string $subject
 * @property string $detail
 * @property integer $priority
 */
class TicketCdata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'z_ticket__cdata';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id'], 'required'],
            [['ticket_id', 'priority'], 'integer'],
            [['subject', 'detail'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_id' => '工单ID',
            'subject' => '概述',
            'detail' => '详情',
            'priority' => 'Priority',
        ];
    }
}
