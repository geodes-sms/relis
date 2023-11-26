import re
import json
import numpy as np
import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt
from enum import Enum
from typing import Type
from matplotlib import ticker
#from FisherExact import fisher_exact
from statsmodels.robust.scale import mad
from scipy.stats import kurtosis, skew, shapiro, spearmanr, pearsonr 

### Config

plt.rcParams['figure.max_open_warning'] = 0

class Multivalue(Enum):
    SEPARATOR = '|'

class Policies(Enum):
    DROPNA = False

### Types

class FieldClassificationType(Enum):
    NOMINAL = 'Nominal'
    CONTINUOUS = 'Continuous'

class Variable:
    def __init__(self, name: str, title: str, type: FieldClassificationType, multiple: bool):
        self.name = name
        self.title = title
        self.type = type
        self.multiple = multiple

class NominalVariables(Enum):
    venue = Variable("venue", "Venue", FieldClassificationType.NOMINAL, False)
    search_type = Variable("search_type", "Search Type", FieldClassificationType.NOMINAL, False)
    domain = Variable("domain", "Domain", FieldClassificationType.NOMINAL, False)
    transformation_language = Variable("transformation_language", "Transformation Language", FieldClassificationType.NOMINAL, True)
    source_language = Variable("source_language", "Source language", FieldClassificationType.NOMINAL, False)
    target_language = Variable("target_language", "Target language", FieldClassificationType.NOMINAL, False)
    scope = Variable("scope", "Scope", FieldClassificationType.NOMINAL, True)
    industrial = Variable("industrial", "Industrial", FieldClassificationType.NOMINAL, False)
    bidirectional = Variable("bidirectional", "Bidirectional", FieldClassificationType.NOMINAL, False)

class ContinuousVariables(Enum):
    publication_year = Variable("publication_year", "Publication year", FieldClassificationType.CONTINUOUS, False)
    targeted_year = Variable("targeted_year", "Targeted year", FieldClassificationType.CONTINUOUS, False)

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

### Shared

def removeEmptyStrings(df: pd.DataFrame):
    df.replace('', np.nan, inplace=True)

def get_variable(field_name: str, variables) -> Variable:
    return variables[field_name].value

def split_multiple_values(value):
    if not pd.isna(value):
        return [item.strip() for item in re.split(rf'\{Multivalue.SEPARATOR.value}', value)] 
    return [value]

def process_multiple_values(values: pd.Series, multiple: bool):
    if (multiple):
        return values.apply(lambda x: split_multiple_values(x))

    return values.apply(lambda x: [x])

def display_data(dataFrame: pd.DataFrame, bool: bool):
    if bool: print(dataFrame)

def display_figure(plt, bool: bool):
    if bool: plt.show()

### Data

## Parsing (only for demonstration purposes)

with open('../data/relis_classification_rsc_CV.json', 'r', encoding='utf8') as f:
   classification_data: list[dict[str, str]] =  json.loads(f.read())

## Preprocessing

# Split config file based on data type
def filter_row_by_field_type(paper, field_type):
    pd_row = {key: value["value"] for key, value in paper.items() if value['type'] == field_type}
    return pd_row

nominal_data = pd.DataFrame([filter_row_by_field_type(paper, FieldClassificationType.NOMINAL.value) for paper in classification_data])
continuous_data = pd.DataFrame([filter_row_by_field_type(paper, FieldClassificationType.CONTINUOUS.value) for paper in classification_data])

nominal = NominalDataFrame(nominal_data, NominalVariables)
continuous = ContinuousDataFrame(continuous_data, ContinuousVariables)

if (Policies.DROPNA.value):
    removeEmptyStrings(nominal.data)
    removeEmptyStrings(continuous.data)

### DESCRIPTIVE STATS

## Util

def beautify_data_desc(field_name: str, data: pd.DataFrame):
    # Get metadata
    variable = get_variable(field_name, NominalVariables)

    # Split the values by the "|" character and flatten the result
    split_values = process_multiple_values(data[field_name], variable.multiple)
    flattened_values = np.concatenate(split_values)

    # Generate the frequency table
    freq_table = pd.Series(flattened_values, dtype=str).value_counts().reset_index()
    freq_table.columns = ['value', 'n']

    # Calculate the percentage
    freq_table['percentage'] = (freq_table['n'] / freq_table['n'].sum()) * 100

    return freq_table

## Frequency tables

