from spire.doc import Document, FileFormat

def process_data(data):
    try:
        # Crear un objeto Document
        document = Document()
        
        # Cargar el documento Word docx o doc
        file_path = "C:/laragon/www/ProyectoMaestro/public/storage/public/formatos/F-005 POLÍTICA DE SEGURIDAD INDUSTRIAL (ELEMENTO I).docx"
        document.LoadFromFile(file_path)

        # Iterar sobre los campos para buscar
        texto_documento = document.GetText()
        campos = ['__politica__', '__permiso_cre__', 'test3']
        campos_faltantes = [campo for campo in campos if campo not in texto_documento]

        if campos_faltantes:
            print('Campos faltantes:', campos_faltantes)
        else:
            print('Todos los campos están presentes.')

    except Exception as e:
        print('Error:', str(e))

if __name__ == '__main__':
    # Ejemplo de datos que podrías recibir
    example_data = {
        'table1': {
            'old_text1': 'new_text1',
            'old_text2': 'new_text2'
        }
    }
    
    process_data(example_data)
