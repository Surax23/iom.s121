<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Button; 

$this->title = 'Назначения: ' . $data['student']->name;
$this->params['breadcrumbs'][] = ['label' => 'Обучающиеся', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $data['student']->name, 'url' => ['view', 'id' => $data['student']->id]];
$this->params['breadcrumbs'][] = 'Назначения';

// echo '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
// echo '<pre>';
// var_dump($scbt);
// echo '</pre>';
?>

<div class="students-edit">

    <h1><?= Html::encode($this->title) ?></h1>

<!-- <div class="container col-xs-12 col-md-8"> -->

<?php
$form = ActiveForm::begin([
	'id' => 'update',
	'options' => ['class' => 'form-horizontal'],
]); 

$k = 1;
echo '<h2>Этап '.$k.'</h2>';
foreach ($scbt as $s) {
	if ($k != $s->stage) {
		$k = $s->stage;
		echo '<h2>Этап '.$k.'</h2>';
	}
	unset($teachers);
	$teachers = [];
	$group_name = '';
	foreach ($data['groups'] as $group) {
		if ($s->group_id == $group->id)
			$group_name = $group->name;
	}
	foreach ($data['teachers'] as $teacher) {
		if ($s->group_id == $teacher->groups) {
			$teachers += [$teacher->id => $teacher->name];
		}
	}

	$params = ['prompt' => 'Выберите значение'];
	echo $form->field($s, "[$s->id]user_id")->dropDownList($teachers,$params)->label($group_name);
}


?>

 <div class="form-group">
		<div class="col-lg-offset-0 col-lg-11">
			<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
		</div>
	</div>


 <?php ActiveForm::end();
?>

</div>