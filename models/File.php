<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "z_file".
 *
 * @property integer $attach_id
 * @property string $attribute
 * @property integer $attribute_id
 * @property string $file_name
 * @property string $file_index
 * @property string $created
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'z_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'integer'],
            [['created'], 'safe'],
            [['attribute'], 'string', 'max' => 16],
            [['file_name', 'file_index'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'attach_id' => Yii::t('app', 'Attach ID'),
            'attribute' => Yii::t('app', 'Attribute'),
            'attribute_id' => Yii::t('app', 'Attribute ID'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_index' => Yii::t('app', 'File Index'),
            'created' => Yii::t('app', 'Created'),
        ];
    }
}
