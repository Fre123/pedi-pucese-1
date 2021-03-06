<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\Url;
use Yii;

/**
 * This is the model class for table "subproyectos".
 *
 * @property integer $id
 * @property integer $id_proyecto
 * @property string $nombre
 * @property string $descripcion
 * @property string $evidencias_subproyectos
 * @property string $fecha_inicio
 * @property string $fecha_fin
 *
 * @property Actividades[] $actividades
 * @property Proyectos $idProyecto
 */
class Subproyectos extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public $evidencias;
    public $evidencias_array = Array();

    public static function tableName() {
        return 'subproyectos';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id_proyecto', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin'], 'required'],
            [['id_proyecto'], 'integer'],
            [['fecha_inicio', 'fecha_fin'], 'verifDate'],
            [['nombre'], 'string', 'max' => 200],
            [['descripcion'], 'string', 'max' => 500],
            //[['evidencias'], 'string', 'max' => 300],
            [['id_proyecto'], 'exist', 'skipOnError' => true, 'targetClass' => Proyectos::className(), 'targetAttribute' => ['id_proyecto' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'id_proyecto' => 'Id Proyecto',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'evidencias' => 'Evidencias Subproyectos',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_fin' => 'Fecha Fin',
        ];
    }

    //  -----> CREAR REGLAS DE VALIDACIONES PARA FECHAS    
    public function verifDate($attribute) {
        $time = new \DateTime('now', new \DateTimeZone('America/Guayaquil'));
        $currentDate = $time->format('Y-m-d h:m:s');

        if ($this->$attribute <= $currentDate) {
            $this->addError($attribute, 'No puede ser menor a la fecha actual');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActividades() {
        return $this->hasMany(Actividades::className(), ['id_subproyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProyecto() {
        return $this->hasOne(Proyectos::className(), ['id' => 'id_proyecto']);
    }

    public function getDocumentFile() {
        Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/pdf/subproyectos/';
        return isset($this->evidencias) ? Yii::$app->params['uploadPath'] : null;
    }

    /**
     * fetch stored image url
     * @return string
     */
    public function getDocumentUrl() {
        Yii::$app->params['uploadUrl'] = Yii::$app->urlManager->baseUrl . '/web/pdf/subproyectos/';
// return a default image placeholder if your source avatar is not found
        $evidencias = isset($this->evidencias) ? $this->evidencias : null;
        return Yii::$app->params['uploadUrl'] . $evidencias;
    }

    public function getDocuments() {
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array (you may need to use
        // getInstances method)
        $image = UploadedFile::getInstances($this, 'evidencias');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }
        $i = 0;
        $txtEvidencias = '';
        foreach ($image as $evide):
            $ext = end((explode(".", $evide->name)));
            // generate a unique file name
            $this->evidencias_array[$i] = Yii::$app->security->generateRandomString() . ".{$ext}";
            $txtEvidencias.= $this->evidencias_array[$i] . ';';
            $i++;
        endforeach;

        return $txtEvidencias;
    }

    public function uploadDocument() {
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array (you may need to use
        // getInstances method)
        $image = UploadedFile::getInstances($this, 'evidencias');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }
        return $image;
    }

    /* public function getFechaObjetivo() {
      $modelObjetivo = Objetivos::findOne($_GET['id']);
      return $modelObjetivo;
      }

      public function getObjetivo($id) {
      $modelObjetivo = Objetivos::findOne($id)->descripcion;
      return $modelObjetivo;
      }
     */

    public function getEvidencias_preview() {
        $aux = explode(';', $this->getAttribute('evidencias'));
        $evidencias_preview = Array();
        for ($i = 0; $i < count($aux) - 1; $i++):
            array_push($evidencias_preview, $this->getDocumentFile() . 'pdf/subproyectos/' . $aux[$i]);
        endfor;
        return $evidencias_preview;
    }

    public function getEvidencias() {
        $aux = explode(';', $this->getAttribute('evidencias'));
        $key = $this->id;
        $evidencias = Array();
        $url = Url::to(['subproyectos/view', 'id' => $key]);
        for ($i = 0; $i < count($aux) - 1; $i++):
            array_push($evidencias, [
                'type' => 'pdf',
                'caption' => $aux[$i],
                'url' => 'pdf/subproyectos/' . $aux[$i],
                'key' => $aux[$i]
                    //'key' => $i + 1
            ]);
        endfor;
        return $evidencias;
    }
    
    //Para los Fixtures
    public function saveSubproyecto() {
        if (!$this->validate()) {
            return null;
        }
        $subproyecto = new Subproyectos();
        $subproyecto->id_proyecto = $this->id_proyecto;
        $subproyecto->nombre = $this->nombre;
        $subproyecto->descripcion = $this->descripcion;
        $subproyecto->evidencias = $this->evidencias;
        $subproyecto->fecha_inicio = $this->fecha_inicio;
        $subproyecto->fecha_fin = $this->fecha_fin;
        return $subproyecto->save() ? $subproyecto : null;
    }

}
