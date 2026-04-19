import re

with open('/Users/esweb/Documents/OtgestApp/app/app/Views/dashboard/admin.php', 'r') as f:
    orig = f.read()

# The presence row
pattern1 = r'(    <!-- \n    // ---------------------------------------------------------------------------------\n    // FILA 1: KPIs DE PRESENCIA \(EN VIVO\)\n    // ---------------------------------------------------------------------------------\n    -->\n    <div class="row g-4 mb-4">.*?    </div>\n)'

# The alerts row
pattern2 = r'(    <!-- \n    // ---------------------------------------------------------------------------------\n    // FILA 2: ALERTAS DE GESTIÓN \(PENDIENTES\)\n    // ---------------------------------------------------------------------------------\n    -->\n    <div class="row g-4 mb-4">.*?    </div>\n)'

p1_match = re.search(pattern1, orig, flags=re.DOTALL)
p2_match = re.search(pattern2, orig, flags=re.DOTALL)

if p1_match and p2_match:
    p1_str = p1_match.group(1).replace('FILA 1', 'FILA 2')
    p2_str = p2_match.group(1).replace('FILA 2', 'FILA 1')
    
    # replace them
    new_text = orig.replace(p1_match.group(1), p2_str).replace(p2_match.group(1), p1_str)
    
    with open('/Users/esweb/Documents/OtgestApp/app/app/Views/dashboard/admin.php', 'w') as f:
        f.write(new_text)
    print("Swapped successfully")
else:
    print("Could not match")
