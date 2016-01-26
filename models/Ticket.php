<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_ticket".
 *
 * @property integer $ticket_id
 * @property string $number
 * @property integer $user_id
 * @property integer $status_id
 * @property integer $type_id
 * @property integer $topic_id
 * @property integer $staff_id
 * @property integer $team_id
 * @property string $ip_address
 * @property integer $source_id
 * @property string $created
 * @property string $updated
 */
class Ticket extends \yii\db\ActiveRecord
{
    const FEEDBACK_TICKET_TYPE = 1000;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'z_ticket';
    }

    public static $ticketType = array(
        1000 => array(
            'name' => "意见反馈工单"
        )
    );

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status_id', 'type_id', 'topic_id', 'staff_id', 'team_id', 'source_id'], 'integer'],
            [['created', 'updated'], 'required'],
            [['created', 'updated'], 'safe'],
            [['number'], 'string', 'max' => 20],
            [['ip_address'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_id' => 'ID',
            'number' => '工单号',
            'user_id' => '用户ID',
            'status_id' => '状态ID',
            'type_id' => '类型ID',
            'topic_id' => '话题ID',
            'staff_id' => '员工股ID',
            'team_id' => '部门ID',
            'ip_address' => 'Ip地址',
            'source_id' => '来源ID',
            'created' => '创建时间',
            'updated' => '更新时间',
        ];
    }


    /**
     * 获取新工单号
     * @return int
     */
    public function newTicketNumber()
    {
        $randNumber = self::randNumber(8);
        $findResult = self::findAll(array('number' => $randNumber));
        if (!empty($findResult)) {
            self::newTicketNumber();
        }
        return (string)$randNumber;

    }

    /* Helper used to generate ticket IDs */
    protected function randNumber($len = 6, $start = false, $end = false)
    {

        $start = (!$len && $start) ? $start : str_pad(1, $len, "0", STR_PAD_RIGHT);
        $end = (!$len && $end) ? $end : str_pad(9, $len, "9", STR_PAD_RIGHT);

        return mt_rand($start, $end);
    }

    public function getCdata()
    {
        return $this->hasOne(TicketCdata::className(), array('ticket_id' => 'ticket_id'));
    }

    public function getTopic()
    {
        return $this->hasOne(TicketTopic::className(), array('topic_id' => 'topic_id'));
    }

    public function getStatus()
    {
        return $this->hasOne(TicketStatus::className(), array('status_id' => 'status_id'));
    }

    public function getFile()
    {
        //TODO 修改表结构 不允许 attribute = tickets 情形
        return $this->hasMany(File::className(), array('attribute_id'=>'ticket_id'));
    }
}
