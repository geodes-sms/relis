from relis_statistics_lib import (
    display_data, display_figure, NominalVariables, ContinuousVariables,
    desc_distr_vector, bar_plot_vector, box_plot_vector, violin_plot_vector,
    evolution_plot_vector, stacked_bar_plot_vector, grouped_bar_plot_vector
)

display_data(desc_distr_vector[NominalVariables.industrial], True)

display_figure(bar_plot_vector[NominalVariables.bidirectional], False)

display_figure(bar_plot_vector[NominalVariables.industrial], False)

display_figure(box_plot_vector[ContinuousVariables.publication_year], False)

display_figure(violin_plot_vector[ContinuousVariables.publication_year], True)

display_figure(evolution_plot_vector[NominalVariables.bidirectional], False)

display_figure(stacked_bar_plot_vector[NominalVariables.bidirectional][NominalVariables.domain], True)

display_figure(grouped_bar_plot_vector[NominalVariables.bidirectional][NominalVariables.domain], True)

input("Press enter to close...")