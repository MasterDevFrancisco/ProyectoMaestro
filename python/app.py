from flask import Flask, request, jsonify
from spire.doc import Document, FileFormat
import subprocess

app = Flask(__name__)

@app.route('/your-endpoint', methods=['POST'])
def handle_data():
    data = request.json
    print(data)
    process_data(data)
    
    # Ejecutar el script adicional
    result = subprocess.run(['python', 'segunda.py'], capture_output=True, text=True)
    
    if result.returncode == 0:
        return jsonify({'status': 'success', 'script_output': result.stdout}), 200
    else:
        return jsonify({'status': 'failure', 'error': result.stderr}), 500

def process_data(data):
    # Crear un objeto Document
    document = Document()
    # Cargar un documento Word docx o doc
    document.LoadFromFile("test.docx")
    # document.LoadFromFile("Template1.doc")

    # Iterar sobre las tablas y sus campos
    for table, fields in data.items():
        replacements = fields
        # Buscar y reemplazar cada texto
        for old_text, new_text in replacements.items():
            document.Replace(old_text, new_text, False, False)

    # Guardar el documento resultante
    document.SaveToFile("ReplaceAllInstances.docx", FileFormat.Docx2016)
    document.Close()

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
