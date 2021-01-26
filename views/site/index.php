<?php
use yii\helpers\Html;
use app\models\User;
/* @var $this yii\web\View */

$this->title = 'ИОМ. ГБОУ Школа № 121';

// if (User::isUserAdmin(Yii::$app->user->id)) {
// 	echo '<div class="panel panel-info"><div class="panel-heading">Для администраторов:</div><div class="panel-body">';
// 	echo Html::a('Пользователи', ['/user/index'], ['class'=>'btn btn-primary grid-button']).' ';
// 	echo Html::a('Обучающиеся', ['/students/index'], ['class'=>'btn btn-primary grid-button']);
// 	echo '</div></div>';
// }

//['label' => 'Пользователи', 'url' => ['/user/index'], 'visible' => !Yii::$app->user->isGuest],
//['label' => 'Ученики (список)', 'url' => ['/students/index'], 'visible' => $isAdmin],
?>
<div class="site-index">

    <div class="body-content">
		<div class="jumbotron">
			<h2>Индивидуальный образовательный маршрут</h2>

			
			<?php
			if (!(User::isUserAdmin(Yii::$app->user->id))) {
				echo '<p class="lead">Перейти к заполнению форм.</p><p>';
				if (Yii::$app->user->isGuest) {
					echo Html::a('Войти', ['/site/login'], ['class'=>'btn btn-primary grid-button']); } 
				else {
					echo Html::a('Заполнить', ['/iomtest'], ['class'=>'btn btn-primary grid-button']);}
				//echo '<a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Войти</a>';
			}
			?>
			
			</p>
		</div>
	
        <div class="row">
            <div class="col-lg-6">
                <h2>Здоровье</h2>

                <p>Сохранение, улучшение и коррекция психологического, социального и  физического   здоровья, сопровождение психофизического развития, психолого-педагогическое обеспечение дифференцированного и индивидуального подхода к обучающемуся с учётом его возможностей и образовательных потребностей.</p>

                <!-- <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a> </p>-->
            </div>
            <div class="col-lg-6">
                <h2>Образование</h2>

                <p>Индивидуализация образовательного процесса обучающегося с учетом его возможностей и особых образовательных потребностей для оптимального психофизического развития и социальной адаптации.</p>

                <!-- <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p> -->
            </div>
        </div>

    </div>
</div>
