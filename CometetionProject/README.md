# CometetionProject
This project have been made by 3 students from Computers and DataScience University
# Cover Type Prediction Project

This project aims to predict the forest cover type based on cartographic variables. The dataset, sourced from the UCI Machine Learning Repository, contains information about wilderness areas, soil types, and various geographical measurements.

## Libraries Used

-   **Data Handling:** pandas
-   **Numerical Operations:** numpy
-   **Data Fetching:** gzip, urllib, os, tarfile
-   **Visualization:** matplotlib, seaborn
-   **Data Preprocessing:** scikit-learn (SimpleImputer, StandardScaler, OneHotEncoder, OrdinalEncoder, ColumnTransformer, Pipeline)
-   **Dimensionality Reduction:** scikit-learn (LinearDiscriminantAnalysis)

## Data Source

The dataset is the Forest CoverType Data from the UCI Machine Learning Repository:

[https://archive.ics.uci.edu/ml/machine-learning-databases/covtype/](https://archive.ics.uci.edu/ml/machine-learning-databases/covtype/)

## Data Loading

The project includes functions to:

-   Download the gzipped dataset (`covtype.data.gz`).
-   Extract the data into a CSV file (`covtype.data`).
-   Load the CSV data into a pandas DataFrame with appropriate column names.

## Exploratory Data Analysis (EDA)

The initial EDA steps involved:

-   Loading the dataset into a pandas DataFrame.
-   Displaying the first few rows of the DataFrame using `head()`.
-   Examining the column names using `columns`.
-   Checking for missing values using `isna().sum()`, which revealed no missing data in this initial load.

## Data Preprocessing (Planned)

The project outlines potential data preprocessing steps, including:

-   **Handling Missing Values:** Strategies like dropping rows/columns or imputing with the median (using pandas or `SimpleImputer` from scikit-learn) are considered, although no missing values were found initially.
-   **Encoding Categorical Features:**
    -   `OrdinalEncoder` for ordered categorical data (if applicable).
    -   `OneHotEncoder` for nominal categorical data (like 'Wilderness Area' and 'Soil Type', though they are already binary).
-   **Feature Scaling:** Using `StandardScaler` to standardize numerical features.
-   **Creating Pipelines:** Employing `Pipeline` and `ColumnTransformer` from scikit-learn to streamline the preprocessing steps for different types of columns.

## Data Preprocessing and Model Training

Following the EDA, the data was prepared for model training using the following steps:

### Feature Encoding and Scaling

-   **Categorical Features:** The 'Wilderness Area' and 'Soil Type' columns (categorical features) were passed through without explicit encoding using `"passthrough"` in the `ColumnTransformer`. This is because these features are already represented as binary (0 or 1) numerical values.
-   **Numerical Features:** The first 10 numerical features were processed using a pipeline that included:
    -   **Imputation:** Missing values were imputed using the median strategy (`SimpleImputer`). Although no missing values were found in the initial EDA, this step ensures robustness.
    -   **Scaling:** Numerical features were standardized using `StandardScaler` to have zero mean and unit variance.
-   **Full Pipeline:** A `ColumnTransformer` was used to apply these transformations to the appropriate columns.
-   **Dimensionality Reduction:** Linear Discriminant Analysis (LDA) was applied to the entire preprocessed dataset, reducing the number of features to 2.

### Data Splitting

The dataset was split into training and testing sets using a custom `split_train_test` function with an 80/20 ratio and a fixed random seed (42) for reproducibility.

### Data Preparation for Modeling

The preprocessing pipeline (`full_pipeline`) was fitted on the training data to learn the scaling and LDA transformations. These transformations were then applied to both the training and testing sets.

### Class Grouping for Modeling

Similar to the EDA, the target variable `Cover_Type` was consolidated into three classes:

| Original Class | New Class |
| -------------- | --------- |
| 1              | 1         |
| 2              | 2         |
| 3–7            | Other (3) |

This was done to address class imbalance and simplify the classification task.

### Data Splitting for Model Training

The features (X) and the grouped target variable (y) were separated. The data was then split into training and testing sets using `train_test_split` with a 75/25 ratio, stratified sampling to maintain class proportions, and a random seed of 42.

### Feature Scaling for Model Training

The numerical features in the training and testing sets were scaled using `StandardScaler`. The scaler was fitted on the training data and then applied to both training and testing sets to prevent data leakage.

### Applying LDA for Model Training

Linear Discriminant Analysis (LDA) was applied to the scaled training and testing features to reduce dimensionality to 2 components.

### Handling Class Imbalance (SMOTE)

Synthetic Minority Over-sampling Technique (SMOTE) was used on the LDA-transformed training data to address the class imbalance by creating synthetic samples for the minority classes.

### Hyperparameter Tuning (SVM)

A Support Vector Machine (SVM) classifier was chosen for modeling. Hyperparameter tuning was performed using `RandomizedSearchCV` with 5-fold cross-validation to find the optimal combination of parameters:

-   `C`: Regularization parameter
-   `gamma`: Kernel coefficient
-   `kernel`: Type of kernel (rbf, poly, sigmoid)
-   `class_weight`: Balancing class weights

The tuning process was monitored using `tqdm` to track progress.

### Model Evaluation

The best SVM model found through hyperparameter tuning was evaluated on the LDA-transformed test set. The evaluation metrics included accuracy and a classification report (precision, recall, F1-score, support).

### Model Saving

The best trained SVM model was saved to a file named `model.pkl` using `joblib` for later use.
