<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use trntv\filekit\widget\Upload;

/* @var $ticketModel */
/* @var $ticketCdataModel */
/* @var $fileModel */
/* @var $topic_list */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1><?= Html::encode($this->title)?></h1>
    <div>
        <?php $form = ActiveForm::begin();?>



        <?= $form->field($ticketModel, 'topic_id')->dropDownList(
            $topic_list,array('prompt'=>"请选择分类")
        )?>

        <?= $form->field($ticketCdataModel, 'subject')->textInput()?>

        <?= $form->field($ticketCdataModel, 'detail')->textarea()?>

        <?php echo $form->field($fileModel, 'file_index')->widget(
            Upload::className(),
            [
                'url' => ['upload'],
                'sortable' => true,
                'maxFileSize' => 10000000, // 10 MiB
                'maxNumberOfFiles' => 10
            ]);
        ?>
        <div class="form-group">
            <?= Html::submitButton("提交工单", array('class'=>'btn btn-success'))?>
        </div>

        <?php ActiveForm::end();?>
    </div>
</div>