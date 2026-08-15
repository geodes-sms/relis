<!-- Select2 -->
<script src="<?php echo site_url();?>cside/vendors/select2/dist/js/select2.full.min.js"></script>
<script src="https://d3js.org/d3.v6.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dagre-d3/0.6.4/dagre-d3.min.js"></script>
<script src="<?php echo site_url();?>cside/js/phases.js"></script>
<link rel="stylesheet" href="<?php echo site_url();?>cside/css/phases_tree.css">
<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">

        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" >
                    <div class="x_title">
                        <?php  //header_perspective('screen');?>
                        <h2><?php echo isset($page_title) ? $page_title :"" ; ?></h2>
                        <?php
                        if(isset($top_buttons)){
                            echo "<ul class='nav navbar-right panel_toolbox'>$top_buttons</ul>";

                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="min-height:400px ">
                        <div id="context-menu" class="dropdown-menu" style="position: fixed !important; display: none; z-index: 1000; background-color: white; padding: 10px; color: black;">
                        </div>
                        <div id="graph-container">
                            <svg style="width: 100%; min-height: 500px;" ><g/></svg>
                        </div>
                        <script>
                            const baseUrl = "<?php echo base_url(); ?>";
                            const phasesList = <?php echo $phases_list; ?>;
                            const graph = createTree(phasesList, null, null, 'edition')

                            var render = new dagreD3.render();
                            var svg = d3.select("svg");
                            var svgGroup = svg.select("g");

                            render(svgGroup, graph);

                            var graphHeight = graph.graph().height;
                            svg.attr("height", graphHeight + 40);

                            var xCenterOffset = (svg.node().getBoundingClientRect().width - graph.graph().width) / 2;
                            svgGroup.attr("transform", "translate(" + xCenterOffset + ", 20)");

                            const divContextMenu = document.querySelector('#context-menu');

                            document.getElementById('graph-container').addEventListener('click', function(e) {
                                contextMenu(e, divContextMenu, phasesList);
                            })

                            divContextMenu.addEventListener('click', function(e) {
                                e.stopPropagation();
                            });
                        </script>

                        <?php

                        if (count(json_decode($phases_list, true)) == 0) {
                            echo '<div class="form-group" style="margin-bottom: 10px;">
                                  <div class="col-md-12 col-sm-12 col-xs-12">
                                  <p class="text-warning" style="margin-top: 5px; font-size: 12px;">
                                  <i class="fa fa-exclamation-triangle"></i> Add a phase before adding papers. Click to add a phase.
                                  </p>
                                  </div>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- /page content -->