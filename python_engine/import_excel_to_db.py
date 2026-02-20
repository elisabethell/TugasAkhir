import pandas as pd
import mysql.connector
import os

# Trik biar Python otomatis nemu file Excel di folder yang bener (Mac/Windows sama aja)
current_dir = os.path.dirname(os.path.abspath(__file__))
excel_path = os.path.join(current_dir, 'Hero.xlsx')

try:
    db = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="db_skripsi"
    )
    cursor = db.cursor()

    df = pd.read_excel(excel_path)
    df = df.fillna('')
    
    df.columns = [c.strip() for c in df.columns]

    for index, row in df.iterrows():
        sql = """
            INSERT INTO master_heroes 
            (nama_hero, role_1, role_2, damage_source, damage_output, spec_1, spec_2, lane_recommendation_1, lane_recommendation_2) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        val = (
            row['nama_hero'], row['role_1'], row['role_2'], 
            row['damage_source'], row['damage_output'], 
            row['spec_1'], row['spec_2'], row['lane_recommendation_1'], row['lane_recommendation_2'] 
        )
        cursor.execute(sql, val)

    db.commit()
    print(f"✅ Hebat! {len(df)} hero berhasil masuk ke database.")

except Exception as e:
    print(f"❌ Error nih: {e}")

finally:
    if 'db' in locals() and db.is_connected():
        cursor.close()
        db.close()