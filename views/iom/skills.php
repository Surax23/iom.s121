<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'ИОМ: навыки';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Сохранить', ['save'], ['class' => 'btn btn-success']) ?>
       <?= Html::a('Закончить ввод', ['finish'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php $form = ActiveForm::begin();
    $tmp = 1; ?>


    <?= GridView::widget([
        'dataProvider' => $skills,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'skill_name',
            
            [
            	'label' => 'Значение',
				'format' => 'raw',
				'value' =>function($model) use ($form) {
                    $items = array('0', '1', '2');
                    $param = null;  
			    	return Html::dropDownList('cat', 'null', $items, $param); //Html::textInput('', '');
				},
			],
			
        ],
    ]); ?>

    <?php ActiveForm::end(); ?>
</div>

<?php 
    // Из GridView если что.
    //[
            //    'label' => 'Действие',
            //    'format' => 'url',
            //    'value' => function () { return '<a href="'.Url::home().Url::toRoute(['iom/skills', 'id' => 'id']).'>Перейти</a>'; },
            //],
    //[
            //  'label' => 'Предыдущее значение',
            //  'format' => 'raw',
            //  'value' => '',
            //],
?>