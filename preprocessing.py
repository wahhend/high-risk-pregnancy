import pandas as pd
import numpy as np
from sklearn.metrics.pairwise import euclidean_distances

class Data:
    def __init__(self, features, label):
        self.features = features
        self.label = label

def read_dataset(filename):
    dataframe = pd.read_csv(filename, sep=';')

    # dataframe.drop(['ID', 'DESA', 'NAMA', 'UMUR KEHAMILAN(MINGGU)', 'BB', 'HB', 'POINT'], axis=1, inplace=True)
    dataframe.drop(['nama_istri', 'hemoglobin', 'riwayat_melahirkan', 'gagal_hamil', 'skor'], axis=1, inplace=True)

    return dataframe

def cleaning(df):
    # df['JARAK KEHAMILAN(THN)'].fillna(0, inplace=True)
    df.dropna(inplace=True)

    label = df['kategori']
    df.drop(['kategori'], axis=1, inplace=True)
    df = df.astype(float)
    # df['RISIKO'] = label
    
    return df, label

def normalize(df):
    # df = pd.to_numeric(df)
    # label = df['RISIKO']
    # df.drop(['RISIKO'], axis=1, inplace=True)
    
    norm_df = (df - df.min()) / (df.max() - df.min())
    # norm_df['RISIKO'] = label

    return norm_df


df = read_dataset('data/DATA_LATIH_ext.csv')
print(df.head(10))
df = df.sample(frac=1)
df, label = cleaning(df)
print(df)
norm_df = normalize(df)
# print(norm_df.head(10))

df.to_csv('data/data.csv', index=False)
label.to_csv('data/label.csv', header=False, index=False)
norm_df.to_csv('data/data_normalized.csv', index=False)
