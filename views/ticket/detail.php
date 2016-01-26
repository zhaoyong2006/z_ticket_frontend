<?php

use yii\helpers\Html;

/* @var $ticket_detail*/

$this->title = $ticket_detail['subject'];
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= Html::encode($this->title)?></h1>

<table class="table table-bordered detail-view">
    <tbody>
    <tr>
        <td class="col-lg-2">话题:</td>
        <td class="col-lg-10"><?= $ticket_detail['topic_name']?></td>
    </tr>
    <tr>
        <td>概述：</td>
        <td><?= $ticket_detail['subject']?></td>
    </tr>
    <tr>
        <td>详情：</td>
        <td><?= $ticket_detail['detail']?></td>
    </tr>
    </tbody>
</table>
