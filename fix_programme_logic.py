import os

directories = [
    r'c:\xampp\htdocs\integ\view\FrontOffice\programme',
    r'c:\xampp\htdocs\integ\view\BackOffice\programme'
]

replacements = {
    'RerégimeC': 'RegimeC',
    'rerégime': 'regime',
    'id_rerégime': 'id_regime',
    'getRerégime': 'getRegime',
    'updateRerégime': 'updateRegime',
    'getPlanningByRerégime': 'getPlanningByRegime',
    'RÃ©régime': 'Régime',
    'rÃ©régime': 'régime',
    'Ã‰tape': 'Étape',
    'Ã  jour': 'à jour',
    'Ã©régime': 'régime',
    'Ã©': 'é',
    'Ã ': 'à',
    'Ãª': 'ê',
    'Ã«': 'ë',
    'Ã®': 'î',
    'Ã¯': 'ï',
    'Ã´': 'ô',
    'Ã¹': 'ù',
    'Ã»': 'û',
    'Ã§': 'ç',
    'Ãˆ': 'È',
    'Ã‰': 'É',
    'Ã€': 'À',
    'Ã': 'à',
    '??': ' ', # Macro labels fix
    'Étape': 'Étape', # Ensure normalization
}

def fix_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        for old, new in replacements.items():
            content = content.replace(old, new)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Fixed: {filepath}")
    except Exception as e:
        print(f"Error fixing {filepath}: {e}")

for directory in directories:
    if os.path.exists(directory):
        for root, dirs, files in os.walk(directory):
            for file in files:
                if file.endswith('.php'):
                    fix_file(os.path.join(root, file))
