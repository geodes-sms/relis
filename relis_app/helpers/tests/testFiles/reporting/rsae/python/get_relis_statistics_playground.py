from relis_statistics_kernel import (
    NominalVariables, ContinuousVariables,
    desc_frequency_table, desc_statistics, desc_bar_plot, desc_box_plot, desc_violin_plot, 
    evo_plot, evo_frequency_table, comp_stacked_bar_plot, comp_grouped_bar_plot,
    comp_chi_squared_test, comp_spearman_cor_test, comp_frequency_table, comp_bubble_chart,
    comp_shapiro_wilk_test, comp_pearson_cor_test
)

#-- Environment version : 1.0.0

# Statistical tests for variable: 'has_chocolate'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.has_chocolate, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.has_chocolate, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.has_chocolate, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.has_chocolate, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.has_chocolate, NominalVariables.brand, False)
comp_frequency_table(NominalVariables.has_chocolate, NominalVariables.cocoa_origin, False)
comp_frequency_table(NominalVariables.has_chocolate, NominalVariables.cocoa_level, False)
comp_frequency_table(NominalVariables.has_chocolate, NominalVariables.types, False)
comp_frequency_table(NominalVariables.has_chocolate, NominalVariables.variety, False)
comp_frequency_table(NominalVariables.has_chocolate, NominalVariables.venue, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.has_chocolate, NominalVariables.brand, False)
comp_stacked_bar_plot(NominalVariables.has_chocolate, NominalVariables.cocoa_origin, False)
comp_stacked_bar_plot(NominalVariables.has_chocolate, NominalVariables.cocoa_level, False)
comp_stacked_bar_plot(NominalVariables.has_chocolate, NominalVariables.types, False)
comp_stacked_bar_plot(NominalVariables.has_chocolate, NominalVariables.variety, False)
comp_stacked_bar_plot(NominalVariables.has_chocolate, NominalVariables.venue, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.has_chocolate, NominalVariables.brand, False)
comp_grouped_bar_plot(NominalVariables.has_chocolate, NominalVariables.cocoa_origin, False)
comp_grouped_bar_plot(NominalVariables.has_chocolate, NominalVariables.cocoa_level, False)
comp_grouped_bar_plot(NominalVariables.has_chocolate, NominalVariables.types, False)
comp_grouped_bar_plot(NominalVariables.has_chocolate, NominalVariables.variety, False)
comp_grouped_bar_plot(NominalVariables.has_chocolate, NominalVariables.venue, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.has_chocolate, NominalVariables.brand, False)
comp_bubble_chart(NominalVariables.has_chocolate, NominalVariables.cocoa_origin, False)
comp_bubble_chart(NominalVariables.has_chocolate, NominalVariables.cocoa_level, False)
comp_bubble_chart(NominalVariables.has_chocolate, NominalVariables.types, False)
comp_bubble_chart(NominalVariables.has_chocolate, NominalVariables.variety, False)
comp_bubble_chart(NominalVariables.has_chocolate, NominalVariables.venue, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.has_chocolate, NominalVariables.brand, False)
comp_chi_squared_test(NominalVariables.has_chocolate, NominalVariables.cocoa_origin, False)
comp_chi_squared_test(NominalVariables.has_chocolate, NominalVariables.cocoa_level, False)
comp_chi_squared_test(NominalVariables.has_chocolate, NominalVariables.types, False)
comp_chi_squared_test(NominalVariables.has_chocolate, NominalVariables.variety, False)
comp_chi_squared_test(NominalVariables.has_chocolate, NominalVariables.venue, False)

# Statistical tests for variable: 'temperature'

