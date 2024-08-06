from spire.doc import *
from spire.doc.common import *

# Crear un objeto Document
document = Document()
# Cargar un documento Word docx o doc
document.LoadFromFile("test.docx")
# document.LoadFromFile("Template1.doc")

# Definir los textos a buscar y sus reemplazos
replacements = {
    "__permiso_cre__": "123456",
    "__politica__": "Política de Privacidad",
    "__numero_permiso_cre__": "78910",
    "__fecha_a__o_de_la_implementacion__": "2024",
    "__nombre_del_representante_legal__": "Juan Pérez",
    "__nombre_de_la_razon_social__": "Empresa Ejemplo S.A."
}

# Buscar y reemplazar cada texto
for old_text, new_text in replacements.items():
    document.Replace(old_text, new_text, False, False)

# Guardar el documento resultante
document.SaveToFile("ReplaceAllInstances.docx", FileFormat.Docx2016)
document.Close()
