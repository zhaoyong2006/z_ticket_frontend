<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Ticket;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use frontend\models\TicketCdata;
use frontend\models\TicketTopic;
use yii\web\BadRequestHttpException;
use yii\db\Query;
use frontend\models\File;
use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'avatar-upload' => [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                        /* @var $file \League\Flysystem\File */
                        $file = $event->file;
                        $img = ImageManagerStatic::make($file->read())->fit(215, 215);
                        $file->put($img->encode());
                    }
            ],
            'avatar-delete' => [
                'class' => DeleteAction::className()
            ]
        ];
    }

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Ticket::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ticket_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 创建工单
     * @return string|\yii\web\Response
     */
    public function actionAdd_ticket()
    {
        $ticketModel = new Ticket();
        $ticketCdataModel = new TicketCdata();
        $fileModel = new File();

        if(Yii::$app->request->post()){

//            echo "<pre>";
//            print_r(Yii::$app->request->post('File'));exit;
            $ticketForm = Yii::$app->request->post('Ticket');
            $ticketCdataForm = Yii::$app->request->post('TicketCdata');

            $ticketModel->number = $ticketModel->newTicketNumber();
            $ticketModel->user_id = Yii::$app->user->identity->id;
            $ticketModel->status_id = 0;
            $ticketModel->type_id = Ticket::FEEDBACK_TICKET_TYPE;
            $ticketModel->topic_id = $ticketForm['topic_id'];
            $ticketModel->staff_id = 0;
            $ticketModel->team_id = 0;
            $ticketModel->source_id = 1;
            $ticketModel->ip_address = Yii::$app->request->getUserIP();
            $ticketModel->created = date("Y-m-d H:i:s");
            $ticketModel->updated = date("Y-m-d H:i:s");

            $ticketCdataModel->subject = $ticketCdataForm['subject'];
            $ticketCdataModel->detail = $ticketCdataForm['detail'];
            $ticketCdataModel->priority = 1;
            if($ticketModel->save()){
                $ticketCdataModel->ticket_id = $ticketModel->ticket_id;
                if($ticketCdataModel->save()){
                    return $this->redirect(array('detail', 'number'=>$ticketModel->number));
                }
            }
            return $this->redirect(array('add_ticket'));


        }else{
            $topicModel = new TicketTopic();
            $topic_list = $topicModel->find()->select(array('topic_name','topic_id'))->indexBy('topic_id')->column();

            return $this->render('add_ticket',array(
                'ticketModel' => $ticketModel,
                'ticketCdataModel' => $ticketCdataModel,
                'fileModel' => $fileModel,
                'topic_list' => $topic_list
            ));
        }
    }

    /**
     * 工单查看
     * @param $number
     * @return string
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDetail($number){

        $ticket_detail = Ticket::find()->where(array('number'=>$number,'user_id'=>Yii::$app->user->identity->id))->asArray()->one();
        if(empty($ticket_detail)){
           throw new BadRequestHttpException("该工单号不存在");
        }

        $topic_detail = TicketTopic::findOne(array('topic_id'=>$ticket_detail['topic_id']));

        $ticket_detail['topic_name'] = $topic_detail->topic_name;

        $cdata_detail = TicketCdata::findOne(array('ticket_id'=>$ticket_detail['ticket_id']));
        $ticket_detail['subject'] = $cdata_detail->subject;
        $ticket_detail['detail'] = $cdata_detail->detail;

        return $this->render('detail',array(
            'ticket_detail' => $ticket_detail,
        ));
    }

    public function actionList(){
        $dataProvider = new ActiveDataProvider([
            'query' => Ticket::find()
                    ->joinWith('cdata')
                    ->joinWith('topic')
                    ->joinWith('status')
                    ->where(array('user_id' => Yii::$app->user->identity->id))
//                    ->asArray()
//                    ->all(),
        ]);
        return $this->render('list', [
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ticket_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
