<?php

use yii\helpers\Html;

use yii\bootstrap\Button;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SkillsChecked */
/* @var $form ActiveForm */
?>
<div class="iom-_fform">

    <?= Html::a('Сохранить', ['/iom/fform'], ['class'=>'btn btn-primary']) ?>
    <?= '&nbsp;'; ?>
    <?= Html::a('Завершить ввод', ['/iom/fform'], ['class'=>'btn btn-success']) ?>

    <?php $form = ActiveForm::begin(); ?>
    	
    	<?php
    		$i = 0;
    		foreach ($model as $index => $m) {
    			//echo $form->field($m, "[$index]value")->label($skills[$i]->skill_name);
    			$items = array('0', '1', '2');
			    $params = ['prompt' => 'Выберите значение'];
			    echo $form->field($m, "[$index]value")->dropDownList($items,$params)->label($skills[$i]->skill_name);
    			$i++;
    		}
    	?>
    
        <div class="form-group">
            <?php // Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>


    <?= Html::a('Сохранить', ['/iom/fform', 'id'=>$student_id], ['class'=>'btn btn-primary']) ?>
    <?= '&nbsp;'; ?>
    <?= Html::a('Завершить ввод', ['/iom/fform'], ['class'=>'btn btn-success']) ?>

</div><!-- iom-_fform -->
