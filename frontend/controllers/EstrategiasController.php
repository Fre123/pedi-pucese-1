<?php

namespace frontend\controllers;

use Yii;
use app\models\Estrategias;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\Programas;
use app\models\EstrategiasSearch;

/**
 * EstrategiasController implements the CRUD actions for Estrategias model.
 */
class EstrategiasController extends Controller {

    /**
     * @inheritdoc
     */
    public $evidencias_array = Array();

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST', 'GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Estrategias models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EstrategiasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        /* $dataProvider = new ActiveDataProvider([
          'query' => Estrategias::find(),
          ]); */

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Estrategias model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $dataProvider = new ActiveDataProvider([
            'query' => Programas::find()->where(['id_estrategia' => $id]),
        ]);
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Estrategias model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Estrategias();

        if ($model->load(Yii::$app->request->post())) {
             $model->id_objetivo = $_GET['id'];
            // process uploaded image file instance
            $image = $model->uploadDocument();
            $model->evidencias = $model->getDocuments();
            $model->responsables = implode(",", $model->responsables);
            if ($image !== false) {
                $path = $model->getDocumentFile();
                $archivos = (explode(";", $model->evidencias));
                $i = 0;
                foreach ($image as $file) {
                    $ext = end((explode(".", $file->name)));
                    // generate a unique file name
                    $file->saveAs($path . $archivos[$i]);
                    $i++;
                }
            }
            if ($model->save()) {
                print_r($model->id . ' -> ' . $model->evidencias . ' -> ' . strlen($model->evidencias));
                $connection = Yii::$app->db;
                $command = $connection->createCommand("UPDATE estrategias SET evidencias='" . $model->evidencias . "' WHERE id=" . $model->id);
                $command->execute();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->redirect(['index']);
            }
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                        'model' => $model
            ]);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing Estrategia model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            // process uploaded image file instance
            $image = $model->uploadDocument();
            $model->evidencias = $model->getDocuments();
            $model->responsables = implode(",", $model->responsables);
            if ($image !== false) {
                $path = $model->getDocumentFile();
                $archivos = (explode(";", $model->evidencias));
                $i = 0;
                foreach ($image as $file) {
                    $ext = end((explode(".", $file->name)));
                    // generate a unique file name
                    $file->saveAs($path . $archivos[$i]);
                    $i++;
                }
            }
            $model->evidencias = $model->oldAttributes['evidencias'] . $model->evidencias;
            if ($model->save()) {
                //print_r($model->id . ' -> ' . $model->evidencias . ' -> ' . strlen($model->evidencias));
                $connection = Yii::$app->db;
                $command = $connection->createCommand("UPDATE estrategias SET evidencias='" . $model->evidencias . "' WHERE id=" . $model->id);
                $command->execute();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('update', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Estrategia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
          $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteDocument() {
        $model = $this->findModel($_GET['id']);
        if ($_GET['action'] == 'deletefile') {
            $file = Yii::$app->basePath . '/web/' . $_GET['file'];
        }
        $evidencias=str_replace($_GET['fileName'].';','',$model->oldAttributes['evidencias']);
         echo ' -> '.$evidencias;
        $connection = Yii::$app->db;
        $command = $connection->createCommand("UPDATE estrategias SET evidencias='" . $evidencias . "' WHERE id=" . $_GET['id']);
        $command->execute();
        // check if file exists on server
        if (empty($file) || !file_exists($file)) {
            return false;
        }

        // check if uploaded file can be deleted on server
        if (!unlink($file)) {
            return false;
        }
        return true;
    }

    /**
     * Finds the Estrategias model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Estrategias the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Estrategias::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