#--Descriptive--#
# Name of test: [Statistics]
desc_statistics(ContinuousVariables.temperature, False)
# Name of test: [Box plots]
desc_box_plot(ContinuousVariables.temperature, False)
# Name of test: [Violin plots]
desc_violin_plot(ContinuousVariables.temperature, False)
# Name of test: [Shapiro Wilk's correlation test]
comp_shapiro_wilk_test(ContinuousVariables.temperature, False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
comp_pearson_cor_test(ContinuousVariables.temperature, ContinuousVariables.year, False)
comp_pearson_cor_test(ContinuousVariables.temperature, ContinuousVariables.number_of_citations, False)
comp_pearson_cor_test(ContinuousVariables.temperature, ContinuousVariables.publication_year, False)
# Name of test: [Spearman's correlation test]
comp_spearman_cor_test(ContinuousVariables.temperature, ContinuousVariables.year, False)
comp_spearman_cor_test(ContinuousVariables.temperature, ContinuousVariables.number_of_citations, False)
comp_spearman_cor_test(ContinuousVariables.temperature, ContinuousVariables.publication_year, False)

# Statistical tests for variable: 'brand'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.brand, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.brand, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.brand, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.brand, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.brand, NominalVariables.has_chocolate, False)
comp_frequency_table(NominalVariables.brand, NominalVariables.cocoa_origin, False)
comp_frequency_table(NominalVariables.brand, NominalVariables.cocoa_level, False)
comp_frequency_table(NominalVariables.brand, NominalVariables.types, False)
comp_frequency_table(NominalVariables.brand, NominalVariables.variety, False)
comp_frequency_table(NominalVariables.brand, NominalVariables.venue, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.brand, NominalVariables.has_chocolate, False)
comp_stacked_bar_plot(NominalVariables.brand, NominalVariables.cocoa_origin, False)
comp_stacked_bar_plot(NominalVariables.brand, NominalVariables.cocoa_level, False)
comp_stacked_bar_plot(NominalVariables.brand, NominalVariables.types, False)
comp_stacked_bar_plot(NominalVariables.brand, NominalVariables.variety, False)
comp_stacked_bar_plot(NominalVariables.brand, NominalVariables.venue, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.brand, NominalVariables.has_chocolate, False)
comp_grouped_bar_plot(NominalVariables.brand, NominalVariables.cocoa_origin, False)
comp_grouped_bar_plot(NominalVariables.brand, NominalVariables.cocoa_level, False)
comp_grouped_bar_plot(NominalVariables.brand, NominalVariables.types, False)
comp_grouped_bar_plot(NominalVariables.brand, NominalVariables.variety, False)
comp_grouped_bar_plot(NominalVariables.brand, NominalVariables.venue, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.brand, NominalVariables.has_chocolate, False)
comp_bubble_chart(NominalVariables.brand, NominalVariables.cocoa_origin, False)
comp_bubble_chart(NominalVariables.brand, NominalVariables.cocoa_level, False)
comp_bubble_chart(NominalVariables.brand, NominalVariables.types, False)
comp_bubble_chart(NominalVariables.brand, NominalVariables.variety, False)
comp_bubble_chart(NominalVariables.brand, NominalVariables.venue, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.brand, NominalVariables.has_chocolate, False)
comp_chi_squared_test(NominalVariables.brand, NominalVariables.cocoa_origin, False)
comp_chi_squared_test(NominalVariables.brand, NominalVariables.cocoa_level, False)
comp_chi_squared_test(NominalVariables.brand, NominalVariables.types, False)
comp_chi_squared_test(NominalVariables.brand, NominalVariables.variety, False)
comp_chi_squared_test(NominalVariables.brand, NominalVariables.venue, False)

# Statistical tests for variable: 'cocoa_origin'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.cocoa_origin, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.cocoa_origin, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.cocoa_origin, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.cocoa_origin, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.cocoa_origin, NominalVariables.has_chocolate, False)
comp_frequency_table(NominalVariables.cocoa_origin, NominalVariables.brand, False)
comp_frequency_table(NominalVariables.cocoa_origin, NominalVariables.cocoa_level, False)
comp_frequency_table(NominalVariables.cocoa_origin, NominalVariables.types, False)
comp_frequency_table(NominalVariables.cocoa_origin, NominalVariables.variety, False)
comp_frequency_table(NominalVariables.cocoa_origin, NominalVariables.venue, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.cocoa_origin, NominalVariables.has_chocolate, False)
comp_stacked_bar_plot(NominalVariables.cocoa_origin, NominalVariables.brand, False)
comp_stacked_bar_plot(NominalVariables.cocoa_origin, NominalVariables.cocoa_level, False)
comp_stacked_bar_plot(NominalVariables.cocoa_origin, NominalVariables.types, False)
comp_stacked_bar_plot(NominalVariables.cocoa_origin, NominalVariables.variety, False)
comp_stacked_bar_plot(NominalVariables.cocoa_origin, NominalVariables.venue, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.cocoa_origin, NominalVariables.has_chocolate, False)
comp_grouped_bar_plot(NominalVariables.cocoa_origin, NominalVariables.brand, False)
comp_grouped_bar_plot(NominalVariables.cocoa_origin, NominalVariables.cocoa_level, False)
comp_grouped_bar_plot(NominalVariables.cocoa_origin, NominalVariables.types, False)
comp_grouped_bar_plot(NominalVariables.cocoa_origin, NominalVariables.variety, False)
comp_grouped_bar_plot(NominalVariables.cocoa_origin, NominalVariables.venue, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.cocoa_origin, NominalVariables.has_chocolate, False)
comp_bubble_chart(NominalVariables.cocoa_origin, NominalVariables.brand, False)
comp_bubble_chart(NominalVariables.cocoa_origin, NominalVariables.cocoa_level, False)
comp_bubble_chart(NominalVariables.cocoa_origin, NominalVariables.types, False)
comp_bubble_chart(NominalVariables.cocoa_origin, NominalVariables.variety, False)
comp_bubble_chart(NominalVariables.cocoa_origin, NominalVariables.venue, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.cocoa_origin, NominalVariables.has_chocolate, False)
comp_chi_squared_test(NominalVariables.cocoa_origin, NominalVariables.brand, False)
comp_chi_squared_test(NominalVariables.cocoa_origin, NominalVariables.cocoa_level, False)
comp_chi_squared_test(NominalVariables.cocoa_origin, NominalVariables.types, False)
comp_chi_squared_test(NominalVariables.cocoa_origin, NominalVariables.variety, False)
comp_chi_squared_test(NominalVariables.cocoa_origin, NominalVariables.venue, False)

# Statistical tests for variable: 'cocoa_level'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.cocoa_level, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.cocoa_level, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.cocoa_level, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.cocoa_level, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.cocoa_level, NominalVariables.has_chocolate, False)
comp_frequency_table(NominalVariables.cocoa_level, NominalVariables.brand, False)
comp_frequency_table(NominalVariables.cocoa_level, NominalVariables.cocoa_origin, False)
comp_frequency_table(NominalVariables.cocoa_level, NominalVariables.types, False)
comp_frequency_table(NominalVariables.cocoa_level, NominalVariables.variety, False)
comp_frequency_table(NominalVariables.cocoa_level, NominalVariables.venue, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.cocoa_level, NominalVariables.has_chocolate, False)
comp_stacked_bar_plot(NominalVariables.cocoa_level, NominalVariables.brand, False)
comp_stacked_bar_plot(NominalVariables.cocoa_level, NominalVariables.cocoa_origin, False)
comp_stacked_bar_plot(NominalVariables.cocoa_level, NominalVariables.types, False)
comp_stacked_bar_plot(NominalVariables.cocoa_level, NominalVariables.variety, False)
comp_stacked_bar_plot(NominalVariables.cocoa_level, NominalVariables.venue, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.cocoa_level, NominalVariables.has_chocolate, False)
comp_grouped_bar_plot(NominalVariables.cocoa_level, NominalVariables.brand, False)
comp_grouped_bar_plot(NominalVariables.cocoa_level, NominalVariables.cocoa_origin, False)
comp_grouped_bar_plot(NominalVariables.cocoa_level, NominalVariables.types, False)
comp_grouped_bar_plot(NominalVariables.cocoa_level, NominalVariables.variety, False)
comp_grouped_bar_plot(NominalVariables.cocoa_level, NominalVariables.venue, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.cocoa_level, NominalVariables.has_chocolate, False)
comp_bubble_chart(NominalVariables.cocoa_level, NominalVariables.brand, False)
comp_bubble_chart(NominalVariables.cocoa_level, NominalVariables.cocoa_origin, False)
comp_bubble_chart(NominalVariables.cocoa_level, NominalVariables.types, False)
comp_bubble_chart(NominalVariables.cocoa_level, NominalVariables.variety, False)
comp_bubble_chart(NominalVariables.cocoa_level, NominalVariables.venue, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.cocoa_level, NominalVariables.has_chocolate, False)
comp_chi_squared_test(NominalVariables.cocoa_level, NominalVariables.brand, False)
comp_chi_squared_test(NominalVariables.cocoa_level, NominalVariables.cocoa_origin, False)
comp_chi_squared_test(NominalVariables.cocoa_level, NominalVariables.types, False)
comp_chi_squared_test(NominalVariables.cocoa_level, NominalVariables.variety, False)
comp_chi_squared_test(NominalVariables.cocoa_level, NominalVariables.venue, False)

# Statistical tests for variable: 'types'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.types, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.types, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.types, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.types, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.types, NominalVariables.has_chocolate, False)
comp_frequency_table(NominalVariables.types, NominalVariables.brand, False)
comp_frequency_table(NominalVariables.types, NominalVariables.cocoa_origin, False)
comp_frequency_table(NominalVariables.types, NominalVariables.cocoa_level, False)
comp_frequency_table(NominalVariables.types, NominalVariables.variety, False)
comp_frequency_table(NominalVariables.types, NominalVariables.venue, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.types, NominalVariables.has_chocolate, False)
comp_stacked_bar_plot(NominalVariables.types, NominalVariables.brand, False)
comp_stacked_bar_plot(NominalVariables.types, NominalVariables.cocoa_origin, False)
comp_stacked_bar_plot(NominalVariables.types, NominalVariables.cocoa_level, False)
comp_stacked_bar_plot(NominalVariables.types, NominalVariables.variety, False)
comp_stacked_bar_plot(NominalVariables.types, NominalVariables.venue, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.types, NominalVariables.has_chocolate, False)
comp_grouped_bar_plot(NominalVariables.types, NominalVariables.brand, False)
comp_grouped_bar_plot(NominalVariables.types, NominalVariables.cocoa_origin, False)
comp_grouped_bar_plot(NominalVariables.types, NominalVariables.cocoa_level, False)
comp_grouped_bar_plot(NominalVariables.types, NominalVariables.variety, False)
comp_grouped_bar_plot(NominalVariables.types, NominalVariables.venue, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.types, NominalVariables.has_chocolate, False)
comp_bubble_chart(NominalVariables.types, NominalVariables.brand, False)
comp_bubble_chart(NominalVariables.types, NominalVariables.cocoa_origin, False)
comp_bubble_chart(NominalVariables.types, NominalVariables.cocoa_level, False)
comp_bubble_chart(NominalVariables.types, NominalVariables.variety, False)
comp_bubble_chart(NominalVariables.types, NominalVariables.venue, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.types, NominalVariables.has_chocolate, False)
comp_chi_squared_test(NominalVariables.types, NominalVariables.brand, False)
comp_chi_squared_test(NominalVariables.types, NominalVariables.cocoa_origin, False)
comp_chi_squared_test(NominalVariables.types, NominalVariables.cocoa_level, False)
comp_chi_squared_test(NominalVariables.types, NominalVariables.variety, False)
comp_chi_squared_test(NominalVariables.types, NominalVariables.venue, False)

# Statistical tests for variable: 'variety'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.variety, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.variety, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.variety, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.variety, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.variety, NominalVariables.has_chocolate, False)
comp_frequency_table(NominalVariables.variety, NominalVariables.brand, False)
comp_frequency_table(NominalVariables.variety, NominalVariables.cocoa_origin, False)
comp_frequency_table(NominalVariables.variety, NominalVariables.cocoa_level, False)
comp_frequency_table(NominalVariables.variety, NominalVariables.types, False)
comp_frequency_table(NominalVariables.variety, NominalVariables.venue, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.variety, NominalVariables.has_chocolate, False)
comp_stacked_bar_plot(NominalVariables.variety, NominalVariables.brand, False)
comp_stacked_bar_plot(NominalVariables.variety, NominalVariables.cocoa_origin, False)
comp_stacked_bar_plot(NominalVariables.variety, NominalVariables.cocoa_level, False)
comp_stacked_bar_plot(NominalVariables.variety, NominalVariables.types, False)
comp_stacked_bar_plot(NominalVariables.variety, NominalVariables.venue, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.variety, NominalVariables.has_chocolate, False)
comp_grouped_bar_plot(NominalVariables.variety, NominalVariables.brand, False)
comp_grouped_bar_plot(NominalVariables.variety, NominalVariables.cocoa_origin, False)
comp_grouped_bar_plot(NominalVariables.variety, NominalVariables.cocoa_level, False)
comp_grouped_bar_plot(NominalVariables.variety, NominalVariables.types, False)
comp_grouped_bar_plot(NominalVariables.variety, NominalVariables.venue, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.variety, NominalVariables.has_chocolate, False)
comp_bubble_chart(NominalVariables.variety, NominalVariables.brand, False)
comp_bubble_chart(NominalVariables.variety, NominalVariables.cocoa_origin, False)
comp_bubble_chart(NominalVariables.variety, NominalVariables.cocoa_level, False)
comp_bubble_chart(NominalVariables.variety, NominalVariables.types, False)
comp_bubble_chart(NominalVariables.variety, NominalVariables.venue, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.variety, NominalVariables.has_chocolate, False)
comp_chi_squared_test(NominalVariables.variety, NominalVariables.brand, False)
comp_chi_squared_test(NominalVariables.variety, NominalVariables.cocoa_origin, False)
comp_chi_squared_test(NominalVariables.variety, NominalVariables.cocoa_level, False)
comp_chi_squared_test(NominalVariables.variety, NominalVariables.types, False)
comp_chi_squared_test(NominalVariables.variety, NominalVariables.venue, False)

# Statistical tests for variable: 'venue'

#--Descriptive--#
# Name of test: [Frequency tables]
desc_frequency_table(NominalVariables.venue, False)
# Name of test: [Bar plots]
desc_bar_plot(NominalVariables.venue, False)

#--Evolutive--#
# Name of test: [Frequency tables]
evo_frequency_table(NominalVariables.venue, False)
# Name of test: [Evolution plots]
evo_plot(NominalVariables.venue, False)

#--Comparative--#
# Name of test: [Frequency tables]
comp_frequency_table(NominalVariables.venue, NominalVariables.has_chocolate, False)
comp_frequency_table(NominalVariables.venue, NominalVariables.brand, False)
comp_frequency_table(NominalVariables.venue, NominalVariables.cocoa_origin, False)
comp_frequency_table(NominalVariables.venue, NominalVariables.cocoa_level, False)
comp_frequency_table(NominalVariables.venue, NominalVariables.types, False)
comp_frequency_table(NominalVariables.venue, NominalVariables.variety, False)
# Name of test: [Stacked bar plots]
comp_stacked_bar_plot(NominalVariables.venue, NominalVariables.has_chocolate, False)
comp_stacked_bar_plot(NominalVariables.venue, NominalVariables.brand, False)
comp_stacked_bar_plot(NominalVariables.venue, NominalVariables.cocoa_origin, False)
comp_stacked_bar_plot(NominalVariables.venue, NominalVariables.cocoa_level, False)
comp_stacked_bar_plot(NominalVariables.venue, NominalVariables.types, False)
comp_stacked_bar_plot(NominalVariables.venue, NominalVariables.variety, False)
# Name of test: [Grouped bar plots]
comp_grouped_bar_plot(NominalVariables.venue, NominalVariables.has_chocolate, False)
comp_grouped_bar_plot(NominalVariables.venue, NominalVariables.brand, False)
comp_grouped_bar_plot(NominalVariables.venue, NominalVariables.cocoa_origin, False)
comp_grouped_bar_plot(NominalVariables.venue, NominalVariables.cocoa_level, False)
comp_grouped_bar_plot(NominalVariables.venue, NominalVariables.types, False)
comp_grouped_bar_plot(NominalVariables.venue, NominalVariables.variety, False)
# Name of test: [Bubble charts]
comp_bubble_chart(NominalVariables.venue, NominalVariables.has_chocolate, False)
comp_bubble_chart(NominalVariables.venue, NominalVariables.brand, False)
comp_bubble_chart(NominalVariables.venue, NominalVariables.cocoa_origin, False)
comp_bubble_chart(NominalVariables.venue, NominalVariables.cocoa_level, False)
comp_bubble_chart(NominalVariables.venue, NominalVariables.types, False)
comp_bubble_chart(NominalVariables.venue, NominalVariables.variety, False)
# Name of test: [Chi-squared test]
comp_chi_squared_test(NominalVariables.venue, NominalVariables.has_chocolate, False)
comp_chi_squared_test(NominalVariables.venue, NominalVariables.brand, False)
comp_chi_squared_test(NominalVariables.venue, NominalVariables.cocoa_origin, False)
comp_chi_squared_test(NominalVariables.venue, NominalVariables.cocoa_level, False)
comp_chi_squared_test(NominalVariables.venue, NominalVariables.types, False)
comp_chi_squared_test(NominalVariables.venue, NominalVariables.variety, False)

# Statistical tests for variable: 'year'

#--Descriptive--#
# Name of test: [Statistics]
desc_statistics(ContinuousVariables.year, False)
# Name of test: [Box plots]
desc_box_plot(ContinuousVariables.year, False)
# Name of test: [Violin plots]
desc_violin_plot(ContinuousVariables.year, False)
# Name of test: [Shapiro Wilk's correlation test]
comp_shapiro_wilk_test(ContinuousVariables.year, False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
comp_pearson_cor_test(ContinuousVariables.year, ContinuousVariables.temperature, False)
comp_pearson_cor_test(ContinuousVariables.year, ContinuousVariables.number_of_citations, False)
comp_pearson_cor_test(ContinuousVariables.year, ContinuousVariables.publication_year, False)
# Name of test: [Spearman's correlation test]
comp_spearman_cor_test(ContinuousVariables.year, ContinuousVariables.temperature, False)
comp_spearman_cor_test(ContinuousVariables.year, ContinuousVariables.number_of_citations, False)
comp_spearman_cor_test(ContinuousVariables.year, ContinuousVariables.publication_year, False)

# Statistical tests for variable: 'number_of_citations'

#--Descriptive--#
# Name of test: [Statistics]
desc_statistics(ContinuousVariables.number_of_citations, False)
# Name of test: [Box plots]
desc_box_plot(ContinuousVariables.number_of_citations, False)
# Name of test: [Violin plots]
desc_violin_plot(ContinuousVariables.number_of_citations, False)
# Name of test: [Shapiro Wilk's correlation test]
comp_shapiro_wilk_test(ContinuousVariables.number_of_citations, False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
comp_pearson_cor_test(ContinuousVariables.number_of_citations, ContinuousVariables.temperature, False)
comp_pearson_cor_test(ContinuousVariables.number_of_citations, ContinuousVariables.year, False)
comp_pearson_cor_test(ContinuousVariables.number_of_citations, ContinuousVariables.publication_year, False)
# Name of test: [Spearman's correlation test]
comp_spearman_cor_test(ContinuousVariables.number_of_citations, ContinuousVariables.temperature, False)
comp_spearman_cor_test(ContinuousVariables.number_of_citations, ContinuousVariables.year, False)
comp_spearman_cor_test(ContinuousVariables.number_of_citations, ContinuousVariables.publication_year, False)

# Statistical tests for variable: 'publication_year'

#--Descriptive--#
# Name of test: [Statistics]
desc_statistics(ContinuousVariables.publication_year, False)
# Name of test: [Box plots]
desc_box_plot(ContinuousVariables.publication_year, False)
# Name of test: [Violin plots]
desc_violin_plot(ContinuousVariables.publication_year, False)
# Name of test: [Shapiro Wilk's correlation test]
comp_shapiro_wilk_test(ContinuousVariables.publication_year, False)

#--Comparative--#
# Name of test: [Pearson's correlation test]
comp_pearson_cor_test(ContinuousVariables.publication_year, ContinuousVariables.temperature, False)
comp_pearson_cor_test(ContinuousVariables.publication_year, ContinuousVariables.year, False)
comp_pearson_cor_test(ContinuousVariables.publication_year, ContinuousVariables.number_of_citations, False)
# Name of test: [Spearman's correlation test]
comp_spearman_cor_test(ContinuousVariables.publication_year, ContinuousVariables.temperature, False)
comp_spearman_cor_test(ContinuousVariables.publication_year, ContinuousVariables.year, False)
comp_spearman_cor_test(ContinuousVariables.publication_year, ContinuousVariables.number_of_citations, False)

input("Press enter to close...")