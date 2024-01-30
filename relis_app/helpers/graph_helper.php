<?php
/* ReLiS - A Tool for conducting systematic literature reviews and mapping studies.
 * Copyright (C) 2018  Eugene Syriani
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * --------------------------------------------------------------------------
 *
 *  :Author: Brice Michel Bigendako
 */


//function is used to generate a pie chart using the Highcharts library 
function pie_graph($data)
{
	//print_test($data);


	$categorie = $data['id'];

	//$result_data=$result_table[$categorie]['data'];

	$res_text = "[";
	$i = 1;
	foreach ($data['data'] as $key => $value) {
		if ($i > 1)
			$res_text .= ",";
		$res_text .= "['" . $value['title'] . "'," . $value['nombre'] . "]";
		$i++;
	}

	$res_text .= "]";
	$id_field = $categorie;

	?>
	<div>

		<div id="<?php echo $id_field ?>" style="; margin: 0 auto"></div>
	</div>

	<script type="text/javascript">

		$(function () {
			Highcharts.setOptions({


			});
			$('#<?php echo $id_field; ?>').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: ''
				},
				tooltip: {
					pointFormat: '<b>{point.y:.0f}</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.0f} %',
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
							},
							showInLegend: true
						},
						events: {
							click: function (event, i) {
								var statuss = event.point.name;


							<?php
							if (!empty($data['link'])) {
								foreach ($data['data'] as $key => $value) {
									$name = $value['title'];
									$link = base_url() . "data_extraction/search_classification/" . $data['field'] . "/" . $value['field'];

									?>
									if (statuss == '<?php echo $name ?>') {
												window.location.replace("<?php echo $link ?>");
											}
							
							
							<?php
								}
							}
							?>
					   
						}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Dossiers',
					data:<?php echo $res_text ?>
			}]
		});
	});


	</script>

<?php



}

//function is used to generate a column chart using the Highcharts library
function column_graph($data)
{
	//print_test($data);


	$categorie = $data['id'] . '_col';

	//$result_data=$result_table[$categorie]['data'];

	$res_text = "[";
	$i = 1;
	foreach ($data['data'] as $key => $value) {
		if ($i > 1)
			$res_text .= ",";
		$res_text .= "{name:'" . $value['title'] . "',data:[" . $value['nombre'] . "]}";
		$i++;
	}

	$res_text .= "]";
	$id_field = $categorie;

	//print_test($res_text) ; exit;

	?>
	<div>

		<div id="<?php echo $id_field ?>" style="; margin: 0 auto"></div>
	</div>

	<script type="text/javascript">

		Highcharts.chart('<?php echo $id_field; ?>', {
			chart: {
				type: 'column'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: [
					'<?php echo $data['title'] ?>',

				],
				crosshair: true
			},
			yAxis: {
				min: 0,
				title: {
					text: ' '
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.0f} </b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: <?php echo $res_text ?>
	});


	</script>

<?php
}

//function is used to generate a line graph using the Highcharts library
function line_graph($data)
{
	//	print_test($data);

	//exit;
	$categorie = $data['id'] . '_linesimple';

	//$result_data=$result_table[$categorie]['data'];
	$j = 1;
	$T_CAT = array();
	///Categories
	$res_cat = "[";
	$res_text = "[";
	foreach ($data['data'] as $p_key => $p_value) {
		$T_CAT[$p_key] = array('title' => $p_key, 'value' => 0);
		if ($j > 1) {
			$res_cat .= ",";
			$res_text .= ',';
		}
		$res_cat .= "'" . $p_value['title'] . "'";
		$res_text .= $p_value['nombre'];

		$j++;
	}
	$res_cat .= "]";
	$res_text .= "]";

	$data['categories'] = $res_cat;
	$id_field = $categorie;
	//	print_test($res_text);
	//exit;

	?>
	<div>

		<div id="<?php echo $id_field ?>" style="; margin: 0 auto"></div>
	</div>

	<script type="text/javascript">

		Highcharts.chart('<?php echo $id_field; ?>', {
			chart: {
				type: 'spline'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories:
					<?php echo $res_cat ?>

				,
				crosshair: true
			},
			yAxis: {
				min: 0,
				title: {
					text: ''
				}
			},

			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: false
				}
			},
			series: [{
				name: '<?php echo $data['title'] ?>',
				data: <?php echo $res_text ?>
			}]
		});


	</script>

<?php
}

//function is used to generate a multi-series line graph using the Highcharts library
function line_graph_multi($data)
{
	//	print_test($data);

	//exit;
	$categorie = $data['id'] . '_line';

	//$result_data=$result_table[$categorie]['data'];
	$j = 1;
	$T_CAT = array();
	///Categories
	$res_cat = "[";
	foreach ($data['p_data'] as $p_key => $p_value) {
		$T_CAT[$p_key] = array('title' => $p_key, 'value' => 0);
		if ($j > 1)
			$res_cat .= ",";
		$res_cat .= "'" . $p_value['title'] . "'";


		$j++;
	}
	$res_cat .= "]";

	//print_test($res_cat);
	$T_SER = array();
	foreach ($data['p_data'] as $p_key => $p_value) {

		$i = 1;


		foreach ($p_value['data'] as $key => $value) {
			$T_SER[$key]['title'] = $value['title'];
			if (empty($T_SER[$key]['serie'])) {
				$T_SER[$key]['serie'] = $T_CAT;
			}

			$T_SER[$key]['serie'][$p_key]['value'] = $value['nombre'];

		}



	}


	//print_test($T_CAT);
//	print_test($T_SER);

	//Series
	$i = 1;
	$res_text = "[";
	foreach ($T_SER as $key_ser => $value_ser) {
		if ($i > 1)
			$res_text .= ',';
		$res_text .= "{name: '" . $value_ser['title'] . "',data: [";
		$v = 1;
		//$res_text.='{[';
		foreach ($value_ser['serie'] as $key1 => $value1) {
			if ($v > 1)
				$res_text .= ',';

			$res_text .= $value1['value'];

			$v++;
		}

		$res_text .= "] }";
		$i++;
	}
	$res_text .= "]";
	$data['categories'] = $res_cat;
	$id_field = $categorie;
	//	print_test($res_text);
	//exit;

	?>
	<div>

		<div id="<?php echo $id_field ?>" style="; margin: 0 auto"></div>
	</div>

	<script type="text/javascript">

		Highcharts.chart('<?php echo $id_field; ?>', {
			chart: {
				type: 'spline'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories:
					<?php echo $res_cat ?>

				,
				crosshair: true
			},
			yAxis: {
				min: 0,
				title: {
					text: ''
				}
			},

			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: false
				}
			},
			series: <?php echo $res_text ?>
		});


	</script>

