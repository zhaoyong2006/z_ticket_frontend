<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_ticket_topic".
 *
 * @property integer $topic_id
 * @property string $topic_name
 * @property string $topic_signature
 * @property string $updated
 * @property string $created
 */
class TicketTopic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'z_ticket_topic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic_signature', 'updated', 'created'], 'required'],
            [['topic_signature'], 'string'],
            [['updated', 'created'], 'safe'],
            [['topic_name'], 'string', 'max' => 128],
            [['topic_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'topic_id' => 'Topic ID',
            'topic_name' => '话题名称',
            'topic_signature' => 'Topic Signature',
            'updated' => 'Updated',
            'created' => 'Created',
        ];
    }
}
