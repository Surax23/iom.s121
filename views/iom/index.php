<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Students;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'ИОМ';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>
        //Html::a('Создать', ['create'], ['class' => 'btn btn-success']) 
       
    </p> -->

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            //'auth_key',
            //'password_reset_token',
            'class',
            'age_id',
            //'password_hash',
            //'groups',

            //['class' => 'yii\grid\ActionColumn'],
            [
		       'label' => 'Действие',
		       'format' => 'raw',
		       'value' => function ($data) {
		            return Html::a(Html::encode('Перейти'), Url::to(['iom/fform', 'id' => $data->id]));
		        },
		    ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>