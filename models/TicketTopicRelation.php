<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_ticket_topic_relation".
 *
 * @property integer $id
 * @property integer $ticket_id
 * @property integer $topic_id
 */
class TicketTopicRelation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'z_ticket_topic_relation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'topic_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'topic_id' => Yii::t('app', 'Topic ID'),
        ];
    }

    public function getTopic()
    {
        return $this->hasOne(TicketTopic::className(), array('topic_id' => 'topic_id'));
    }
}
