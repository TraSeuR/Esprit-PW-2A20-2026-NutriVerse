import os

# Target directories
directories = [
    r'c:\xampp\htdocs\integ\view\FrontOffice\programme',
    r'c:\xampp\htdocs\integ\view\BackOffice\programme'
]

# Replacements to fix logic and corruption
replacements = {
    'RerégimeC': 'RegimeC',
    'rerégime': 'regime',
    'id_rerégime': 'id_regime',
    'getRerégime': 'getRegime',
    'updateRerégime': 'updateRegime',
    'deleteRerégime': 'deleteRegime',
    'getPlanningByRerégime': 'getPlanningByRegime',
    'last_id_rerégime': 'last_id_regime',
    'id_regime_rerégime': 'id_regime', # Some potential double corruption
    'Étape': 'Étape',
    'É': 'É',
    'tapez': 'Tapez',
    '': ' ', # Remove any remaining weird characters
}

def fix_file(filepath):
    try:
        # Read with UTF-8, ignore errors to handle corruption gracefully
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Apply replacements
        for old, new in replacements.items():
            content = content.replace(old, new)
        
        # Specific fix for the summary.php macro labels if they are still broken
        content = content.replace('<span>??</span>', '<span>VALEUR</span>')
        
        # Write back
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Verified & Fixed: {filepath}")
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

for directory in directories:
    if os.path.exists(directory):
        for root, dirs, files in os.walk(directory):
            for file in files:
                if file.endswith('.php'):
                    fix_file(os.path.join(root, file))