desc_distr_vector = {NominalVariables[field_name]: beautify_data_desc(field_name, nominal.data)
                      for field_name in nominal.data.columns}

## Bar plots

def generate_bar_plot(field_name: str, data: pd.DataFrame):
    df = beautify_data_desc(field_name, data)
    
    if (len(df) == 0): return

    # Set the theme
    sns.set_theme(style="whitegrid")

    # Create the plot
    fig, ax = plt.subplots(figsize=(10, 6))
    sns.barplot(data=df, x="value", y="percentage", hue="n")

    # Get metadata
    variable = get_variable(field_name, NominalVariables)

    # Set labels and title
    title = f"{variable.title} ~ Bar plot"
    plt.title(title)
    plt.xlabel(variable.title)
    plt.ylabel("Percentage")

    return fig

bar_plot_vector = {NominalVariables[field_name]: generate_bar_plot(field_name, nominal.data)
                    for field_name in nominal.data.columns}

## Statistics

def generate_statistics(field_name: str, data: pd.DataFrame):
    series =  data[field_name]
    
    series.replace('', np.nan, inplace=True)
    
    if (len(data) == 0): return

    nan_policy = 'omit' if Policies.DROPNA.value else 'propagate'
    results = {
    "vars": 1,
    "n": series.count(),
    "mean": series.mean(),
    "sd": series.std(),
    "median": series.median(),
    "trimmed": series[series.between(series.quantile(0.25), series.quantile(0.75))].mean(),
    "mad": mad(series),
    "min": series.min(),
    "max": series.max(),
    "range": series.max() - series.min(),
    "skew": skew(series, nan_policy=nan_policy),
    "kurtosis": kurtosis(series, nan_policy=nan_policy, fisher=True),
    "se": series.std() / np.sqrt(series.count())  
    }
    return results

statistics_vector = {ContinuousVariables[field_name]: generate_statistics(field_name, continuous.data)
                      for field_name in continuous.data.columns}

## Box Plots

def generate_box_plot(field_name: str, data: pd.DataFrame):
    series = data[field_name]

    variable = get_variable(field_name, ContinuousVariables)

    # Create the box plot
    fig, ax = plt.subplots(figsize=(10, 6))
    sns.boxplot(data=series, color='lightblue')

    # Overlay the mean point
    mean_value = series.mean()
    plt.scatter(x=0, y=mean_value, color='red', s=50, zorder=3)  # s is the size of the point

    # Set the title and labels
    title = f"{variable.title} ~ Box plot"
    plt.title(title)
    plt.ylabel(variable.title)
    plt.xlabel('')

    plt.gca().yaxis.set_major_formatter(ticker.FormatStrFormatter('%0.0f'))

    return fig

box_plot_vector = {ContinuousVariables[field_name]: generate_box_plot(field_name, continuous.data)
                    for field_name in continuous.data.columns}

## Violin Plots

def generate_violin_plot(field_name: str, data: pd.DataFrame):
    series = data[field_name]
    
    variable = get_variable(field_name, ContinuousVariables)

    fig, ax = plt.subplots(figsize=(10, 6))
    sns.violinplot(data=series, color="lightgray")

    plt.title(f"{variable.title} ~ Violin plot")
    plt.ylabel(variable.title)
    plt.xlabel("Density")
    plt.xticks([])

    return fig

violin_plot_vector = {ContinuousVariables[field_name]: generate_violin_plot(field_name, continuous.data)
                       for field_name in continuous.data.columns}

### EVOLUTIVE STATS

## Util

def beautify_data_evo(field_name: str, publication_year: pd.Series, variable: Variable, data: pd.DataFrame):
    series = data[field_name]
    
    # Create new DataFrame with specified columns
    subset_data = pd.DataFrame({
        'Year': publication_year,
        'Value': process_multiple_values(series, variable.multiple)
    })
    
    subset_data = subset_data.explode('Value')

    # Remove rows with empty values
    subset_data = subset_data[(subset_data['Value'] != '')]

    subset_data = subset_data.groupby(['Year', 'Value']).size().reset_index(name='Frequency')

    return subset_data

## Frequency tables

def expand_data(field_name: str, publication_year: pd.Series, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)

    subset_data = beautify_data_evo(field_name, publication_year, variable, data)

    # Pivoting the data
    subset_data = subset_data.pivot(index='Year', columns='Value', values='Frequency').fillna(0)

    subset_data.columns.name = None
    subset_data.reset_index(inplace=True)

    return subset_data 

