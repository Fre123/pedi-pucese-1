<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\file\FileInput;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\models\Estrategias */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Objetivo ' . $model->id_objetivo, 'url' => ['/objetivos/view', 'id' => $model->id_objetivo]];
$this->params['breadcrumbs'][] = 'Estrategia ' . $this->title;
?>
<div class="estrategias-view">

    <h3>Objetivo: </h3> <p><?= $model->getObjetivo($model->id_objetivo) ?></p>
    <h3>Estrategia: </h3><p><?= $model->descripcion ?></p>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-7">
                <?=
                DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                        'attribute' => 'responsables',
                        'value' => $model->getResponsables(array_map('intval', explode(',', $model->responsables))),
                    ],
                        'fecha_inicio',
                        'fecha_fin',
                        'presupuesto',
                    ],
                ])
                ?>
            </div>
            <div class="col-sm-3">
                <?=
                Html::button('Ver Evidencias', [
                    'class' => 'btn btn-success btn-ajax-modal',
                    'data-target' => '#modal_ver_evidencia',
                ]);
                ?>
            </div>
        </div>
    </div>

    <h3>PROGRAMAS</h3>
    <?=
    Html::button('Crear Programas', [
        'class' => 'btn btn-success btn-ajax-modal',
        'value' => Url::to(['/programas/create', 'id' => $model->id]),
        'id' => 'agregar_programas',
        'data-target' => '#modal_add_programas',
    ]);
    ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //'id',
            ['class' => 'yii\grid\SerialColumn'],
            'descripcion',
            //'responsables',
            //'fecha_inicio',
            //'fecha_fin',
            // 'evidencias',
            // 'presupuesto',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['programas/view', 'id' => $model['id']], [
                                    'title' => Yii::t('yii', 'Ver'),
                        ]);
                    },
                            'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['programas/update', 'id' => $model['id']], [
                                    'title' => Yii::t('yii', 'Modificar'),
                        ]);
                    },
                            'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['programas/delete', 'id' => $model['id']], [
                                    'title' => Yii::t('yii', 'Eliminar'),
                        ]);
                    },
                        ]
                    ],
                ],
            ]);
            ?>
            <?php
            Modal::begin([
                'size' => Modal::SIZE_LARGE,
                'id' => 'modal_ver_evidencia',
                'header' => '<h4>Evidencias</h4>',
            ]);
            echo FileInput::widget([
                'name' => 'evidencias',
                'options' => [
                    'multiple' => true,
                    'showRemove' => false,
                     'layoutTemplates' => [
                    'main2' => '<div class="kv-upload-progress hide"></div>{remove}{cancel}{upload}{preview}',
                ]
                ],
                'pluginOptions' => [
                    'overwriteInitial' => false,
                    'initialPreview' => $model->getEvidencias_preview(),
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => $model->getEvidencias(),
                    'showPreview' => true,
                    'showCaption' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'upload'=>false,
                    'showBrowse' => false,
                    'showremoveClass' => false,
                    'showremoveIcon' => false,
                    'showZoom'=> false,
                ]
            ]);
            Modal::end();
            ?>
            <?php
            Modal::begin([
                'size' => Modal::SIZE_LARGE,
                'id' => 'modal_add_programas',
                'header' => '<h4>Programas</h4>',
            ]);
            echo '<div id="modal-content"></div>';
            Modal::end();
            ?>
            <?php
            $this->registerJs('
        $(\'.modal-lg\').css(\'width\', \'95%\');
        $(\'.btn-ajax-modal\').click(function (){
    var elm = $(this),
        target = elm.attr(\'data-target\'),
        ajax_body = elm.attr(\'value\');

    $(target).modal(\'show\')
        .find(\'.modal-content\')
        .load(ajax_body);
});');
 $this->registerJs('
$(".kv-file-remove").on("click", function() {
   
    $.ajax({
        type: "GET",
        data: {
            action: "deletefile",
            file: $(this).data("url"),
            id:'.$_GET['id'].',
            fileName: $(this).data("key"),
        },
        url: "index.php?r=estrategias/delete-document",
        success: function(msg) {
        $(".kv-fileinput-error").hide();
        alert("Exito");
        self.parent.location.reload(); 
        },
    })
})
    ');
            ?>
</div>