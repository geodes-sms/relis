from relis_statistics_lib import (
    display_data, display_figure, NominalVariables, ContinuousVariables,
    desc_frequency_tables, desc_statistics, desc_bar_plots, desc_box_plots, desc_violin_plots, 
    evo_plots, evo_frequency_tables, comp_stacked_bar_plots, comp_grouped_bar_plots,
    comp_chi_squared_tests, comp_spearman_cor_tests, comp_frequency_tables, comp_bubble_charts,
    comp_shapiro_wilk_tests, comp_pearson_cor_tests
)


# Statistical tests for variable: 'has_choco'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.has_choco], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.has_choco], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.has_choco], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.has_choco], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'temperature'

#--Descriptive--#
# Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.temperature], False)
# Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.temperature], False)
# Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.temperature], False)
# Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.temperature], False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.temperature][ContinuousVariables.year], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.temperature][ContinuousVariables.citation], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.temperature][ContinuousVariables.publication_year], False)
# Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.temperature][ContinuousVariables.year], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.temperature][ContinuousVariables.citation], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.temperature][ContinuousVariables.publication_year], False)

# Statistical tests for variable: 'brand'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.brand], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.brand], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.brand], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.brand], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'cocoa_origin'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.cocoa_origin], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.cocoa_origin], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.cocoa_origin], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.cocoa_origin], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'cocoa_level'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.cocoa_level], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.cocoa_level], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.cocoa_level], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.cocoa_level], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'types'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.types], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.types], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.types], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.types], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.bev_qualities], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.bev_qualities], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.bev_qualities], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.bev_qualities], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'variety'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.variety], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.variety], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.variety], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.variety], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'bev_qualities'

#--Descriptive--#
# Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.bev_qualities], False)
# Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.bev_qualities], False)

#--Evolutive--#
# Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.bev_qualities], False)
# Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.bev_qualities], False)

#--Comparative--#
# Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.variety], False)

# Statistical tests for variable: 'year'

#--Descriptive--#
# Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.year], False)
# Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.year], False)
# Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.year], False)
# Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.year], False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.year][ContinuousVariables.temperature], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.year][ContinuousVariables.citation], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.year][ContinuousVariables.publication_year], False)
# Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.year][ContinuousVariables.temperature], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.year][ContinuousVariables.citation], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.year][ContinuousVariables.publication_year], False)

# Statistical tests for variable: 'citation'

#--Descriptive--#
# Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.citation], False)
# Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.citation], False)
# Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.citation], False)
# Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.citation], False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.citation][ContinuousVariables.temperature], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.citation][ContinuousVariables.year], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.citation][ContinuousVariables.publication_year], False)
# Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.citation][ContinuousVariables.temperature], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.citation][ContinuousVariables.year], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.citation][ContinuousVariables.publication_year], False)

# Statistical tests for variable: 'publication_year'

#--Descriptive--#
# Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.publication_year], False)
# Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.publication_year], False)
# Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.publication_year], False)
# Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.publication_year], False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.temperature], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.year], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.citation], False)
# Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.temperature], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.year], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.citation], False)

input("Press enter to close...")