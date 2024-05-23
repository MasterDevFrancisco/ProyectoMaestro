<div>
    <h1>Componente Inicio</h1>
    <x-card cardTitle="Titulo" cardFooter="Pie de pagina">
        <x-slot:cardTools>
            <a href="#" class="btn btn-primary" >Agregar</a>
        </x-slot>

        <x-table>
            <x-slot:thead>
                <th>1</th>
                <th>2</th>
            </x-slot>
            <th>A</th>
            <th>B</th>

        </x-table>

    </x-card>

</div>
