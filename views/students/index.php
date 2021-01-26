<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StudentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// echo '<pre>';
// var_dump($searchModel);
// echo '</pre>';

$this->title = 'Обучающиеся';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="students-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\ActionColumn','header'=>"Действия"],
            //'id',
            'name',
            'class',
            'birthday',
            [
               'label' => 'Возрастная группа',
               'format' => 'raw',
               'attribute' => 'name',
               //'attribute' => 'age_name',
               'value' => function ($data) {
                        return $data->age->name; //Html::a(Html::encode('Перейти'), Url::to(['iomtest/update', 'id' => $data->id]));
                },
            ],

            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function ($data) use ($appointments, $users) {
                    $super = '';
                    foreach ($users as $key => $user) {
                        foreach ($appointments as $key2 => $app) {
                            if ($user->id == $app->user_id)
                                if ($data->id == $app->students_id) {
                                    //print_r(gettype($app->status));
                                    // echo '<pre>';
                                    // var_dump($app);
                                    // echo '</pre>';
                                    $family = $super.explode(' ', $user->name)[0];
                                    if ($app->status == 0) {
                                        $super = $family.': '.$app->stage.' <span class="glyphicon glyphicon-remove"></span><br />';
                                    } elseif ($app->status == 1) {
                                        $super = $family.': '.$app->stage.' <span class="glyphicon glyphicon-pencil"></span><br />';
                                    } elseif ($app->status == 2) {
                                        $super = $family.': '.$app->stage.' <span class="glyphicon glyphicon-ok"></span><br />';
                                    }
                                }
                        }
                    }
                    if ($super == '')
                        return '&mdash;';
                    else
                        return $super;
                }
            ],

            [
                //
                'label' => 'Назначения',
                'format' => 'raw', //glyphicon-remove-sign
                'value' => function ($data) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['students/edit',
                                'id' => $data->id])).' '
                        //.Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['students/print', 'id' => $data->id]), ['target' => '_blank']).' '
                        .Html::a('<span class="glyphicon glyphicon-ban-circle"></span>', Url::to(['students/clear',
                        'id' => $data->id]), ['class' => '', 'data' => ['confirm' => 'Вы действительно хотите
                        очистить все назначения? С назначениеями удалятся уже проставленные навыки. Вернуть все будет невозможно.']]);//Url::to(['students/clear', 'id' => $data->id], ['class' => '', 'data' => ['confirm' => 'Вы действительно хотите очистить все назначения? Вернуться к изменениям будет нельзя.']]));
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
