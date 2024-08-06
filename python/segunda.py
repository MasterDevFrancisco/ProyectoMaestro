from docx import Document
import docx2pdf

# Paso 1: Abrir y leer el archivo .docx
input_docx = 'ReplaceAllInstances.docx'
output_docx = 'output.docx'
document = Document(input_docx)

# Paso 2: Buscar y eliminar el string especificado
target_string = "Evaluation Warning: The document was created with Spire.Doc for Python."

for paragraph in document.paragraphs:
    if target_string in paragraph.text:
        paragraph.text = paragraph.text.replace(target_string, "")

# Paso 3: Guardar el archivo modificado como .docx
document.save(output_docx)

# Paso 4: Convertir el archivo .docx a .pdf
output_pdf = 'output.pdf'
docx2pdf.convert(output_docx, output_pdf)
