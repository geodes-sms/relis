from relis_statistics_lib import (
    display_data, display_figure, NominalVariables, ContinuousVariables,
    desc_frequency_tables, desc_statistics, desc_bar_plots, desc_box_plots, desc_violin_plots, 
    evo_plots, evo_frequency_tables, comp_stacked_bar_plots, comp_grouped_bar_plots,
    comp_chi_squared_tests, comp_spearman_cor_tests, comp_frequency_tables, comp_bubble_charts,
    comp_shapiro_wilk_tests, comp_pearson_cor_tests
)
{# Generating statistical tests for every variable #}
{% for key1, item in cm %}

# Statistical tests for variable: {{ item.name }}
{% for statistic in item.statistics %}
{% set foo = statistic.name[9:]~'s'%}
{% if statistic.type != 'comparative'%}
{% if statistic.return_data_type == 'Dataframe'%}
display_data({{foo}}[{{item.data_type}}Variables.{{item.name}}], False)
{% endif %}
{% if statistic.return_data_type == 'Figure'%}
display_figure({{foo}}[{{item.data_type}}Variables.{{item.name}}], False)
{% endif %}
{% endif %}
{% if statistic.type == 'comparative'%}{# Every variable that has a comparative test will be compared with every other variable that shares the same return_data_type (DataFrame/Figure)#}
{% for key2, item_compa in cm %}{# If/else hell, but in short, we compare the statistic we have with all the other variable's statistics.#}
{% if item_compa.name != item.name %}{# We do not compare the variable with itself#}
{% for statistic_compa in item_compa.statistics %}{# Iterate over the list again #}
{% if statistic_compa.type == 'comparative' %}{# Check the type and make sure its comparative#}
{% if statistic_compa.return_data_type == statistic.return_data_type and statistic_compa.name == statistic.name and item_compa.data_type == item.data_type %}{# Make sure both comparative test have the same return type, the same name and the same data_type so they can be compared together #}
{% if statistic_compa.return_data_type == 'Dataframe'%}{# Same as above, we display data or figure depending on the output #}
display_data({{foo}}[{{item.data_type}}Variables.{{item.name}}][{{item_compa.data_type}}Variables.{{item_compa.name}}], False)
{% endif %}
{% if statistic_compa.return_data_type == 'Figure'%}
display_figure({{foo}}[{{item.data_type}}Variables.{{item.name}}][{{item_compa.data_type}}Variables.{{item_compa.name}}], False)
{% endif %}
{% endif %}
{% endif %}
{% endfor %}
{% endif %}
{% endfor %}
{% endif %}
{% endfor %}
{% endfor %}

input("Press enter to close...")