import os
import re

# Directory to process
directories = [
    r'c:\xampp\htdocs\integ\view\FrontOffice\programme',
    r'c:\xampp\htdocs\integ\view\BackOffice\programme',
    r'c:\xampp\htdocs\integ\view\FrontOffice\RECETTE',
    r'c:\xampp\htdocs\integ\view\BackOffice\RECETTE'
]

# Replacements for corrupted strings
replacements = {
    'RerǸgimeC': 'RegimeC',
    'rerǸgime': 'regime',
    'RǸgimeC': 'RegimeC',
    'rǸgime': 'regime',
    'RǸgimes': 'Regimes',
    'rǸgimes': 'regimes',
    'id_rerǸgime': 'id_regime',
    'utilisǸ': 'utilise',
    'rǸcupre': 'recupere',
    'requǦte': 'requete',
    'entraǩne': 'entraine',
    'rǸgime associǸ': 'regime associe',
    'dǸtaillǸ': 'detaille',
    'annǟ\'Ń?Tǟ?s\'es': 'années',
    'annǟ\'Ń?Tǟ?s\'e': 'année',
    'Prǟ\'Ń?Tǟ?s\'diction': 'Prédiction',
    'prǟ\'Ń?Tǟ?s\'diction': 'prédiction',
    'Mǟ\'Ń?Tǟ?s\'tabolique': 'Métabolique',
    'mǟ\'Ń?Tǟ?s\'tabolique': 'métabolique',
    'd\'ǟ\'Ń?Tǟǽ?s\'volution': 'd\'évolution',
    'Dǟ\'Ń?Tǟ?s\'couvrez': 'Découvrez',
    'dǟ\'Ń?Tǟ?s\'couvrez': 'découvrez',
    'prǟ\'Ń?Tǟ?s\'cis': 'précis',
    'donnǟ\'Ń?Tǟ?s\'es': 'données',
    'rǟ\'Ń?Tǟ?s\'el': 'réel',
    'Rǟ\'Ń?Tǟ?s\'sultat': 'Résultat',
    'rǟ\'Ń?Tǟ?s\'sultat': 'résultat',
    'Rǟ\'Ń?Tǟ?s\'rǸgime': 'Régime',
    'rǟ\'Ń?Tǟ?s\'rǸgime': 'régime',
    'prǟ\'Ń?Tǟǽ?s\'dicte': 'prédicte',
    'ǟ\'Ń?Tǟǽ?s.ge': 'Âge',
    'annǟ\'Ń?Tǟ?s\'es': 'années',
    'prǟ\'Ń?Tǟǽ?s\'diction': 'prédiction',
    'aprǟ\'Ń?Tǟ?s': 'après',
    'crǸation': 'creation',
    '%tape': 'Étape',
    'tape': 'Étape',
    'ǟ\'' : ' ', # generic catch-all for bad sequences
}

def fix_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        for old, new in replacements.items():
            content = content.replace(old, new)
        
        # Also fix the weird icons if they are corrupted
        content = re.sub(r'<span>Y.*?</span>', '<span>🥗</span>', content) # example fix for icons
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Fixed: {filepath}")
    except Exception as e:
        print(f"Error fixing {filepath}: {e}")

for directory in directories:
    if os.path.exists(directory):
        for root, dirs, files in os.walk(directory):
            for file in files:
                if file.endswith('.php') or file.endswith('.js') or file.endswith('.css'):
                    fix_file(os.path.join(root, file))
