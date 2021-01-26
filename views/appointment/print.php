<?php

$resources = [];
$deficit = [];
$sk_ready_id = [];

for ($i=0; $i < count($data['skills_ch']); $i++) { 
	if (!in_array($data['skills_ch'][$i]->skills_id, $sk_ready_id)) {
		$sum = 0;
		$sum = $data['skills_ch'][$i]->value;
		$k = 1;
		for ($j=$i+1; $j < count($data['skills_ch'])-1; $j++) { 

			if ($data['skills_ch'][$i]->skills_id == $data['skills_ch'][$j]->skills_id) {
				// if ($data['skills_ch'][$j]->skills_id == 2199)
				// echo 'Дегенерат: '.$sum;
				$sum = $sum + $data['skills_ch'][$j]->value;
				$k++;
			}
		}
		$sk_ready_id[] = $data['skills_ch'][$i]->skills_id;
		if ($k != 0)
			if ($sum/$k > 1.5)
				$resources[] = ['id' => $data['skills_ch'][$i]->skills_id, 'value' => $sum/$k];
			else
				$deficit[] = ['id' => $data['skills_ch'][$i]->skills_id, 'value' => $sum/$k];
	}
}

// echo '<pre>';
// var_dump($data['skills_ch']);
// echo '</pre>';
?>




<style>
p {
	margin: 0 0;
}
</style>

<h3 align="center">Индивидуальный образовательный маршрут<br />
на обучающую(его)ся:  <?= $data['student']->name; ?>, 
<?php  
	$date = new DateTime($data['student']->birthday);
echo $date->format('d.m.Y');
?>
 г.р.,<br />
<?= $data['student']->class; ?> класса с ОВЗ на 2020-2021 учебный год</h3>

<p><strong>Ресурсы ребенка (хорошо сформированные навыки):</strong></p>
<!-- <p>навыки со средним ">1.5"</p> -->
<p><?php 
	foreach ($resources as $key => $res) {
		foreach ($data['skills'] as $skill) {
			if ($res['id'] == $skill->id)
				echo $skill->skill_name;
		}
		echo '<br />'; //': '.round($res['value'], 2).'<br />';
	}
?></p>
<p>&nbsp;</p>
<p><strong>Дефициты ребёнка (навык отсутствует или находится в стадии формирования):</strong></p>
<!-- <p>навыки со средним "1.5 >= x > 0"</p> -->
<p><?php 
	foreach ($deficit as $key => $def) {
		foreach ($data['skills'] as $skill) {
			if ($def['id'] == $skill->id)
				echo $skill->skill_name;
		}
		echo '<br />';
		//echo $def['id'].' ;; '.round($def['value'], 2).'<br />';
	}
?></p>
<p>&nbsp;</p>
<p><strong>Цель:</strong><br />
сохранение, улучшение и корректировка психологического, социального и  физического   здоровья, сопровождение психофизического развития школьника, психолого-педагогическое обеспечение дифференцированного и индивидуального подхода к обучающемуся.</p>
<p>&nbsp;</p>
<p><strong>Задачи развития ребёнка на учебный год:</strong></p>
<p><?php 
$dev_dirs = [];
	foreach ($deficit as $key => $def) {
		foreach ($data['skills'] as $skill) {
			if ($def['id'] == $skill->id)
				foreach ($data['dev_dir_id'] as $dev) {
					if ($skill->dev_dir_id == $dev->id && !in_array($dev, $dev_dirs)) {
						echo $dev->task.'<br />';
						$dev_dirs[] = $dev;
					}
				}
		}
		
		//echo $def['id'].' ;; '.round($def['value'], 2).'<br />';
	}
?></p>
<p>&nbsp;</p>
<p><strong>Рекомендации педагогическим работникам и родителям:</strong></p>
<p><?php 
$recomendation = [];
	foreach ($deficit as $key => $def) {
		foreach ($data['skills'] as $skill) {
			if ($def['id'] == $skill->id && !in_array($skill->recomendation, $recomendation))
				{
					echo $skill->recomendation.'<br />';
					$recomendation[] = $skill->recomendation;
				}
		}
		
		//echo $def['id'].' ;; '.round($def['value'], 2).'<br />';
	}
?></p>
<p>&nbsp;</p>

<table width="100%">
<tr><td width="50%" valign="bottom">
	<p>Дата составления: _____</p>
</td>

<td width="50%">
<p align="right">Председатель ППК ГБОУ Школа №121 ________ Е.А.Ефремова<br />
<Педагог-психолог><br />
<Учитель-логопед><br />
<Учитель-дефектолог><br />
<Социальный педагог><br />
<Учитель><br />
<Воспитатель></p>
</td></tr></table>