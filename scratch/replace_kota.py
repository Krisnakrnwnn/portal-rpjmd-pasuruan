import os
import re

directories_to_scan = [
    'resources/views',
    'app',
    'database/seeders',
    'public',
    'routes',
    'config'
]
base_dir = r"d:\PBL\pbls6"

def replace_in_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
            
        new_content = content
        
        # Replace variations of Kota Pasuruan
        replacements = [
            (r'\bKota Pasuruan\b', 'Kabupaten Pasuruan'),
            (r'\bkota pasuruan\b', 'kabupaten pasuruan'),
            (r'\bKOTA PASURUAN\b', 'KABUPATEN PASURUAN'),
            (r'\bPemkot Pasuruan\b', 'Pemkab Pasuruan'),
            (r'\bpemkot pasuruan\b', 'pemkab pasuruan'),
            (r'\bPemerintah Kota Pasuruan\b', 'Pemerintah Kabupaten Pasuruan'),
            (r'\bpemerintah kota pasuruan\b', 'pemerintah kabupaten pasuruan'),
            (r'\bpemerintah kota\b', 'pemerintah kabupaten'),
            (r'\bPemerintah Kota\b', 'Pemerintah Kabupaten'),
            (r'Visi Misi Kota', 'Visi Misi Kabupaten'),
            (r'Misi Kota', 'Misi Kabupaten'),
        ]

        for old, new in replacements:
            new_content = re.sub(old, new, new_content)

        if new_content != content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Updated: {filepath}")
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

for directory in directories_to_scan:
    dir_path = os.path.join(base_dir, directory)
    if os.path.exists(dir_path):
        for root, dirs, files in os.walk(dir_path):
            for file in files:
                if file.endswith(('.php', '.js', '.json', '.html', '.env', '.txt')):
                    filepath = os.path.join(root, file)
                    replace_in_file(filepath)
