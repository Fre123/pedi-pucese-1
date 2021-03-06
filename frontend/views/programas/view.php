<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Programas */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Programas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programas-view">

    <h1><?= Html::encode($this->title) ?></h1>

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

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'descripcion',
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
    <h3>PROYECTOS</h3>
    <?=
    Html::button('Crear Proyectos', [
        'class' => 'btn btn-success btn-ajax-modal',
        'value' => Url::to(['/proyectos/create', 'id' => $model->id]),
        'id' => 'agregar_proyectos',
        'enableAjaxValidation' => true,
        'data-target' => '#modal_add_proyectos',
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
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['proyectos/view', 'id' => $model['id']], [
                                    'title' => Yii::t('yii', 'Ver'),
                        ]);
                    },
                            'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['proyectos/update', 'id' => $model['id']], [
                                    'title' => Yii::t('yii', 'Modificar'),
                        ]);
                    },
                            'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['proyectos/delete', 'id' => $model['id']], [
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
                'id' => 'modal_add_proyectos',
                'header' => '<h4>Proyectos</h4>',
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
});
    ');
            ?>
</div>
