<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $dataProvider */

$this->title = 'Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Ticket', ['add_ticket'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'ticket_id',
            'number',
//            'user_id',
//            'status_id',
//            'type_id',
//            'topic_id',
            'cdata.subject',
            'status.name:text:当前状态',
            //'staff_id',
            // 'team_id',
            // 'ip_address',
            // 'source_id',
            'topic.topic_name',
             'created:datetime:提交时间',
            // 'updated',
            [
                'class' => 'yii\grid\ActionColumn',
                'content' => '你好',
                'buttons' =>[
                    'view' => function ($url, $model, $key){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',Url::to(['ticket/detail','number'=>$model->number]));
                        }
                ],
                'template'=> '{view}'
            ],
        ],
    ]); ?>

</div>
