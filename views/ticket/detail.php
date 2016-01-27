<?php

use yii\helpers\Html;

/* @var $ticket_detail*/
/* @var $attachment*/
/* @var $topics*/

$this->title = $ticket_detail['cdata']['subject'];
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="content">
    <article>
        <h3><?= Html::encode($this->title)?></h3>
        <?php foreach($topics as $topic): ?>
        <span class="label label-info"><?= $topic['topic']['topic_name']?></span>
        <?php endforeach;?>
        <br/><br/>
        概述:
        <p><?= $ticket_detail['cdata']['subject']?></p>
        详情:
        <p><?= $ticket_detail['cdata']['detail']?></p>

        <?php if (!empty($attachments)): ?>
            <h3><?php echo Yii::t('frontend', 'Attachments') ?></h3>
            <ul id="article-attachments">
                <?php foreach ($attachments as $attachment): ?>
                    <li>
                        <?php echo \yii\helpers\Html::a(
                            $attachment['file_name'],
                            ['attachment-download', 'id' => $attachment['attach_id']])
                        ?>
                        (<?php echo Yii::$app->formatter->asSize($attachment['size']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</div>




