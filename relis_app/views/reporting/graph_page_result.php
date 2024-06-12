
<?php 
$categorie=$this->session->userdata('graph_categorie_show');

$result_data=$result_table[$categorie]['rows'];

$res_text="[";
$i=1;
foreach ($result_data as $key => $value) {
	if($i>1)
		$res_text.=",";
	$res_text.="['".$value['field_desc']."',".$value['nombre']."]";
	$i++;
}

$res_text.="]";
$id_field=Slug($categorie);


?>
<div>


<div id="<?php echo $id_field?>" style="; margin: 0 auto"></div>
</div>

<script type="text/javascript">
		
$(function () {
	Highcharts.setOptions({
	    
	    
	    });
    $('#<?php echo $id_field;?>').highcharts({
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
                events:{
                    click: function (event, i) {
                    	var statuss=event.point.name;


                    	<?php 
                    	foreach ($result_data as $key => $value) {
                    		$name=$value['field_desc'];
                    		$link=base_url()."data_extraction/search_classification/".$result_table[$categorie]['field_name']."/".$value['field'];
                    		
                    		?>
                    		if(statuss == '<?php echo $name ?>'){
                        		window.location.replace("<?php echo $link ?>");
                            }
                    		
                    		
                    	<?php 	
                    	}
                    	
                    	?>
                       
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Dossiers',
            data:<?php echo $res_text?>
        }]
    });
});


		</script>



