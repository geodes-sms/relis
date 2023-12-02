from relis_statistics_lib import (
    display_data, display_figure, NominalVariables, ContinuousVariables,
    desc_frequency_tables, desc_statistics, desc_bar_plots, desc_box_plots, desc_violin_plots, 
    evo_plots, evo_frequency_tables, comp_stacked_bar_plots, comp_grouped_bar_plots,
    comp_chi_squared_tests, comp_spearman_cor_tests, comp_frequency_tables, comp_bubble_charts,
    comp_shapiro_wilk_tests, comp_pearson_cor_tests
)

# Statistical tests for variable: 'has_choco'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.has_choco], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.has_choco], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.has_choco], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.has_choco], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.has_choco][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.has_choco][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'temperature'
# Type of test: [descriptive] Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.temperature], False)
# Type of test: [descriptive] Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.temperature], False)
# Type of test: [descriptive] Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.temperature], False)
# Type of test: [descriptive] Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.temperature], False)
# Type of test: [comparative] Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.temperature][ContinuousVariables.year], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.temperature][ContinuousVariables.citation], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.temperature][ContinuousVariables.publication_year], False)
# Type of test: [comparative] Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.temperature][ContinuousVariables.year], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.temperature][ContinuousVariables.citation], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.temperature][ContinuousVariables.publication_year], False)

# Statistical tests for variable: 'brand'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.brand], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.brand], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.brand], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.brand], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.brand][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.brand][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'cocoa_origin'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.cocoa_origin], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.cocoa_origin], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.cocoa_origin], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.cocoa_origin], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_origin][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'cocoa_level'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.cocoa_level], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.cocoa_level], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.cocoa_level], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.cocoa_level], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.cocoa_level][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'types'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.types], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.types], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.types], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.types], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.variety], False)
display_data(comp_frequency_tables[NominalVariables.types][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.variety], False)
display_figure(comp_stacked_bar_plots[NominalVariables.types][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.variety], False)
display_figure(comp_grouped_bar_plots[NominalVariables.types][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.variety], False)
display_figure(comp_bubble_charts[NominalVariables.types][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.variety], False)
display_data(comp_chi_squared_tests[NominalVariables.types][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'variety'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.variety], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.variety], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.variety], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.variety], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.variety][NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.variety][NominalVariables.bev_qualities], False)

# Statistical tests for variable: 'bev_qualities'
# Type of test: [descriptive] Name of test: [Frequency tables]
display_data(desc_frequency_tables[NominalVariables.bev_qualities], False)
# Type of test: [descriptive] Name of test: [Bar plots]
display_figure(desc_bar_plots[NominalVariables.bev_qualities], False)
# Type of test: [evolutive] Name of test: [Frequency tables]
display_data(evo_frequency_tables[NominalVariables.bev_qualities], False)
# Type of test: [evolutive] Name of test: [Evolution plots]
display_figure(evo_plots[NominalVariables.bev_qualities], False)
# Type of test: [comparative] Name of test: [Frequency tables]
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.types], False)
display_data(comp_frequency_tables[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Type of test: [comparative] Name of test: [Stacked bar plots]
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.types], False)
display_figure(comp_stacked_bar_plots[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Type of test: [comparative] Name of test: [Grouped bar plots]
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.types], False)
display_figure(comp_grouped_bar_plots[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Type of test: [comparative] Name of test: [Bubble charts]
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.types], False)
display_figure(comp_bubble_charts[NominalVariables.bev_qualities][NominalVariables.variety], False)
# Type of test: [comparative] Name of test: [Chi-squared test]
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.has_choco], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.brand], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.cocoa_origin], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.cocoa_level], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.types], False)
display_data(comp_chi_squared_tests[NominalVariables.bev_qualities][NominalVariables.variety], False)

# Statistical tests for variable: 'year'
# Type of test: [descriptive] Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.year], False)
# Type of test: [descriptive] Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.year], False)
# Type of test: [descriptive] Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.year], False)
# Type of test: [descriptive] Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.year], False)
# Type of test: [comparative] Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.year][ContinuousVariables.temperature], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.year][ContinuousVariables.citation], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.year][ContinuousVariables.publication_year], False)
# Type of test: [comparative] Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.year][ContinuousVariables.temperature], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.year][ContinuousVariables.citation], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.year][ContinuousVariables.publication_year], False)

# Statistical tests for variable: 'citation'
# Type of test: [descriptive] Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.citation], False)
# Type of test: [descriptive] Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.citation], False)
# Type of test: [descriptive] Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.citation], False)
# Type of test: [descriptive] Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.citation], False)
# Type of test: [comparative] Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.citation][ContinuousVariables.temperature], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.citation][ContinuousVariables.year], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.citation][ContinuousVariables.publication_year], False)
# Type of test: [comparative] Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.citation][ContinuousVariables.temperature], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.citation][ContinuousVariables.year], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.citation][ContinuousVariables.publication_year], False)

# Statistical tests for variable: 'publication_year'
# Type of test: [descriptive] Name of test: [Statistics]
display_data(desc_statistics[ContinuousVariables.publication_year], False)
# Type of test: [descriptive] Name of test: [Box plots]
display_figure(desc_box_plots[ContinuousVariables.publication_year], False)
# Type of test: [descriptive] Name of test: [Violin plots]
display_figure(desc_violin_plots[ContinuousVariables.publication_year], False)
# Type of test: [descriptive] Name of test: [Shapiro Wilk's correlation test]
display_data(comp_shapiro_wilk_tests[ContinuousVariables.publication_year], False)
# Type of test: [comparative] Name of test: [Pearson's correlation test]
display_data(comp_pearson_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.temperature], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.year], False)
display_data(comp_pearson_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.citation], False)
# Type of test: [comparative] Name of test: [Spearman's correlation test]
display_data(comp_spearman_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.temperature], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.year], False)
display_data(comp_spearman_cor_tests[ContinuousVariables.publication_year][ContinuousVariables.citation], False)

input("Press enter to close...")