evo_distr_vector = {NominalVariables[field_name]: expand_data(field_name, continuous.data["publication_year"], nominal.data)
                       for field_name in nominal.data.columns}

## Evolution Plots

def generate_evo_plot(field_name: str, publication_year: pd.Series, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)
    
    subset_data = beautify_data_evo(field_name, publication_year, variable, data)

    # Create a plot
    fig, ax = plt.subplots(figsize=(10, 6))
    sns.lineplot(data=subset_data, x='Year', y='Frequency', hue='Value', style='Value', markers=True)

    # Setting title, labels, and theme
    plt.title(f"{variable.title} ~ Evolution plot")
    plt.xlabel('Year')
    plt.ylabel('Frequency')
    plt.grid(True)
    return fig

evolution_plot_vector = {NominalVariables[field_name]: generate_evo_plot(field_name, continuous.data["publication_year"], nominal.data)
                          for field_name in nominal.data.columns}

### COMPARATIVE STATS

## Util

def beautify_data_comp(field_name: str, dependency_field_name: str,
                        variable: Variable, dependency_variable: Variable, data: pd.DataFrame):    
    subset_data = pd.DataFrame({
        field_name: data[field_name],
        dependency_field_name: data[dependency_field_name]
    })
    
    # Filtering out rows where any of the variables is empty
    subset_data = subset_data[(subset_data[field_name] != "") & (subset_data[dependency_field_name] != "")]

    # Splitting the strings and expanding into separate rows
    subset_data[field_name] = process_multiple_values(subset_data[field_name], variable.multiple)
    subset_data = subset_data.explode(field_name)

    subset_data[dependency_field_name] = process_multiple_values(subset_data[dependency_field_name],
                                                                  dependency_variable.multiple)
    subset_data = subset_data.explode(dependency_field_name)

    # Counting occurrences
    subset_data = subset_data.groupby([field_name, dependency_field_name]).size().reset_index(name='Frequency')

    return subset_data

def evaluate_comparative_dependency_field(field_name: str, dataFrame: DataFrame, strategy):
    """
    Perform a statistical analysis strategy for each 
    dependency field of a given classification field.
    Act as a wrapper for the comparative statistical
    functions
    """
    field_names = list(dataFrame.data.columns)

    return {dataFrame.variable_type[dependency_field_name]: strategy(field_name, dependency_field_name, dataFrame.data)
             for dependency_field_name in field_names if dependency_field_name != field_name}

## Frequency Tables

def generate_comparative_violin_plot(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)
    dependency_variable = get_variable(dependency_field_name, NominalVariables)

    return beautify_data_comp(field_name, dependency_field_name,
                                      variable, dependency_variable, data)

comp_distr_vector = {NominalVariables[field_name]: evaluate_comparative_dependency_field(field_name, nominal, generate_comparative_violin_plot)
                       for field_name in nominal.data.columns}

## Bar Plots

def generate_stacked_bar_plot(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)
    dependency_variable = get_variable(dependency_field_name, NominalVariables)

    subset_data = beautify_data_comp(field_name, dependency_field_name,
                                      variable, dependency_variable, data)

    if subset_data.empty: return

    # Pivot the data to get a matrix form
    pivoted_data = subset_data.pivot(index=field_name, columns=dependency_field_name, values='Frequency')

    # Replace NaN values with 0
    pivoted_data = pivoted_data.fillna(0)

    fig, ax = plt.subplots(figsize=(10, 6))

    # Bottom value for stacking
    bottom_value = pd.Series([0] * pivoted_data.shape[0], index=pivoted_data.index)

    for col in pivoted_data.columns:
        plt.bar(pivoted_data.index, pivoted_data[col], bottom=bottom_value, label=col)
        bottom_value += pivoted_data[col]

    plt.title(f"{variable.title} and {dependency_variable.title} ~ Stacked bar plot")
    plt.xlabel(variable.title)
    plt.ylabel('Frequency')
    plt.legend(title=dependency_field_name)

    return fig

stacked_bar_plot_vector = {NominalVariables[field_name]: evaluate_comparative_dependency_field(field_name, nominal, generate_stacked_bar_plot)
                       for field_name in nominal.data.columns}

## Grouped Bar Plots

