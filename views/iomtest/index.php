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

// echo '<pre>';
// var_dump($status);
// echo '</pre>';


?>

<div class="user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="alert alert-info" role="alert">
    <span class="glyphicon glyphicon-pencil"></span> &mdash; ИОМ необходимо заполнить;<br />
    <span class="glyphicon glyphicon-ok"></span> &mdash; ИОМ заполнен, возможно просмотреть.<br />
	Отсутствие значка в графе этапа означает отсутствие назначения вас на этот этап.
    </div>

	<?php Pjax::begin(); ?>

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
			   //'attribute' => 'age_name',
			   'value' => function ($data) {
						return $data->age->name; //Html::a(Html::encode('Перейти'), Url::to(['iomtest/update', 'id' => $data->id]));
				},
			],
			[
				'label' => 'Этап 1',
				'format' => 'raw',
				'value' => function ($data) use ($status) {
					$view = false;
					$zero = false;
					foreach ($status as $key => $s) { // в этой строке $s -- айди статусов, если без скобок(?).
						if (isset($s[1]) && $key == $data->id && $s[1] == 2) {
							$view = true;
						} elseif (!isset($s[1]) && $key == $data->id) {
							$zero = true;
						}
					}
					if (!$zero) {
						if ($view) { //glyphicon glyphicon-search
							return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['iomtest/update', 'id' => $data->id, 'stage' => 1]));
						} else {
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['iomtest/update', 'id' => $data->id, 'stage' => 1]));
						}
					}
					else
						return '';
				},  
			],
			[
				'label' => 'Этап 2',
				'format' => 'raw',
				'value' => function ($data) use ($status) {
					$view = false;
					$zero = false;
					foreach ($status as $key => $s) { // в этой строке $s -- айди статусов, если без скобок(?).
						if (isset($s[2]) && $key == $data->id && $s[2] == 2) {
							$view = true;
						} elseif (!isset($s[2]) && $key == $data->id) {
							$zero = true;
						}
					}
					if (!$zero) {
						if ($view) { //glyphicon glyphicon-search
							return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['iomtest/update', 'id' => $data->id, 'stage' => 2]));
						} else {
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['iomtest/update', 'id' => $data->id, 'stage' => 2]));
						}
					}
					else
						return '';
				}, 
			],
			[
				'label' => 'Этап 3',
				'format' => 'raw',
				'value' => function ($data) use ($status) {
					$view = false;
					$zero = false;
					foreach ($status as $key => $s) { // в этой строке $s -- айди статусов, если без скобок(?).
						if (isset($s[3]) && $key == $data->id && $s[3] == 2) {
							$view = true;
						} elseif (!isset($s[3]) && $key == $data->id) {
							$zero = true;
						}
					}
					if (!$zero) {
						if ($view) { //glyphicon glyphicon-search
							return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['iomtest/update', 'id' => $data->id, 'stage' => 3]));
						} else {
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['iomtest/update', 'id' => $data->id, 'stage' => 3]));
						}
					}
					else
						return '';
				}, 
			],
		],
	]); ?>

	<?php Pjax::end(); ?>

</div>