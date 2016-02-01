<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\TicketStatus;


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
            'number',
            'cdata.subject',
            'cdata.detail',
            [
                'attribute' => 'status_id',
                'value' => function($model){
                        $status_map = TicketStatus::$status_map;
                        return isset($status_map[$model->status_id]) ? $status_map[$model->status_id]['name'] : '';
                    }
            ],
            'created:datetime:提交时间',
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
