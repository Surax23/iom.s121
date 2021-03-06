<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\models\User;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php $this->registerCsrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
	<?php

	NavBar::begin([
		'brandLabel' => Yii::$app->name,
		'brandUrl' => Yii::$app->homeUrl,
		'options' => [
			'class' => 'navbar-inverse navbar-fixed-top',
		],
	]);

	//echo Html::a('Пользователи', ['/user/index'], ['class'=>'btn btn-primary grid-button']).' ';
	//echo Html::a('Обучающиеся', ['/students/index'], ['class'=>'btn btn-primary grid-button']);

	$items[] = ['label' => 'Главная', 'url' => ['/']];
	$items[] = ['label' => 'Пользователи', 'url' => ['/user/index'], 'visible' => User::isUserAdmin(Yii::$app->user->id)];
	$items[] = ['label' => 'Обучающиеся', 'url' => ['/students/index'], 'visible' => User::isUserAdmin(Yii::$app->user->id)];
	$items[] = ['label' => 'Заполнить', 'url' => ['/iomtest'], 'visible' => !Yii::$app->user->isGuest && !User::isUserAdmin(Yii::$app->user->id)];
	$items[] = Yii::$app->user->isGuest ? (
			   ['label' => 'Войти', 'url' => ['/site/login']]
			) : (
			   '<li>'
			   . Html::beginForm(['/site/logout'], 'post')
			   . Html::submitButton(
				   'Выйти (' . Yii::$app->user->identity->name . ')',
				   ['class' => 'btn btn-link logout']
			   )
			   . Html::endForm()
			   . '</li>'
			);

	echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-right'],
		'items' => $items,
	]);
	NavBar::end();

	?>

	<div class="container">
		<?= Breadcrumbs::widget([
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>
		<?= Alert::widget() ?>
		<?= $content ?>
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; ГБОУ Школа № 121, 1962-<?= date('Y') ?>.</p>

		<p class="pull-right"><?= Yii::powered() ?></p>
	</div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
