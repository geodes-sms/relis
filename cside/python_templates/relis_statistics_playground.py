from relis_statistics_kernel import (
    NominalVariables, ContinuousVariables,
    desc_frequency_table, desc_statistics, desc_bar_plot, desc_box_plot, desc_violin_plot, 
    evo_plot, evo_frequency_table, comp_stacked_bar_plot, comp_grouped_bar_plot,
    comp_chi_squared_test, comp_spearman_cor_test, comp_frequency_table, comp_bubble_chart,
    comp_shapiro_wilk_test, comp_pearson_cor_test
)

#-- Environment version : {{attribute(export_config,'ENVIRONMENT_VERSION')}}
{% set previousStatistic = null %}
{# Generating statistical tests for every variable #}
{% for key1, item in sam %}

# Statistical tests for variable: '{{ item.name }}'
{% for statistic in item.statistics %}
{% if loop.index == 1 %}

#--{{ statistic.type|capitalize }}--#
{% endif %}
{# Check if it's not the first iteration and if the current statistic is different from the previous one #}
{% if loop.index > 1 and statistic.type != previousStatistic.type %}

#--{{ statistic.type|capitalize }}--#
{% endif %}
{% set previousStatistic = statistic %}
# Name of test: [{{ statistic.title|raw }}]
{% if statistic.type != 'comparative'%}
{{statistic.name}}({{item.data_type}}Variables.{{item.name}}, False)
{% endif %}
{% if statistic.type == 'comparative'%}{# Every variable that has a comparative test will be compared with every other variable that shares the same return_data_type (DataFrame/Figure)#}
{% for key2, item_compa in sam %}{# If/else hell, but in short, we compare the statistic we have with all the other variable's statistics.#}
{% if item_compa.name != item.name %}{# We do not compare the variable with itself#}
{% for statistic_compa in item_compa.statistics %}{# Iterate over the list again #}
{% if statistic_compa.type == 'comparative' %}{# Check the type and make sure its comparative#}
{% if statistic_compa.return_data_type == statistic.return_data_type and statistic_compa.name == statistic.name and item_compa.data_type == item.data_type %}{# Make sure both comparative test have the same return type, the same name and the same data_type so they can be compared together #}
{{statistic.name}}({{item.data_type}}Variables.{{item.name}}, {{item_compa.data_type}}Variables.{{item_compa.name}}, False)
{% endif %}
{% endif %}
{% endfor %}
{% endif %}
{% endfor %}
{% endif %}
{% endfor %}
{% endfor %}

input("Press enter to close...")