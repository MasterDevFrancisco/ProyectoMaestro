{{-- <div class="d-flex justify-content-end mb-3">
    <input type="text" class="form-control" placeholder="Buscar" style="width: 250px;">
</div> --}}
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                {{$thead}}
            </tr>
        </thead>
        <tbody>
            {{$slot}}
        </tbody>
    </table>
</div>