def generate_grouped_bar_plot(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)
    dependency_variable = get_variable(dependency_field_name, NominalVariables)

    subset_data = beautify_data_comp(field_name, dependency_field_name,
                                      variable, dependency_variable, data)

    if subset_data.empty: return

    fig, ax = plt.subplots(figsize=(10, 6))
    sns.barplot(x=field_name, y='Frequency', hue=dependency_field_name, data=subset_data, dodge=True)

    plt.title(f"{variable.title} and {dependency_variable.title} ~ Grouped bar plot")
    plt.gca().set_xlabel('')
    plt.ylabel('Frequency')

    return fig

grouped_bar_plot_vector = {NominalVariables[field_name]: evaluate_comparative_dependency_field(field_name, nominal, generate_grouped_bar_plot)
                       for field_name in nominal.data.columns}

## Bubble Charts

def generate_bubble_chart(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)
    dependency_variable = get_variable(dependency_field_name, NominalVariables)

    subset_data = beautify_data_comp(field_name, dependency_field_name,
                                      variable, dependency_variable, data)

    if subset_data.empty: return

    # Creating the bubble chart
    fig, ax = plt.subplots(figsize=(10, 6))
    sns.scatterplot(data=subset_data, x=field_name, y=dependency_field_name, size='Frequency', color='black')

    # Adding labels and title
    plt.title(f"{variable.title} and {dependency_variable.title} ~ Bubble Chart")
    plt.gca().set_xlabel('')
    plt.gca().set_ylabel('')

    return fig

bubble_chart_vector = {NominalVariables[field_name]: evaluate_comparative_dependency_field(field_name, nominal, generate_bubble_chart)
                       for field_name in nominal.data.columns}

## Fisher's Exact Test

def fisher_exact_test(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    variable = get_variable(field_name, NominalVariables)
    dependency_variable = get_variable(dependency_field_name, NominalVariables)

    subset_data = beautify_data_comp(field_name, dependency_field_name,
                                      variable, dependency_variable, data)

    if subset_data.empty: return

    # Check for the condition where there's only one row and both variables are NaN
    if len(subset_data) == 1 and pd.isna(subset_data[field_name]).all() and pd.isna(subset_data[dependency_field_name]).all():
        return

    # Create contingency table
    contingency_table = pd.crosstab(subset_data[field_name], subset_data[dependency_field_name],
                                     values=subset_data['Frequency'], aggfunc='sum', dropna=False).fillna(0)

    # Perform Fisher's Exact Test
    fisher_result = fisher_exact(contingency_table, simulate_pval=True)

    # return fisher_result
    return fisher_result

fisher_exact_test_vector = {NominalVariables[field_name]: evaluate_comparative_dependency_field(field_name, nominal, fisher_exact_test)
                       for field_name in nominal.data.columns}

## Shapiro Wilk's Correlation Test

def shapiro_wilk_test(field_name: str, continuous_df: pd.DataFrame):
    subset_data = continuous_df[field_name].fillna(0)

    shapiro_result = shapiro(subset_data)

    return shapiro_result

shapiro_wilk_test_vector = {ContinuousVariables[field_name]: shapiro_wilk_test(field_name, continuous.data)
                          for field_name in continuous.data.columns}

## Pearson's Correlation Test

def pearson_cor_test(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    _, pvalue = shapiro_wilk_test_vector[ContinuousVariables[field_name]]
    _, dpvalue = shapiro_wilk_test_vector[ContinuousVariables[dependency_field_name]]

    if not (pvalue > 0.05 and dpvalue > 0.05): return
    
    # Perform Pearson's correlation test
    pearson_coefficient, p_value = pearsonr(data[field_name].fillna(0), data[dependency_field_name].fillna(0))

    return pearson_coefficient, p_value

pearson_cor_test_vector = {ContinuousVariables[field_name]: evaluate_comparative_dependency_field(field_name, continuous, pearson_cor_test)
                       for field_name in continuous.data.columns}

## Spearman's Correlation Test

def spearman_cor_test(field_name: str, dependency_field_name: str, data: pd.DataFrame):
    _, pvalue = shapiro_wilk_test_vector[ContinuousVariables[field_name]]
    _, dpvalue = shapiro_wilk_test_vector[ContinuousVariables[dependency_field_name]]

    if  pvalue > 0.05 and dpvalue > 0.05: return
  
    # Perform Spearman's correlation test
    spearman_result = spearmanr(data[field_name].fillna(0), data[dependency_field_name].fillna(0))

    return spearman_result

spearman_cor_test_vector = {ContinuousVariables[field_name]: evaluate_comparative_dependency_field(field_name, continuous, spearman_cor_test)
                       for field_name in continuous.data.columns}