import re
import numpy as np
import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt
from enum import Enum
from typing import Type
from matplotlib import ticker
from matplotlib.text import Text
from statsmodels.robust.scale import mad
from scipy.stats import kurtosis, skew, shapiro, spearmanr, pearsonr, chi2_contingency

#-- Environment version : {{attribute(export_config,'ENVIRONMENT_VERSION')}}
#-- Generated timestamp: {{attribute(export_config,'DATE_TIME_GENERATED')}}

### Config

plt.rcParams['figure.max_open_warning'] = 0

custom = {'axes.edgecolor': 'black', 'grid.linestyle': 'dashed', 'grid.color': 'grey'}

sns.set_style('darkgrid', rc = custom)

class Multivalue(Enum):
    SEPARATOR = '{{attribute(export_config,'MULTIVALUE_SEPARATOR')}}'

class Policies(Enum):
    DROP_NA = {{attribute(export_config,'DROP_NA') ? 'True' : 'False' }}

### Types

class VariableDataType(Enum):
    NOMINAL = 'Nominal'
    CONTINUOUS = 'Continuous'

class Variable:
    def __init__(self, name: str, title: str, data_type: VariableDataType, multiple: bool):
        self.name = name
        self.title = title
        self.data_type = data_type
        self.multiple = multiple

