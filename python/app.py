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

@app.route('/valida-campos', methods=['POST'])
def valida_campos():
    data = request.json
    file_path = data.get('file_path')
    campos = data.get('campos')

    if not file_path or not campos:
        return jsonify({'error': 'file_path y campos son requeridos'}), 400

    try:
        # Crear un objeto Document
        document = Document()
        
        # Cargar el documento Word docx o doc
        document.LoadFromFile(file_path)

        # Obtener el texto del documento
        texto_documento = document.GetText()

        # Buscar campos faltantes
        campos_faltantes = [campo for campo in campos if campo not in texto_documento]

        if campos_faltantes:
            return jsonify({'campos_faltantes': campos_faltantes})
        else:
            return jsonify({'message': 'Todos los campos están presentes.'})

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
