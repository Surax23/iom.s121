<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Students;
use app\models\Age;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Назначения';
$this->params['breadcrumbs'][] = $this->title;

//echo '<pre>';
//var_dump($skillsch[3]->skills_id);
//echo '</pre>';
?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php //Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'class',
            'birthday',
            //'age_id',
            [
               'label' => 'Возрастная группа',
               'format' => 'raw',
               //'filter' => Age::find(),
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

                    // echo '<pre>';
                    // print_r($appointments);
                    // echo '</pre>';

                    // echo '<pre>';
                    // print_r(count($appointments));
                    // echo '</pre>';
                   
                        //for ($i=0; $i < count($appointments); $i++) { 
                    foreach ($users as $key => $user) {
                        foreach ($appointments as $key2 => $app) {
                            if ($user->id == $app->user_id)
                                if ($data->id == $app->students_id) {
                                    //print_r(gettype($app->status));
                                    if ($app->status == 0) {
                                        $super = $super.explode(' ', $user->name)[0].': не заполнено.<br />';
                                    } elseif ($app->status == 1) {
                                        $super = $super.explode(' ', $user->name)[0].': в процессе.<br />';
                                    } elseif ($app->status == 2) {
                                        $super = $super.explode(' ', $user->name)[0].': завершено.<br />';
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
               'label' => 'Действие',
               'format' => 'raw',
               'value' => function ($data) {
                    //$flag = false;
                    //foreach ($skillsch as $skill) {
                    //    if ($data->id == $skill->students_id)
                    //       $flag = true;
                    //}
                    //if ($flag)
                // Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['appointment/edit', 'id' => $data->id]));
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['appointment/edit', 'id' => $data->id])).' '
                            .Html::a('<span class="glyphicon glyphicon-print"></span>', Url::to(['/appointment/print', 'id' => $data->id]));
                        //Html::a(Html::encode('Перейти'), Url::to(['appointment/edit', 'id' => $data->id]), ['class'=>'btn btn-primary grid-button', 'type' => 'button']).'<br /><br />'
                        //.Html::a('Печать', Url::to(['/appointment/print', 'id' => $data->id]), ['class'=>'btn btn-primary grid-button', 'target' => '_blank', 'type' => 'button']);
                    //else
                    //    return Html::a(Html::encode('Перейти'), Url::to(['iomtest/create', 'id' => $data->id]));
                },
            ],

            //echo Html::a('Назначение', ['/appointment/print'], ['class'=>'btn btn-primary grid-button']);
        ],
    ]); ?>

    <?php //Pjax::end(); ?>

</div>