<?php
}

//function is used to generate a multi-series column graph using the Highcharts library
function multi_collumn($data)
{
	//	print_test($data);

	//exit;
	$categorie = $data['id'] . '_multi_collumn';

	//$result_data=$result_table[$categorie]['data'];
	$j = 1;
	$T_CAT = array();
	///Categories
	$res_cat = "[";
	foreach ($data['p_data'] as $p_key => $p_value) {
		$T_CAT[$p_key] = array('title' => $p_key, 'value' => 0);
		if ($j > 1)
			$res_cat .= ",";
		$res_cat .= "'" . $p_value['title'] . "'";


		$j++;
	}
	$res_cat .= "]";

	//print_test($res_cat);
	$T_SER = array();
	foreach ($data['p_data'] as $p_key => $p_value) {

		$i = 1;


		foreach ($p_value['data'] as $key => $value) {
			$T_SER[$key]['title'] = $value['title'];
			if (empty($T_SER[$key]['serie'])) {
				$T_SER[$key]['serie'] = $T_CAT;
			}

			$T_SER[$key]['serie'][$p_key]['value'] = $value['nombre'];

		}



	}


	//print_test($T_CAT);
	//	print_test($T_SER);

	//Series
	$i = 1;
	$res_text = "[";
	foreach ($T_SER as $key_ser => $value_ser) {
		if ($i > 1)
			$res_text .= ',';
		$res_text .= "{name: '" . $value_ser['title'] . "',data: [";
		$v = 1;
		//$res_text.='{[';
		foreach ($value_ser['serie'] as $key1 => $value1) {
			if ($v > 1)
				$res_text .= ',';

			$res_text .= $value1['value'];

			$v++;
		}

		$res_text .= "] }";
		$i++;
	}
	$res_text .= "]";
	$data['categories'] = $res_cat;
	$id_field = $categorie;
	//	print_test($res_text);
	//exit;

	?>
	<div>

		<div id="<?php echo $id_field ?>" style="; margin: 0 auto"></div>
	</div>

	<script type="text/javascript">

		Highcharts.chart('<?php echo $id_field; ?>', {
			chart: {
				type: 'column'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories:
					<?php echo $res_cat ?>

				,
				crosshair: true
			},
			yAxis: {
				min: 0,
				title: {
					text: ' '
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.0f} </b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: <?php echo $res_text ?>
	});
	</script>
<?php
}

//function is used to generate a pie chart with drilldown functionality using the Highcharts library
function pie_drilldown($data)
{
	//		print_test($data);

	//exit;
	$categorie = $data['id'] . '_pie_drilldown';

	//$result_data=$result_table[$categorie]['data'];
	$j = 1;
	///Categories
	$res_cat = "[";
	$res_series = "[";
	foreach ($data['p_data'] as $p_key => $p_value) {
		$cat_number = 0;

		if ($j > 1) {
			$res_series .= ",";
		}

		$res_series .= "{
            name: '" . $p_value['title'] . "',
            id:	'" . $p_value['title'] . "',
			data:[";
		$i = 1;
		foreach ($p_value['data'] as $key => $value) {
			$cat_number += $value['nombre'];
			if ($i > 1) {
				$res_series .= ",";
			}

			$res_series .= "['" . $value['title'] . "'," . $value['nombre'] . "]";

			$i++;
		}
		$res_series .= "]
        }";

		if ($j > 1) {
			$res_cat .= ",";
		}
		$res_cat .= "{
            name: '" . $p_value['title'] . "',
            y:	" . $cat_number . ",
            drilldown:	'" . $p_value['title'] . "'
        	}";


		$j++;

	}
	$res_cat .= "]";
	$res_series .= "]";

	//	print_test($res_cat);
//	print_test($res_series);

	$data['categories'] = $res_cat;
	$id_field = $categorie;
	//	print_test($res_text);
	//exit;

	?>
	<div>

		<div id="<?php echo $id_field ?>" style="; margin: 0 auto"></div>
	</div>

	<script type="text/javascript">

		Highcharts.chart('<?php echo $id_field; ?>', {
			chart: {
				type: 'pie'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: 'Click the slices to view <?php echo $data['values_title']; ?>'
			},
			plotOptions: {
				series: {
					dataLabels: {
						enabled: true,
						format: '{series.name} - {point.name}: {point.y:.0f}'
					}
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> <br/>'
			},

			series: [{
				name: '<?php echo $data['reference_title'] ?>',
				colorByPoint: true,
				data:<?php echo $res_cat ?>
		}],
			drilldown: {
				series: <?php echo $res_series ?>
		}
	});
	</script>
<?php
}