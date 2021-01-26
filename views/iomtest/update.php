<?php
use yii\helpers\Html;

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Button; 

$this->title = 'ИОМ: '.$student->name;
$this->params['breadcrumbs'][] = ['label' => 'ИОМ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//echo $models[25]->user->name;
// echo '<pre>';
// print_r($app_data);
// echo '</pre>';
?>

<div class="container col-xs-12 col-md-8">

<?php
$form = ActiveForm::begin([
	'id' => 'update',
	//'action' => ['iomtest/update', 'id' => $id],
	'options' => ['class' => 'form-horizontal'],
]); 

//$view_only = true;

if (!$view_only) {
	echo '<div class="form-group">
		<div class="col-lg-offset-0 col-lg-11">';
	echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']).'&nbsp;';
	echo Html::a(Html::encode('Завершить'), Url::to(['iomtest/finish', 'id' => $id, 'stage' => $stage]), ['class' => 'btn btn-success', 'data' => ['confirm' => 'Вы действительно хотите завершить заполнение? Вернуться к изменениям будет нельзя.']]);
	echo '</div>
	</div>';
}


$hands_sk_id = []; //[1115, 1345, 1611, 1837, 2096, 2329];
$tempo_sk_id = []; //[1320, 1584, 1812, 2064, 2297, 2531, 1318, 1582, 1810, 2062, 2295, 2529];

$params = ['prompt' => 'Выберите значение', 'disabled' => ($view_only) ? 'disabled' : false];
$mycat = -1;
foreach ($models as $index=>$md) {
	if ($md->skills_id != null)
		{
			if (in_array($md->skills_id, $hands_sk_id)) {
				$vars = [
					0 => 'Леворукость',
					1 => 'Праворукость',
					2 => 'Амбидекстр'];
				
			} elseif (in_array($md->skills_id, $tempo_sk_id)) {
				$vars = [
					0 => 'Низкий',
					1 => 'Средний',
					2 => 'Высокий'];
			} else {
				$vars = [
					0 => 'Не сформирован',
					1 => 'Находится в стадии формирования',
					2 => 'Сформирован'];
			}

			if ($mycat != $md->skills->dev_dir_id)
				foreach ($category as $cat) {
					if ($cat->id == $md->skills->dev_dir_id)
						{
							$mycat = $cat->id;
							echo '<h3 style="margin: 10px 0 -5px -20px;">Направление развития: '.$cat->name.'</h3>';
						}
				}
			echo $form->field($md, "[$index]value")->dropDownList($vars,$params)->label($md->skills->skill_name);
		}
} ?>

<?php 

if (!$view_only) {
	echo '<div class="form-group">
		<div class="col-lg-offset-0 col-lg-11">';
	echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']).'&nbsp;';
	echo Html::a(Html::encode('Завершить'), Url::to(['iomtest/finish', 'id' => $id, 'stage' => $stage]), ['class' => 'btn btn-success', 'data' => ['confirm' => 'Вы действительно хотите завершить заполнение? Вернуться к изменениям будет нельзя.']]);
	echo '</div>
	</div>';
}

ActiveForm::end();
?>
</div>