{#Producing the Nominal variables of our configuration model #}
class NominalVariables(Enum):
{% for key1, item in sam %}
{% if item.data_type == 'Nominal'%}
    {{ item.name }} = Variable("{{item.name}}", "{{item.title}}", VariableDataType.NOMINAL, {{attribute(item,'multiple') ? 'True' : 'False' }})
{% endif %}
{% endfor %}

{#Producing the Continuous variables of our configuration model #}
class ContinuousVariables(Enum):    
{% for key1, item in sam %}
{% if item.data_type == 'Continuous'%}
    {{ item.name }} = Variable("{{item.name}}", "{{item.title}}", VariableDataType.CONTINUOUS, {{attribute(item,'multiple') ? 'True' : 'False' }})
{% endif %}
{% endfor %}

class DataFrame:
    def __init__(self, data: pd.DataFrame, variable_type: Type[NominalVariables] | Type[ContinuousVariables]):
        self.data = data
        self.variable_type = variable_type

class NominalDataFrame(DataFrame):
    def __init__(self, data: pd.DataFrame, variable_type: Type[NominalVariables]):
        super().__init__(data, variable_type)

class ContinuousDataFrame(DataFrame):
    def __init__(self, data: pd.DataFrame, variable_type: Type[ContinuousVariables]):
        super().__init__(data, variable_type)

class DataCache:
    def __init__(self):
        self.cache = {}

    def load_csv(self, file_path: str, encoding: str):
        if file_path not in self.cache:
            self.cache[file_path] = pd.read_csv(file_path, encoding=encoding)
        return self.cache[file_path]

### Shared

def _substitute_nan(df: pd.DataFrame) -> None:
    df.replace(np.nan, '', inplace=True)

def _get_variable(field_name: str, variables) -> Variable:
    return variables[field_name].value

def _split_multiple_values(value):
    if not pd.isna(value):
        return [item.strip() for item in re.split(rf'\{Multivalue.SEPARATOR.value}', value)] 
    
    return [value]

def _process_multiple_values(values: pd.Series, multiple: bool):
    if (multiple):
        return values.apply(lambda x: _split_multiple_values(x))

    return values.apply(lambda x: [x])

def _dataframe_get_title(statistic_type: str, statistic_name: str,
                          variable_name: str, comparison_variable_name = None):
    
    base_str =  f"{statistic_type} | {statistic_name} : {variable_name  }"
    if comparison_variable_name: base_str += f" and {comparison_variable_name}"

    return {'title': base_str}

def _configure_seaborn_legend(title: str, ax, plt):
    handles, labels = ax.get_legend_handles_labels()

    if handles:
        plt.legend(bbox_to_anchor=(1, 1), loc='upper left', title=title)
        plt.gca().get_legend().get_frame().set_edgecolor('black')

def _dataframe_update_title(dataFrame: pd.DataFrame, object: dict):
    dataFrame.attrs.update(object)

def _create_empty_dataframe(title: dict[str, str], _dataframe_update_title):
    empty_df = pd.DataFrame()
    _dataframe_update_title(empty_df, title)

    return empty_df

def _validate_comp_shapiro_wilk_test(variable_result: pd.DataFrame,
                              comparison_variable_result: pd.DataFrame):
    if variable_result.empty or comparison_variable_result.empty:
        return

    return True

def _no_data_message():
    return 'No data... Nothing to show'

def _display_data(dataFrame: pd.DataFrame):
    if not bool:
        return
    
    if dataFrame.attrs.get('title'): print(dataFrame.attrs.get('title'))

    if dataFrame.size != 0:
        print(dataFrame.to_markdown())
    else:
        print(_no_data_message())
    print('\n')

def _display_figure(plt):
    if not bool:
        return
    
    if isinstance(plt, Text):
        print(plt.get_text())
        print(_no_data_message())
        print('\n')
        return
    else: plt.show()

### Data

## Parsing

data_cache = DataCache()

{# The data should be at the root of the project, with the name of the project as the name of the .csv #}
def _read_project_classification_data(path = './{{attribute(export_config,'CLASSIFICATION_FILE_NAME')}}'):
    return data_cache.load_csv(path, 'utf8')

def _aggregate_variables_by_data_type(variables: type[NominalVariables] | type[ContinuousVariables]):
    return {variable.value.title: variable.name for variable in variables}

def _transform_classification_data(project_classification_data: pd.DataFrame, aggregated_variables: dict[str, str]):
    data = project_classification_data[aggregated_variables.keys()].rename(columns=aggregated_variables)

    if not Policies.DROP_NA.value:
        _substitute_nan(data)

    return data

## Preprocessing

def _nominal_dataframe():
    project_classification_data = _read_project_classification_data()
    aggregated_variables = _aggregate_variables_by_data_type(NominalVariables)
    nominal_data = _transform_classification_data(project_classification_data, aggregated_variables)
    return NominalDataFrame(nominal_data, NominalVariables)

def _continuous_dataframe():
    project_classification_data = _read_project_classification_data()
    aggregated_variables = _aggregate_variables_by_data_type(ContinuousVariables)
    continuous_data = _transform_classification_data(project_classification_data, aggregated_variables)
    return ContinuousDataFrame(continuous_data, ContinuousVariables)

### DESCRIPTIVE STATS

## Util

def _beautify_data_desc(field_name: str, data: pd.DataFrame):
    # Get metadata
    variable = _get_variable(field_name, NominalVariables)

    # Split the values by the multivalue character and flatten the result
    split_values = _process_multiple_values(data[field_name], variable.multiple)
    flattened_values = np.concatenate(split_values)

    # Generate the frequency table
    freq_table = pd.Series(flattened_values, dtype=str).value_counts().reset_index()
    freq_table.columns = ['value', 'n']

    # Calculate the percentage
    freq_table['percentage'] = (freq_table['n'] / freq_table['n'].sum()) * 100

    return freq_table

## Frequency tables

def _desc_frequency_table(classification_variable: NominalVariables):
    df = _nominal_dataframe().data

    variable = classification_variable.value
    
    df_title = _dataframe_get_title('Descriptive', 'Frequency tables', variable.title)
    
    if df.empty: return _create_empty_dataframe(df_title, _dataframe_update_title)

    subset_data = _beautify_data_desc(variable.name, df)

    _dataframe_update_title(subset_data, df_title)

    return subset_data

def desc_frequency_table(classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _desc_frequency_table(classification_variable)
    _display_data(data)

## Bar plots

def _desc_bar_plot(classification_variable: NominalVariables):
    df = _nominal_dataframe().data

    variable = classification_variable.value

    # Set labels and title
    title = f"{variable.title} ~ Bar plot"
    
    if df.empty: return plt.title(title)

    df = _beautify_data_desc(variable.name, df)

    if df.empty: return plt.title(title) 

    # Create the plot
    fig, ax = plt.subplots(figsize=(10, 6))
    hue = 'n'
    sns.barplot(data=df, x='value', y='percentage', hue=hue, dodge=False) # type: ignore

    plt.title(title)
    plt.xlabel(variable.title)
    plt.ylabel('Percentage')
    _configure_seaborn_legend(hue, ax, plt)

    return fig

def desc_bar_plot(classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _desc_bar_plot(classification_variable)
    _display_figure(data)

## Statistics

def _desc_statistics(classification_variable: ContinuousVariables):
    df = _continuous_dataframe().data

    variable = classification_variable.value

    series =  df[variable.name]

    df_title = _dataframe_get_title('Descriptive', 'Statistics', variable.title)
    
    if series.empty: return _create_empty_dataframe(df_title, _dataframe_update_title)
    
    series.replace('', np.nan, inplace=True)

    nan_policy = 'omit' if Policies.DROP_NA.value else 'propagate'
    results = {
    'vars': 1,
    'n': series.count(),
    'mean': series.mean(),
    'sd': series.std(),
    'median': series.median(),
    'trimmed': series[series.between(series.quantile(0.25), series.quantile(0.75))].mean(),
    'mad': mad(series),
    'min': series.min(),
    'max': series.max(),
    'range': series.max() - series.min(),
    'skew': skew(series, nan_policy=nan_policy),
    'kurtosis': kurtosis(series, nan_policy=nan_policy, fisher=True),
    'se': series.std() / np.sqrt(series.count())  
    }

    subset_data = pd.DataFrame(results, index=[0])

    _dataframe_update_title(subset_data, df_title)

    return subset_data

def desc_statistics(classification_variable: ContinuousVariables, show: bool):
    if not show: return
    
    data = _desc_statistics(classification_variable)
    _display_data(data)

## Box Plots

def _desc_box_plot(classification_variable: ContinuousVariables):
    df = _continuous_dataframe().data

    variable = classification_variable.value

    series =  df[variable.name]

    # Set the title and labels
    title = f"{variable.title} ~ Box plot"

    if series.empty: return plt.title(title)

    # Create the box plot
    fig, ax = plt.subplots(figsize=(10, 6))
    sns.boxplot(data=series, color='lightblue')

    # Overlay the mean point
    mean_value = series.mean()
    plt.scatter(x=0, y=mean_value, color='red', s=50, zorder=3)  # s is the size of the point

    plt.title(title)
    plt.ylabel(variable.title)
    plt.xlabel('')
    plt.gca().yaxis.set_major_formatter(ticker.FormatStrFormatter('%0.0f'))

    return fig

def desc_box_plot(classification_variable: ContinuousVariables, show: bool):
    if not show: return
    
    data = _desc_box_plot(classification_variable)
    _display_figure(data)

## Violin Plots

def _desc_violin_plot(classification_variable: ContinuousVariables):
    df = _continuous_dataframe().data

    variable = classification_variable.value

    series =  df[variable.name]
    
    title = f"{variable.title} ~ Violin plot"

    if series.empty: return plt.title(title)

    fig, ax = plt.subplots(figsize=(10, 6))
    sns.violinplot(data=series, color='lightgray')

    plt.title(title)
    plt.ylabel(variable.title)
    plt.xlabel('Density')
    plt.xticks([])

    return fig

def desc_violin_plot(classification_variable: ContinuousVariables, show: bool):
    if not show: return
    
    data = _desc_violin_plot(classification_variable)
    _display_figure(data)

### EVOLUTIVE STATS

## Util

def _beautify_data_evo(field_name: str, publication_year: pd.Series, variable: Variable, data: pd.DataFrame):
    series = data[field_name]
    
    # Create new DataFrame with specified columns
    subset_data = pd.DataFrame({
        'Year': publication_year,
        'Value': _process_multiple_values(series, variable.multiple)
    })
    
    subset_data = subset_data.explode('Value')

    # Remove rows with empty values
    subset_data = subset_data[(subset_data['Value'] != '')]

    subset_data = subset_data.groupby(['Year', 'Value']).size().reset_index(name='Frequency')

    return subset_data

## Frequency tables

def _evo_frequency_table(classification_variable: NominalVariables):
    df = _nominal_dataframe().data

    publication_year = _continuous_dataframe().data["publication_year"]

    variable = classification_variable.value

    subset_data = _beautify_data_evo(variable.name, publication_year, variable, df)

    # Pivoting the data
    subset_data = subset_data.pivot(index='Year', columns='Value', values='Frequency').replace('', np.nan).fillna(0)

    subset_data.columns.name = None
    subset_data.reset_index(inplace=True)

    _dataframe_update_title(subset_data, _dataframe_get_title('Evolutive', 'Frequency tables', variable.title))

    return subset_data 

def evo_frequency_table(classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _evo_frequency_table(classification_variable)
    _display_data(data)

## Evolution Plots

def _evo_plot(classification_variable: NominalVariables):
    df = _nominal_dataframe().data

    publication_year = _continuous_dataframe().data["publication_year"]

    variable = classification_variable.value
    
    subset_data = _beautify_data_evo(variable.name, publication_year, variable, df)

    title = f"{variable.title} ~ Evolution plot"

    if subset_data.empty: return plt.title(title)

    # Create a plot
    fig, ax = plt.subplots(figsize=(10, 6))
    hue = 'Value'
    sns.lineplot(data=subset_data, x='Year', y='Frequency', hue=hue, style='Value', markers=True)

    # Setting title, labels, and theme
    plt.title(title)
    plt.xlabel('Year')
    plt.ylabel('Frequency')
    plt.grid(True)
    _configure_seaborn_legend(hue, ax, plt)

    return fig

def evo_plot(classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _evo_plot(classification_variable)
    _display_figure(data)

### COMPARATIVE STATS

## Util

def _beautify_data_comp(field_name: str, comparison_variable_name: str,
                        variable: Variable, comparison_variable: Variable, data: pd.DataFrame):    
    subset_data = pd.DataFrame({
        field_name: data[field_name],
        comparison_variable_name: data[comparison_variable_name]
    })
    
    # Filtering out rows where any of the variables is empty
    subset_data = subset_data[(subset_data[field_name] != '') & (subset_data[comparison_variable_name] != '')]

    # Splitting the strings and expanding into separate rows
    subset_data[field_name] = _process_multiple_values(subset_data[field_name], variable.multiple)
    subset_data = subset_data.explode(field_name)

    subset_data[comparison_variable_name] = _process_multiple_values(subset_data[comparison_variable_name],
                                                                  comparison_variable.multiple)
    subset_data = subset_data.explode(comparison_variable_name)

    # Counting occurrences
    subset_data = subset_data.groupby([field_name, comparison_variable_name]).size().reset_index(name='Frequency')

    return subset_data

## Frequency Tables

def _comp_frequency_table(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables):
    data = _nominal_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    subset_data = _beautify_data_comp(variable.name, comparison_variable.name,
                                      variable, comparison_variable, data)
    
    _dataframe_update_title(subset_data, _dataframe_get_title('Comparative', 'Frequency tables',
                                                                 variable.title, comparison_variable.title))

    return subset_data

def comp_frequency_table(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _comp_frequency_table(classification_variable, comparison_classification_variable)
    _display_data(data)

## Stacked Bar Plots

def _comp_stacked_bar_plot(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables):
    data = _nominal_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    subset_data = _beautify_data_comp(variable.name, comparison_variable.name,
                                      variable, comparison_variable, data)

    title = f"{variable.title} and {comparison_variable.title} ~ Stacked bar plot"

    if subset_data.empty: return plt.title(title)

    # Pivot the data to get a matrix form
    pivoted_data = subset_data.pivot(index=variable.name, columns=comparison_variable.name, values='Frequency')

    # Replace NaN values with 0
    pivoted_data = pivoted_data.replace('', np.nan).fillna(0)

    fig, ax = plt.subplots(figsize=(10, 6))

    # Bottom value for stacking
    bottom_value = pd.Series([0] * pivoted_data.shape[0], index=pivoted_data.index)

    for col in pivoted_data.columns:
        plt.bar(pivoted_data.index, pivoted_data[col], bottom=bottom_value, label=col)
        bottom_value += pivoted_data[col]

    plt.title(title)
    plt.xlabel(variable.title)
    plt.ylabel('Frequency')
    _configure_seaborn_legend(comparison_variable.title, ax, plt)

    return fig

def comp_stacked_bar_plot(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _comp_stacked_bar_plot(classification_variable, comparison_classification_variable)
    _display_figure(data)

## Grouped Bar Plots

def _comp_grouped_bar_plot(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables):
    data = _nominal_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    subset_data = _beautify_data_comp(variable.name, comparison_variable.name,
                                      variable, comparison_variable, data)
    
    title = f"{variable.title} and {comparison_variable.title} ~ Grouped bar plot"

    if subset_data.empty: return plt.title(title)

    fig, ax = plt.subplots(figsize=(10, 6))
    sns.barplot(x=variable.name, y='Frequency', hue=comparison_variable.name, data=subset_data)

    plt.title(title)
    plt.gca().set_xlabel('')
    plt.ylabel('Frequency')
    _configure_seaborn_legend(comparison_variable.title, ax, plt)

    return fig

def comp_grouped_bar_plot(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _comp_grouped_bar_plot(classification_variable, comparison_classification_variable)
    _display_figure(data)

## Bubble Charts

def _comp_bubble_chart(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables):
    data = _nominal_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    subset_data = _beautify_data_comp(variable.name, comparison_variable.name,
                                      variable, comparison_variable, data)

    title = f"{variable.title} and {comparison_variable.title} ~ Bubble Chart"

    if subset_data.empty: return plt.title(title)

    # Creating the bubble chart
    fig, ax = plt.subplots(figsize=(10, 6))
    size = 'Frequency'
    sns.scatterplot(data=subset_data, x=variable.name, y=comparison_variable.name, size=size, color='black')

    # Adding labels and title
    plt.title(title)
    plt.gca().set_xlabel('')
    plt.gca().set_ylabel('')
    _configure_seaborn_legend(size, ax, plt)

    return fig

def comp_bubble_chart(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _comp_bubble_chart(classification_variable, comparison_classification_variable)
    _display_figure(data)

## Chi-squared test

def _comp_chi_squared_test(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables):
    data = _nominal_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    subset_data = _beautify_data_comp(variable.name, comparison_variable.name,
                                      variable, comparison_variable, data)
    
    df_title = _dataframe_get_title('Comparative', 'Chi-squared test',
                                    variable.title, comparison_variable.title)

    empty_df = _create_empty_dataframe(df_title, _dataframe_update_title)

    if subset_data.empty: return empty_df

    # Check for the condition where both variables are NaN
    if len(subset_data) == 1 and pd.isna(subset_data[variable.name]).all()  \
    and pd.isna(subset_data[comparison_variable.name]).all():
        return empty_df

    # Create contingency table
    contingency_table = pd.crosstab(subset_data[variable.name], subset_data[comparison_variable.name],
                                     values=subset_data['Frequency'], aggfunc='sum', dropna=False).replace('', np.nan).fillna(0)
   
    # Calculating the Chi-squared statistic
    chi2_result = chi2_contingency(contingency_table)

    subset_data = pd.DataFrame({
        'p-value': chi2_result.pvalue # type: ignore
    }, index=[0])

    _dataframe_update_title(subset_data, df_title)

    return subset_data

def comp_chi_squared_test(classification_variable: NominalVariables,
                              comparison_classification_variable: NominalVariables, show: bool):
    if not show: return
    
    data = _comp_chi_squared_test(classification_variable, comparison_classification_variable)
    _display_data(data)

## Shapiro Wilk's Correlation Test

def _comp_shapiro_wilk_test(classification_variable: ContinuousVariables):
    df = _continuous_dataframe().data

    variable = classification_variable.value

    subset_data = df[variable.name].replace('', np.nan).replace('', np.nan).fillna(0)

    df_title = _dataframe_get_title('Comparative', "Shapiro Wilk's Correlation Test",
                                    variable.title)
    
    empty_df = _create_empty_dataframe(df_title, _dataframe_update_title)

    # Test requires at least 3 samples
    if len(subset_data) <= 2: return empty_df

    shapiro_result = shapiro(subset_data)

    statistics, pvalue =  shapiro_result

    subset_data = pd.DataFrame({
        'statistics': statistics,
        'p-value': pvalue
    }, index=[0])

    _dataframe_update_title(subset_data, df_title)

    return subset_data

def comp_shapiro_wilk_test(classification_variable: ContinuousVariables, show: bool):
    if not show: return
    
    data = _comp_shapiro_wilk_test(classification_variable)
    _display_data(data)

## Pearson's Correlation Test

def _comp_pearson_cor_test(classification_variable: ContinuousVariables,
                              comparison_classification_variable: ContinuousVariables):
    data = _continuous_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    df_title = _dataframe_get_title('Comparative', "Pearson's Correlation Test", variable.title,
                                    comparison_variable.title)

    empty_df = _create_empty_dataframe(df_title, _dataframe_update_title)

    cv_comp_shapiro_wilk_test = _comp_shapiro_wilk_test(classification_variable)
    ccv_comp_shapiro_wilk_test = _comp_shapiro_wilk_test(comparison_classification_variable)
    
    if cv_comp_shapiro_wilk_test is None or ccv_comp_shapiro_wilk_test is None \
        or not _validate_comp_shapiro_wilk_test(cv_comp_shapiro_wilk_test, ccv_comp_shapiro_wilk_test):
        return empty_df

    p_value = cv_comp_shapiro_wilk_test['p-value'][0]
    dp_value = ccv_comp_shapiro_wilk_test['p-value'][0]

    if not (p_value > 0.05 and dp_value > 0.05): return empty_df
    
    # Perform Pearson's correlation test
    pearson_coefficient, p_value = pearsonr(data[variable.name].replace('', np.nan).fillna(0),
                                             data[comparison_variable.name].replace('', np.nan).fillna(0))

    subset_data = pd.DataFrame({
        'pearson coefficient': pearson_coefficient,
        'p-value': p_value
    }, index=[0])

    _dataframe_update_title(subset_data, df_title)

    return subset_data

def comp_pearson_cor_test(classification_variable: ContinuousVariables,
                              comparison_classification_variable: ContinuousVariables, show: bool):
    if not show: return
    
    data = _comp_pearson_cor_test(classification_variable, comparison_classification_variable)
    _display_data(data)

## Spearman's Correlation Test

def _comp_spearman_cor_test(classification_variable: ContinuousVariables,
                              comparison_classification_variable: ContinuousVariables):
    data = _continuous_dataframe().data

    variable = classification_variable.value
    comparison_variable = comparison_classification_variable.value

    df_title = _dataframe_get_title('Comparative', "Spearman's Correlation Test", variable.title,
                                    comparison_variable.title)

    empty_df = _create_empty_dataframe(df_title, _dataframe_update_title)

    cv_comp_shapiro_wilk_test = _comp_shapiro_wilk_test(classification_variable)
    ccv_comp_shapiro_wilk_test = _comp_shapiro_wilk_test(comparison_classification_variable)

    if cv_comp_shapiro_wilk_test is None or ccv_comp_shapiro_wilk_test is None \
        or not _validate_comp_shapiro_wilk_test(cv_comp_shapiro_wilk_test, ccv_comp_shapiro_wilk_test):
        return empty_df
    
    p_value = cv_comp_shapiro_wilk_test['p-value'][0]
    dp_value = ccv_comp_shapiro_wilk_test['p-value'][0]
    
    if  p_value > 0.05 and dp_value > 0.05: return empty_df

    # Perform Spearman's correlation test
    spearman_result = spearmanr(data[variable.name].replace('', np.nan).fillna(0),
                                 data[comparison_variable.name].replace('', np.nan).fillna(0))

    subset_data = pd.DataFrame({
        'statistic': spearman_result.statistic, # type: ignore
        'p-value': spearman_result.pvalue # type: ignore
    }, index=[0])

    _dataframe_update_title(subset_data, df_title)

    return subset_data

def comp_spearman_cor_test(classification_variable: ContinuousVariables,
                              comparison_classification_variable: ContinuousVariables, show: bool):
    if not show: return

    data = _comp_spearman_cor_test(classification_variable, comparison_classification_variable)
    _display_data(data)