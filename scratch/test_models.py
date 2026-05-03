import pandas as pd
import os
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler, PolynomialFeatures
from sklearn.ensemble import RandomForestRegressor, ExtraTreesRegressor
from xgboost import XGBRegressor
from sklearn.metrics import r2_score

df = pd.read_csv('sleeptime_prediction_dataset.csv')
X = df.drop(columns=['SleepTime'])
y = df['SleepTime']
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
sc = StandardScaler()
X_train_s = sc.fit_transform(X_train)
X_test_s = sc.transform(X_test)

results = {}
for name, m in {
    'RF': RandomForestRegressor(n_estimators=500, random_state=42),
    'ET': ExtraTreesRegressor(n_estimators=500, random_state=42),
    'XGB': XGBRegressor(n_estimators=500, random_state=42)
}.items():
    m.fit(X_train_s, y_train)
    results[name] = r2_score(y_test, m.predict(X_test_s))

print(results)
