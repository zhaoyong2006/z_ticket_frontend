<?php

namespace frontend\controllers;

use Aws\CloudFront\Exception\Exception;
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
            'upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'deleteRoute' => 'upload-delete'
            ],
//            'upload' => [
//                'class' => UploadAction::className(),
//                'deleteRoute' => 'avatar-delete',
//                'on afterSave' => function ($event) {
//                        /* @var $file \League\Flysystem\File */
//                        $file = $event->file;
//                        $img = ImageManagerStatic::make($file->read())->fit(215, 215);
//                        $file->put($img->encode());
//                    }
//            ],
//            'avatar-delete' => [
//                'class' => DeleteAction::className()
//            ]
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

        if(Yii::$app->request->post()){

            $ticketForm = Yii::$app->request->post('Ticket');
            $ticketCdataForm = Yii::$app->request->post('TicketCdata');
            $fileForm = Yii::$app->request->post('File');

            $ticketModel->number = $ticketModel->newTicketNumber();
            $ticketModel->user_id = Yii::$app->user->identity->id;
            $ticketModel->status_id = 1;
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

            //事务
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try{
                if(!$ticketModel->save()){
                    throw new Exception("数据存储失败，请检查数据库配置(error:1001)");
                }
                $ticketCdataModel->ticket_id = $ticketModel->ticket_id;
                if(!$ticketCdataModel->save()){
                    throw new Exception("数据存储失败，请检查数据库配置(error:1002)");
                }
                if(!empty($fileForm['file_index']) && is_array($fileForm['file_index'])){
                    foreach($fileForm['file_index'] as $k=>$v){
                        $fileModel = new File();
                        $fileModel->attribute = 'tickets';
                        $fileModel->attribute_id = $ticketModel->ticket_id;
                        $fileModel->file_name = $v['name'];
                        $fileModel->file_index = $v['path'];
                        $fileModel->size = $v['size'];
                        $fileModel->type = $v['type'];
                        if(!$fileModel->save()){
                            throw new Exception("数据存储失败，请检查数据库配置(error:1003)");
                        }
                    }
                }
                $transaction->commit();
            }catch (Exception $e){
                $transaction->rollBack();
                return $this->redirect(array('add_ticket'));
            }

            return $this->redirect(array('detail', 'number'=>$ticketModel->number));


        }else{
            $topicModel = new TicketTopic();
            $topic_list = $topicModel->find()->select(array('topic_name','topic_id'))->indexBy('topic_id')->column();
            $fileModel = new File();

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

        $ticket_detail = Ticket::find()
            ->joinWith('cdata')
            ->joinWith('topic')
            ->joinWith('status')
            ->where(array('number' => $number));

        $attachments = $ticket_detail->one()->file;
        return $this->render('detail',array(
            'ticket_detail' => $ticket_detail->asArray()->one(),
            'attachments' => $attachments,
        ));
    }

    /**
     * @param $id
     * @return $this
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAttachmentDownload($id)
    {
        $model = File::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException;
        }

        return \Yii::$app->response->sendStreamAsFile(
            \Yii::$app->fileStorage->getFilesystem()->readStream($model->file_index),
            $model->file_name
        );
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
